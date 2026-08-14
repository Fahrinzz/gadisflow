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
            /* Single-page docs (marked by JS below): stick the Terms footer to the
               very bottom so it never floats. Multi-page docs keep the footer in
               normal flow (after the content) so it never overlaps the item table. */
            body.stick-footer .doc .band-bottom { position: fixed; left: 0; right: 0; bottom: 0; }
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
        // If the whole document (header + body + footer) fits on one A4 page,
        // stick the footer to the page bottom. Otherwise leave it flowing so it
        // never overlaps the item table on multi-page prints.
        try {
            var top = document.querySelector('.band-top');
            var body = document.querySelector('.body');
            var footer = document.querySelector('.band-bottom');
            var pagePx = 1122; // A4 height at 96dpi with margin:0
            var used = (top ? top.offsetHeight : 0) + (body ? body.offsetHeight : 0) + (footer ? footer.offsetHeight : 0);
            if (used <= pagePx) document.body.classList.add('stick-footer');
        } catch (e) {}
        @if($autoPrint) setTimeout(function () { window.print(); }, 350); @endif
    });
    </script>
</body>
</html>
