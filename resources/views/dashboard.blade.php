@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="page-head">
    <h3>Dashboard</h3>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('documents.create', ['type' => 'quotation']) }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i> Quotation</a>
        <a href="{{ route('documents.create', ['type' => 'invoice']) }}" class="btn btn-success btn-sm"><i class="bi bi-plus-lg"></i> Invoice</a>
        <a href="{{ route('documents.create', ['type' => 'delivery_order']) }}" class="btn btn-purple btn-sm"><i class="bi bi-plus-lg"></i> Delivery Order</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="stat">
            <div class="ic ic-q"><i class="bi bi-file-earmark-text"></i></div>
            <div><div class="lbl">Quotation</div><div class="val">{{ $counts['quotation'] }}</div></div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="stat">
            <div class="ic ic-i"><i class="bi bi-receipt"></i></div>
            <div><div class="lbl">Invoice</div><div class="val">{{ $counts['invoice'] }}</div></div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="stat">
            <div class="ic ic-d"><i class="bi bi-truck"></i></div>
            <div><div class="lbl">Delivery Order</div><div class="val">{{ $counts['delivery_order'] }}</div></div>
        </div></div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card h-100"><div class="stat">
            <div class="ic ic-u"><i class="bi bi-cash-coin"></i></div>
            <div><div class="lbl">Unpaid (RM)</div><div class="val">{{ number_format($unpaidTotal, 2) }}</div></div>
        </div></div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex align-items-center"><i class="bi bi-clock-history me-2"></i> Recent Documents</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle table-stack">
            <thead>
                <tr><th>No.</th><th>Type</th><th>Recipient</th><th>Date</th><th>Status</th><th class="text-end">Amount (RM)</th><th></th></tr>
            </thead>
            <tbody>
                @forelse ($recent as $doc)
                    <tr>
                        <td data-label="No."><a href="{{ route('documents.show', $doc) }}" class="fw-semibold text-decoration-none">{{ $doc->number }}</a></td>
                        <td data-label="Type"><span class="badge doc-badge-{{ $doc->type }}">{{ $doc->type_label }}</span></td>
                        <td data-label="Recipient">{{ $doc->attn_name }}</td>
                        <td data-label="Date">{{ $doc->doc_date->format('d/m/Y') }}</td>
                        <td data-label="Status">{{ $doc->status ?? '—' }}</td>
                        <td data-label="Amount (RM)" class="text-end">{{ $doc->isDeliveryOrder() ? '—' : number_format($doc->subtotal, 2) }}</td>
                        <td data-label="" class="text-end text-nowrap stack-actions">
                            <a href="{{ route('documents.edit', $doc) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('documents.pdf', $doc) }}" class="btn btn-sm btn-outline-danger"><i class="bi bi-file-pdf"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-3 d-block mb-2 opacity-50"></i>
                        No documents yet. Click a button above to start.
                    </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
