<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $document->number }}</title>
    @include('documents._styles')
    <style>
        html, body { margin: 0; background: #e9ecef;
            -webkit-print-color-adjust: exact; color-adjust: exact; print-color-adjust: exact;
            -webkit-text-size-adjust: 100%; text-size-adjust: 100%; }
        * { box-sizing: border-box; }
        .sheet { background: #fff; width: 210mm; min-height: 297mm; margin: 16px auto; padding: 0;
            box-sizing: border-box; box-shadow: 0 1px 8px rgba(0,0,0,.2); }

        /* Clean pagination for long item lists */
        .sheet table.items { page-break-inside: auto; }
        .sheet table.items tr { page-break-inside: auto; }         /* let a tall item flow across pages so page 1 fills up */
        .sheet table.items tfoot tr { page-break-inside: avoid; }  /* but keep the TOTAL row intact */
        .sheet table.items thead { display: table-header-group; }  /* repeat column headers each page */
        .sheet table.items tfoot { display: table-row-group; }     /* TOTAL row shows once, at the end */

        .toolbar { position: sticky; top: 0; background: #212529; color: #fff; padding: 10px 16px; text-align: center; }
        .toolbar button, .toolbar a { font: inherit; padding: 6px 14px; border: 0; border-radius: 5px; cursor: pointer; text-decoration: none; margin: 0 4px; }
        .btn-print { background: #dc3545; color: #fff; }
        .btn-back { background: #6c757d; color: #fff; }

        @media print {
            /* Force ALL backgrounds/colours to print on every browser, even
               when the user hasn't ticked "Background graphics". */
            * { -webkit-print-color-adjust: exact !important; color-adjust: exact !important; print-color-adjust: exact !important; }
            html, body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }

            /* Pin the Terms footer to the very bottom of EVERY printed page.
               @page reserves 46mm at the bottom so the item table never runs
               under the footer; the footer (bottom:0) fills that reserved band. */
            .doc .band-bottom { position: fixed; left: 0; right: 0; bottom: 0; }
            /* margin:0 on top/sides removes the browser's URL header; the bottom
               reserve holds the footer. */
            @page { size: A4; margin: 0 0 46mm 0; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
        <a class="btn-back" href="{{ route('documents.show', $document) }}">← Back</a>
    </div>
    <div class="sheet">
        @include('documents._render')
    </div>
    @if($autoPrint)
    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 350));</script>
    @endif
</body>
</html>
