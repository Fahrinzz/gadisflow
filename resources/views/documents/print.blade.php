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

        /* Let tall item rows split across pages so the content flows continuously
           (no gaps) — this keeps the on-screen height equal to the printed height,
           which the footer script relies on. */
        .sheet table.items { page-break-inside: auto; break-inside: auto; }
        .sheet table.items tr,
        .sheet table.items td { page-break-inside: auto; break-inside: auto; }
        .sheet table.items tfoot tr { page-break-inside: avoid; break-inside: avoid; }
        .sheet table.items thead { display: table-row-group; }
        .sheet table.items tfoot { display: table-row-group; }

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
            .doc .band-bottom { page-break-inside: avoid; break-inside: avoid; }
            /* The Terms footer lives in the page wrapper's <tfoot>; every browser
               places a table-footer-group at the bottom of each printed page. */
            .doc .pagewrap tfoot { display: table-footer-group; }
            /* Single-page docs (JS-marked): pin it to the very bottom instead. */
            body.one-page .doc .band-bottom { position: fixed; left: 0; right: 0; bottom: 0; }
            /* margin:0 removes the browser's URL / page-number header & footer. */
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
    <script>
    window.addEventListener('load', function () {
        // If everything fits on one page, pin the footer to the very bottom.
        // Otherwise the <tfoot> keeps it at the bottom of every page (all browsers).
        try {
            var wrap = document.querySelector('.pagewrap');
            if (wrap && wrap.getBoundingClientRect().height <= 1120) {
                document.body.classList.add('one-page');
            }
        } catch (e) {}
        @if($autoPrint) setTimeout(function () { window.print(); }, 400); @endif
    });
    </script>
</body>
</html>
