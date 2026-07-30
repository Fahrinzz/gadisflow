@extends('layouts.app')

@php
    use App\Models\Document;
    use App\Models\CompanySetting;
    $settings = CompanySetting::current();
    $typeLabel = Document::TYPES[$type];
    $isNew = ! $document->exists;
    $showPrice = $type !== 'delivery_order';
    $title = strtoupper($typeLabel);

    // Rows to prefill: old input (after validation error) > existing items > one empty row
    $rows = old('items');
    if ($rows === null) {
        $rows = collect($items)->map(fn ($i) => [
            'description' => $i->description,
            'qty' => rtrim(rtrim(number_format((float) $i->qty, 2, '.', ''), '0'), '.'),
            'unit_price' => $i->unit_price,
        ])->values()->all();
    }
    if (empty($rows)) {
        $rows = [['description' => '', 'qty' => 1, 'unit_price' => '']];
    }

    $editColspan = $showPrice ? 4 : 3;                 // cells before the total value
    $statusValue = old('status', $document->status);
@endphp

@section('title', ($isNew ? 'Create ' : 'Edit ').$typeLabel)

@push('head')
    @include('documents._styles')
    <style>
        .edit-wrap { background:#eef0f2; padding:0 0 40px; }
        .edit-toolbar { position:sticky; top:0; z-index:20; background:#fff; border-bottom:1px solid #e2e5e8;
            padding:12px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px; }
        .edit-toolbar .hint { color:#6c757d; font-size:.85rem; }
        .sheet { background:#fff; max-width:900px; margin:20px auto; box-shadow:0 1px 10px rgba(0,0,0,.15); overflow:hidden; }

        /* Inline editable fields blend into the document */
        .docedit input, .docedit textarea, .docedit select {
            font:inherit; color:inherit; width:100%; box-sizing:border-box;
            border:1px dashed #cf9fb0; background:rgba(255,255,255,.55);
            border-radius:3px; padding:2px 5px;
        }
        .docedit textarea { resize:vertical; overflow:hidden; }
        .docedit input:hover, .docedit textarea:hover, .docedit select:hover { background:#fff; }
        .docedit input:focus, .docedit textarea:focus, .docedit select:focus { outline:none; border-color:var(--maroon); background:#fff; }
        .docedit .topright input, .docedit .topright select { display:inline-block; width:auto; min-width:120px; }
        .docedit .price, .docedit .qty { text-align:right; }
        .docedit .amount { font-weight:bold; }
        .docedit table.items td.actcell { border:none; background:#fff; width:34px; text-align:center; vertical-align:middle; }
        .docedit .rm { border:0; background:#f8d7da; color:#b02a37; border-radius:50%; width:24px; height:24px; cursor:pointer; line-height:1; }
        .docedit .addrow { display:inline-block; margin-top:8px; color:var(--maroon); font-weight:bold; cursor:pointer; text-decoration:none; }
        .docedit .addrow:hover { text-decoration:underline; }
        .docedit .plabel { font-weight:bold; font-size:11px; }
    </style>
@endpush

@section('content')
<form method="POST" action="{{ $isNew ? route('documents.store') : route('documents.update', $document) }}" id="docForm">
    @csrf
    @unless($isNew) @method('PUT') @endunless
    <input type="hidden" name="type" value="{{ $type }}">

    <div class="edit-toolbar">
        <div>
            <strong>{{ $isNew ? 'Create' : 'Edit' }} {{ $typeLabel }}</strong>
            <span class="hint d-none d-md-inline">— fill in the document below, then Save / Print</span>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ $isNew ? route('documents.index', ['type'=>$type]) : route('documents.show', $document) }}" class="btn btn-light btn-sm">Cancel</a>
            <button type="submit" class="btn btn-dark btn-sm"><i class="bi bi-check-lg"></i> Save</button>
            <button type="submit" name="print" value="1" class="btn btn-success btn-sm"><i class="bi bi-printer"></i> Save & Print</button>
        </div>
    </div>

    <div class="edit-wrap">
        <div class="sheet">
            <div class="doc doc--{{ $type }} docedit">

                {{-- ===== TOP BAND ===== --}}
                <div class="band band-top">
                    {{-- Logo + company (branded letterhead) --}}
                    <table class="head">
                        <tr>
                            <td class="logo-box" style="width:180px;">
                                @include('documents._logo')
                            </td>
                            <td class="company">
                                <div class="company-name">{{ $settings->name }}</div>
                                @if($settings->reg_no)<div>{{ $settings->reg_no }}</div>@endif
                                @if($settings->address)<div>{!! nl2br(e($settings->address)) !!}</div>@endif
                                @if($settings->email)<div>{{ $settings->email }}</div>@endif
                            </td>
                        </tr>
                    </table>
                </div>

                {{-- ===== BODY (white) ===== --}}
                <div class="body clearfix">
                    <div class="doc-title">{{ $title }}</div>

                    {{-- Attn (left) + project details / meta (right) --}}
                    <table class="w100" style="margin-bottom:10px;">
                        <tr>
                            <td style="width:55%; vertical-align:top;">
                                <strong>Attn :</strong>
                                <div class="attn">
                                    <input type="text" name="attn_name" value="{{ old('attn_name', $document->attn_name) }}" placeholder="Recipient name / department">
                                    <textarea name="attn_address" rows="3" placeholder="Recipient address" style="margin-top:4px;">{{ old('attn_address', $document->attn_address) }}</textarea>
                                </div>
                            </td>
                            <td class="topright" style="width:45%; vertical-align:top;">
                                <div class="plabel">PROJECT DETAILS:</div>
                                <textarea name="project_details" rows="2" placeholder="Project details...">{{ old('project_details', $document->project_details) }}</textarea>
                                <div style="margin-top:6px;"><span class="meta-k">Number</span> :
                                    <input type="text" name="number" value="{{ old('number', $document->number) }}" required></div>
                                <div style="margin-top:3px;"><span class="meta-k">Date</span> :
                                    <input type="date" name="doc_date" value="{{ old('doc_date', optional($document->doc_date)->format('Y-m-d') ?? $document->doc_date) }}" required></div>
                                @if($type !== 'quotation')
                                <div style="margin-top:3px;"><span class="meta-k">Status</span> :
                                    <select name="status">
                                        @foreach(($type === 'invoice' ? ['Unpaid','Partial','Paid'] : ['To deliver','Delivered']) as $s)
                                            <option value="{{ $s }}" @selected($statusValue === $s)>{{ $s }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <table class="items" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width:34px;">No.</th>
                                <th class="text-left">Items</th>
                                @if($showPrice)
                                    <th style="width:100px;">Price per unit (RM)</th>
                                    <th style="width:60px;">Qty</th>
                                    <th style="width:110px;">Total Price (RM)</th>
                                @else
                                    <th style="width:70px;">Qty</th>
                                    <th style="width:80px;">Check</th>
                                @endif
                                <th class="actcell"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            @foreach($rows as $idx => $row)
                            <tr class="item-row">
                                <td class="text-center row-no">{{ $idx + 1 }}</td>
                                <td><textarea name="items[{{ $idx }}][description]" rows="3" class="desc" placeholder="Service ...&#10;• detail 1&#10;• detail 2">{{ $row['description'] }}</textarea></td>
                                @if($showPrice)
                                    <td><input type="number" step="0.01" name="items[{{ $idx }}][unit_price]" class="price" value="{{ $row['unit_price'] }}"></td>
                                    <td><input type="number" step="0.01" name="items[{{ $idx }}][qty]" class="qty" value="{{ $row['qty'] }}"></td>
                                    <td class="text-right amount">0.00</td>
                                @else
                                    <td><input type="number" step="0.01" name="items[{{ $idx }}][qty]" class="qty" value="{{ $row['qty'] }}"></td>
                                    <td class="text-center"><span class="checkbox"></span></td>
                                @endif
                                <td class="actcell"><button type="button" class="rm" title="Remove">&times;</button></td>
                            </tr>
                            @endforeach
                        </tbody>
                        @if($type === 'quotation')
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="{{ $editColspan }}" class="text-right">TOTAL</td>
                                <td class="text-right" id="grandTotal">0.00</td>
                                <td class="actcell"></td>
                            </tr>
                        </tfoot>
                        @endif
                        @if($type === 'delivery_order')
                        <tfoot>
                            <tr class="do-total">
                                <td style="border:none;"></td>
                                <td class="text-right" style="border:none; font-weight:bold;">Total :</td>
                                <td class="text-center" style="border:none; font-weight:bold;" id="qtyTotal">0</td>
                                <td class="actcell"></td>
                            </tr>
                        </tfoot>
                        @endif
                    </table>

                    <a class="addrow" id="addRow">+ Add item</a>

                    @if($type === 'invoice')
                    <table class="totals">
                        <tr><td class="tk">Total (RM):</td><td class="tv" id="grandTotal">0.00</td></tr>
                        <tr><td class="tk">Payment:</td><td class="tv">
                            <input type="number" step="0.01" name="payment" id="paymentInput" value="{{ old('payment', $document->payment ?? 0) }}" style="width:100px;text-align:right;">
                        </td></tr>
                        <tr class="balance"><td class="tk">Balance:</td><td class="tv" id="balanceCell">0.00</td></tr>
                    </table>
                    @endif

                </div>

                {{-- ===== FOOTER BAND (pink): terms ===== --}}
                <div class="band band-bottom">
                    <div class="section-label">TERMS &amp; CONDITIONS:</div>
                    <textarea name="terms" rows="3">{{ old('terms', $document->terms) }}</textarea>
                    @if($settings->bank_info)<div class="bank">{{ $settings->bank_info }}</div>@endif
                    @if($settings->phone)<div class="contact">For any inquiries, kindly contact {{ $settings->phone }}</div>@endif
                    <div class="thanks">Thank you.</div>
                </div>

            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
(function () {
    const showPrice = @json($showPrice);
    const type = @json($type);
    const body = document.getElementById('itemsBody');
    let counter = body.querySelectorAll('.item-row').length;

    const fmt = n => n.toLocaleString('en-MY', {minimumFractionDigits: 2, maximumFractionDigits: 2});

    function autosize(t) { t.style.height = 'auto'; t.style.height = (t.scrollHeight + 2) + 'px'; }

    function recalc() {
        let grand = 0, qtySum = 0;
        body.querySelectorAll('.item-row').forEach((row, i) => {
            row.querySelector('.row-no').textContent = i + 1;
            const qty = parseFloat(row.querySelector('.qty')?.value) || 0;
            qtySum += qty;
            if (showPrice) {
                const price = parseFloat(row.querySelector('.price')?.value) || 0;
                const amt = price * qty;
                row.querySelector('.amount').textContent = fmt(amt);
                grand += amt;
            }
        });
        document.querySelectorAll('#grandTotal').forEach(el => el.textContent = fmt(grand));
        const qt = document.getElementById('qtyTotal');
        if (qt) qt.textContent = (Math.round(qtySum * 100) / 100).toString();
        const bal = document.getElementById('balanceCell');
        if (bal) {
            const pay = parseFloat(document.getElementById('paymentInput')?.value) || 0;
            bal.textContent = fmt(grand - pay);
        }
    }

    function addRow() {
        const idx = counter++;
        const tr = document.createElement('tr');
        tr.className = 'item-row';
        let html = '<td class="text-center row-no"></td>' +
            '<td><textarea name="items[' + idx + '][description]" rows="3" class="desc"></textarea></td>';
        if (showPrice) {
            html += '<td><input type="number" step="0.01" name="items[' + idx + '][unit_price]" class="price"></td>' +
                    '<td><input type="number" step="0.01" name="items[' + idx + '][qty]" class="qty" value="1"></td>' +
                    '<td class="text-right amount">0.00</td>';
        } else {
            html += '<td><input type="number" step="0.01" name="items[' + idx + '][qty]" class="qty" value="1"></td>' +
                    '<td class="text-center"><span class="checkbox"></span></td>';
        }
        html += '<td class="actcell"><button type="button" class="rm" title="Remove">&times;</button></td>';
        tr.innerHTML = html;
        body.appendChild(tr);
        tr.querySelectorAll('textarea').forEach(autosize);
        recalc();
    }

    document.getElementById('addRow').addEventListener('click', addRow);

    body.addEventListener('click', function (e) {
        if (e.target.closest('.rm')) {
            if (body.querySelectorAll('.item-row').length > 1) {
                e.target.closest('.item-row').remove();
            } else {
                e.target.closest('.item-row').querySelectorAll('input, textarea').forEach(el => el.value = '');
            }
            recalc();
        }
    });

    body.addEventListener('input', function (e) {
        if (e.target.classList.contains('price') || e.target.classList.contains('qty')) recalc();
        if (e.target.tagName === 'TEXTAREA') autosize(e.target);
    });

    const pay = document.getElementById('paymentInput');
    if (pay) pay.addEventListener('input', recalc);

    document.querySelectorAll('.docedit textarea').forEach(autosize);
    recalc();
})();
</script>
@endpush
