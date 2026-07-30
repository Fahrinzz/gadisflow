<?php

namespace App\Http\Controllers;

use App\Models\CompanySetting;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocumentController extends Controller
{
    /**
     * Default status per document type.
     */
    private const DEFAULT_STATUS = [
        'quotation' => null,
        'invoice' => 'Unpaid',
        'delivery_order' => 'To deliver',
    ];

    public function index(Request $request)
    {
        $type = $request->query('type');

        $documents = Document::query()
            ->when(in_array($type, array_keys(Document::TYPES), true), fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('documents.index', compact('documents', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->query('type', 'quotation');
        abort_unless(in_array($type, array_keys(Document::TYPES), true), 404);

        $settings = CompanySetting::current();

        $document = new Document([
            'type' => $type,
            'number' => $this->suggestedNumber($type, $settings->next_number),
            'doc_date' => now()->toDateString(),
            'status' => self::DEFAULT_STATUS[$type],
            'terms' => $settings->default_terms,
        ]);

        return view('documents.form', [
            'document' => $document,
            'items' => [],
            'type' => $type,
        ]);
    }

    public function store(Request $request)
    {
        $type = $request->input('type');
        abort_unless(in_array($type, array_keys(Document::TYPES), true), 404);

        $data = $this->validateDocument($request, $type);

        $document = DB::transaction(function () use ($data, $type) {
            $document = Document::create([
                'type' => $type,
                'number' => $data['number'],
                'doc_date' => $data['doc_date'],
                'status' => $data['status'] ?? null,
                'attn_name' => $data['attn_name'] ?? null,
                'attn_address' => $data['attn_address'] ?? null,
                'project_details' => $data['project_details'] ?? null,
                'terms' => $data['terms'] ?? null,
                'payment' => $data['payment'] ?? 0,
            ]);

            $this->syncItems($document, $data['items'] ?? []);
            $document->recalculateTotals();
            $this->bumpRunningNumber($document->number);

            return $document;
        });

        if ($request->boolean('print')) {
            return redirect()->route('documents.pdf', ['document' => $document, 'download' => 1]);
        }

        return redirect()->route('documents.show', $document)
            ->with('status', Document::TYPES[$type].' created successfully.');
    }

    public function show(Document $document)
    {
        $document->load('items');
        $settings = CompanySetting::current();

        return view('documents.show', compact('document', 'settings'));
    }

    public function edit(Document $document)
    {
        $document->load('items');

        return view('documents.form', [
            'document' => $document,
            'items' => $document->items,
            'type' => $document->type,
        ]);
    }

    public function update(Request $request, Document $document)
    {
        $data = $this->validateDocument($request, $document->type);

        DB::transaction(function () use ($data, $document) {
            $document->update([
                'number' => $data['number'],
                'doc_date' => $data['doc_date'],
                'status' => $data['status'] ?? null,
                'attn_name' => $data['attn_name'] ?? null,
                'attn_address' => $data['attn_address'] ?? null,
                'project_details' => $data['project_details'] ?? null,
                'terms' => $data['terms'] ?? null,
                'payment' => $data['payment'] ?? 0,
            ]);

            $document->items()->delete();
            $this->syncItems($document, $data['items'] ?? []);
            $document->recalculateTotals();
        });

        if ($request->boolean('print')) {
            return redirect()->route('documents.pdf', ['document' => $document, 'download' => 1]);
        }

        return redirect()->route('documents.show', $document)
            ->with('status', 'Document updated successfully.');
    }

    public function destroy(Document $document)
    {
        $type = $document->type;
        $document->delete();

        return redirect()->route('documents.index', ['type' => $type])
            ->with('status', 'Document deleted successfully.');
    }

    /**
     * Printable A4 view. The browser's "Save as PDF" produces the download,
     * so no server-side PDF library is required.
     */
    public function pdf(Request $request, Document $document)
    {
        $document->load('items');
        $settings = CompanySetting::current();

        return view('documents.print', [
            'document' => $document,
            'settings' => $settings,
            'autoPrint' => $request->boolean('download'),
        ]);
    }

    /**
     * Auto-generate a new document from an existing one
     * (quotation -> invoice -> delivery order).
     */
    public function convert(Request $request, Document $document)
    {
        $to = $request->input('to');
        abort_unless(in_array($to, ['invoice', 'delivery_order'], true), 404);

        $settings = CompanySetting::current();

        $new = DB::transaction(function () use ($document, $to, $settings) {
            $new = Document::create([
                'type' => $to,
                // Re-use the numeric part of the source, just swap the prefix.
                'number' => Document::PREFIXES[$to].$this->numericPart($document->number),
                'doc_date' => now()->toDateString(),
                'status' => self::DEFAULT_STATUS[$to],
                'customer_id' => $document->customer_id,
                'attn_name' => $document->attn_name,
                'attn_address' => $document->attn_address,
                'project_details' => $document->project_details,
                'terms' => $settings->default_terms,
                'parent_id' => $document->id,
            ]);

            foreach ($document->items as $item) {
                $new->items()->create([
                    'position' => $item->position,
                    'description' => $item->description,
                    'qty' => $item->qty,
                    'unit_price' => $to === 'delivery_order' ? null : $item->unit_price,
                    'amount' => $to === 'delivery_order' ? null : $item->amount,
                ]);
            }

            $new->recalculateTotals();

            return $new;
        });

        return redirect()->route('documents.edit', $new)
            ->with('status', 'Generated from '.$document->type_label.'. Please review & save.');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    private function validateDocument(Request $request, string $type): array
    {
        $rules = [
            'number' => 'required|string|max:50',
            'doc_date' => 'required|date',
            'status' => 'nullable|string|max:50',
            'attn_name' => 'nullable|string|max:255',
            'attn_address' => 'nullable|string',
            'project_details' => 'nullable|string',
            'terms' => 'nullable|string',
            'payment' => 'nullable|numeric|min:0',
            'items' => 'array',
            'items.*.description' => 'required|string',
            'items.*.qty' => 'nullable|numeric',
            'items.*.unit_price' => 'nullable|numeric',
        ];

        return $request->validate($rules);
    }

    private function syncItems(Document $document, array $items): void
    {
        $position = 1;
        foreach ($items as $row) {
            if (blank($row['description'] ?? null)) {
                continue;
            }

            $qty = (float) ($row['qty'] ?? 1);
            $unitPrice = isset($row['unit_price']) && $row['unit_price'] !== ''
                ? (float) $row['unit_price']
                : null;

            $document->items()->create([
                'position' => $position++,
                'description' => $row['description'],
                'qty' => $qty,
                'unit_price' => $unitPrice,
                'amount' => $unitPrice === null ? null : round($qty * $unitPrice, 2),
            ]);
        }
    }

    private function suggestedNumber(string $type, int $next): string
    {
        return Document::PREFIXES[$type].$next;
    }

    private function numericPart(string $number): string
    {
        return preg_replace('/\D/', '', $number) ?: (string) CompanySetting::current()->next_number;
    }

    /**
     * If a document uses a number at or beyond the running counter,
     * advance the counter so the next document gets a fresh number.
     */
    private function bumpRunningNumber(string $number): void
    {
        $numeric = (int) preg_replace('/\D/', '', $number);
        $settings = CompanySetting::current();

        if ($numeric >= $settings->next_number) {
            $settings->update(['next_number' => $numeric + 1]);
        }
    }
}
