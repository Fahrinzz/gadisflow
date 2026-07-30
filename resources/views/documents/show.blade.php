@extends('layouts.app')

@section('title', $document->number)

@push('head')
    @include('documents._styles')
    <style>
        .paper { background:#fff; max-width:820px; margin:0 auto; padding:0; box-shadow:0 1px 6px rgba(0,0,0,.12); overflow:hidden; }
    </style>
@endpush

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <span class="badge doc-badge-{{ $document->type }}">{{ $document->type_label }}</span>
        <span class="h5 ms-2">{{ $document->number }}</span>
    </div>
    <div class="btn-group">
        <a href="{{ route('documents.edit', $document) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-pencil"></i> Edit</a>
        <a href="{{ route('documents.pdf', $document) }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="bi bi-eye"></i> View PDF</a>
        <a href="{{ route('documents.pdf', ['document' => $document, 'download' => 1]) }}" class="btn btn-danger btn-sm"><i class="bi bi-download"></i> Download PDF</a>

        @if($document->isQuotation())
            <form method="POST" action="{{ route('documents.convert', $document) }}" class="d-inline" onsubmit="return confirm('Generate an Invoice from this Quotation?')">
                @csrf <input type="hidden" name="to" value="invoice">
                <button class="btn btn-success btn-sm"><i class="bi bi-arrow-right-circle"></i> Convert to Invoice</button>
            </form>
        @endif
        @if($document->isInvoice())
            <form method="POST" action="{{ route('documents.convert', $document) }}" class="d-inline" onsubmit="return confirm('Generate a Delivery Order from this Invoice?')">
                @csrf <input type="hidden" name="to" value="delivery_order">
                <button class="btn btn-sm" style="background:#6f42c1;color:#fff"><i class="bi bi-truck"></i> Convert to Delivery Order</button>
            </form>
        @endif

        <form method="POST" action="{{ route('documents.destroy', $document) }}" class="d-inline" onsubmit="return confirm('Delete this document?')">
            @csrf @method('DELETE')
            <button class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
        </form>
    </div>
</div>

@if($document->parent)
    <div class="alert alert-info py-2">Generated from
        <a href="{{ route('documents.show', $document->parent) }}">{{ $document->parent->type_label }} {{ $document->parent->number }}</a>.
    </div>
@endif

<div class="paper">
    @include('documents._render')
</div>
@endsection
