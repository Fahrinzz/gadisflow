@extends('layouts.app')

@php
    $typeLabel = $type ? (\App\Models\Document::TYPES[$type] ?? 'Document') : 'All Documents';
@endphp

@section('title', $typeLabel)

@section('content')
<div class="page-head">
    <h3>{{ $typeLabel }}</h3>
    @if ($type)
        <a href="{{ route('documents.create', ['type' => $type]) }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> New {{ $typeLabel }}</a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle table-stack">
            <thead class="table-light">
                <tr>
                    <th>No.</th>
                    @unless($type)<th>Type</th>@endunless
                    <th>Recipient</th><th>Date</th><th>Status</th><th class="text-end">Amount (RM)</th><th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($documents as $doc)
                    <tr>
                        <td data-label="No."><a href="{{ route('documents.show', $doc) }}">{{ $doc->number }}</a></td>
                        @unless($type)<td data-label="Type"><span class="badge doc-badge-{{ $doc->type }}">{{ $doc->type_label }}</span></td>@endunless
                        <td data-label="Recipient">{{ $doc->attn_name }}</td>
                        <td data-label="Date">{{ $doc->doc_date->format('d/m/Y') }}</td>
                        <td data-label="Status">{{ $doc->status ?? '—' }}</td>
                        <td data-label="Amount (RM)" class="text-end">{{ $doc->isDeliveryOrder() ? '—' : number_format($doc->subtotal, 2) }}</td>
                        <td data-label="" class="text-end text-nowrap stack-actions">
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-outline-secondary" title="View"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('documents.edit', $doc) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <a href="{{ route('documents.pdf', ['document' => $doc, 'download' => 1]) }}" class="btn btn-sm btn-outline-danger" title="Download PDF"><i class="bi bi-download"></i></a>
                        </td>
                    </tr>
                @empty
                    <tr><td data-label="" colspan="7" class="text-center text-muted py-4">No records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $documents->links() }}</div>
@endsection
