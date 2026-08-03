<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $document->number }}</title>
    @include('documents._styles')
    <style>
        html, body { margin: 0; background: #e9ecef; }
        .sheet { background: #fff; width: 210mm; min-height: 297mm; margin: 16px auto; padding: 0;
            box-sizing: border-box; box-shadow: 0 1px 8px rgba(0,0,0,.2); }

        /* Fill most of the page so the footer sits near the bottom on short
           documents, while long item lists still flow onto more pages. */
        .sheet .doc .body { min-height: 172mm; }

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
            html, body { background: #fff; }
            .toolbar { display: none; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
            /* margin:0 removes the browser's URL / page-number header & footer */
            @page { size: A4; margin: 0; }
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
