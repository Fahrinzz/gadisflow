{{-- Shared, branded document body (GADIS KREATIF theme). --}}
@php
    $showPrice = ! $document->isDeliveryOrder();
    $title = strtoupper($document->type_label);
    $colspan = $showPrice ? 4 : 3;
@endphp
<div class="doc doc--{{ $document->type }}">

    {{-- ===== HEADER BAND (pink): logo + company only ===== --}}
    <div class="band band-top">
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
                    <span class="attn-label">Attn :</span>
                    <div class="attn">
                        @if($document->attn_name)<div>{{ $document->attn_name }}</div>@endif
                        @if($document->attn_address)<div>{!! nl2br(e($document->attn_address)) !!}</div>@endif
                    </div>
                </td>
                <td class="topright" style="width:45%; vertical-align:top;">
                    @if($document->project_details)
                        <div class="pd-label">PROJECT DETAILS:</div>
                        <div class="pd-text">{!! nl2br(e($document->project_details)) !!}</div>
                    @endif
                    <div><span class="meta-k">Number</span> : {{ $document->number }}</div>
                    <div><span class="meta-k">Date</span> : {{ $document->doc_date->format('d F Y') }}</div>
                    @if($document->status)
                        <div>
                            <span class="meta-k">Status</span> :
                            <span class="{{ $document->isInvoice() ? 'status-unpaid' : '' }}">{{ $document->status }}</span>
                        </div>
                    @endif
                </td>
            </tr>
        </table>

        {{-- Items --}}
        <table class="items">
            <thead>
                <tr>
                    <th style="width:34px;">No.</th>
                    <th class="text-left">Items</th>
                    @if($showPrice)
                        <th style="width:96px;">Price per unit (RM)</th>
                        <th style="width:48px;">Qty</th>
                        <th style="width:96px;">Total Price (RM)</th>
                    @else
                        <th style="width:60px;">Qty</th>
                        <th style="width:70px;">Check</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($document->items as $item)
                <tr>
                    <td class="text-center">{{ $item->position }}</td>
                    <td class="text-left desc">{!! nl2br(e($item->description)) !!}</td>
                    @if($showPrice)
                        <td class="text-right">{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="text-center">{{ rtrim(rtrim(number_format((float) $item->qty, 2), '0'), '.') }}</td>
                        <td class="text-right">{{ number_format((float) $item->amount, 2) }}</td>
                    @else
                        <td class="text-center">{{ rtrim(rtrim(number_format((float) $item->qty, 2), '0'), '.') }}</td>
                        <td class="text-center"><span class="checkbox"></span></td>
                    @endif
                </tr>
                @empty
                <tr><td colspan="{{ $showPrice ? 5 : 4 }}" class="text-center" style="padding:14px;">No items.</td></tr>
                @endforelse
            </tbody>
            {{-- Quotation: yellow TOTAL row inside the table --}}
            @if($document->isQuotation())
            <tfoot>
                <tr class="total-row">
                    <td colspan="{{ $colspan }}" class="text-right">TOTAL</td>
                    <td class="text-right">{{ number_format((float) $document->subtotal, 2) }}</td>
                </tr>
            </tfoot>
            @endif
            {{-- Delivery Order: total quantity row --}}
            @if($document->isDeliveryOrder())
            <tfoot>
                <tr class="do-total">
                    <td style="border:none;"></td>
                    <td class="text-right" style="border:none; font-weight:bold;">Total :</td>
                    <td class="text-center" style="border:none; font-weight:bold;">{{ rtrim(rtrim(number_format((float) $document->items->sum('qty'), 2), '0'), '.') }}</td>
                    <td style="border:none;"></td>
                </tr>
            </tfoot>
            @endif
        </table>

        {{-- Invoice: plain totals block below the table --}}
        @if($document->isInvoice())
        <table class="totals">
            <tr><td class="tk">Total (RM):</td><td class="tv">{{ number_format((float) $document->subtotal, 2) }}</td></tr>
            <tr><td class="tk">Payment:</td><td class="tv">{{ number_format((float) $document->payment, 2) }}</td></tr>
            <tr class="balance"><td class="tk">Balance:</td><td class="tv">{{ number_format((float) $document->balance, 2) }}</td></tr>
        </table>
        @endif

    </div>

    {{-- ===== FOOTER BAND (pink): terms ===== --}}
    <div class="band band-bottom">
        @if($document->terms)
        <div class="section-label">TERMS &amp; CONDITIONS:</div>
        <div class="terms">{!! nl2br(e($document->terms)) !!}</div>
        @if($settings->bank_info)
        <div class="bank">{{ $settings->bank_info }}</div>
        @endif
        @endif

        @if($settings->phone)
        <div class="contact">For any inquiries, kindly contact {{ $settings->phone }}</div>
        @endif
        <div class="thanks">Thank you.</div>
    </div>
</div>
