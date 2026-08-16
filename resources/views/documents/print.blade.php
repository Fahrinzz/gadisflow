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
    // Place the Terms footer ONCE, at the bottom of the LAST page.
    // Chrome does NOT split tall table rows across pages — it moves a whole row
    // to the next page, leaving a gap. So we simulate that pagination to find the
    // footer's true printed position, then add a top margin to drop it to the
    // bottom of its page.
    function placeFooter() {
        try {
            var sheet = document.querySelector('.sheet');
            var footer = document.querySelector('.band-bottom');
            if (!sheet || !footer) return;
            var pageH = 1122; // A4 @96dpi, @page margin:0
            footer.style.marginTop = '0px';
            var sTop = sheet.getBoundingClientRect().top;

            // Simulate the page-break gaps caused by whole rows that don't fit.
            var gap = 0;
            var rows = document.querySelectorAll('.items tbody tr, .items tfoot tr');
            rows.forEach(function (r) {
                var rect = r.getBoundingClientRect();
                var top = (rect.top - sTop) + gap;
                var h = rect.height;
                if (h > 0 && h <= pageH) {
                    if (Math.floor(top / pageH) !== Math.floor((top + h - 1) / pageH)) {
                        gap += (Math.floor((top + h - 1) / pageH) * pageH) - top;
                    }
                }
            });

            var fr = footer.getBoundingClientRect();
            var naturalBottom = (fr.top - sTop) + fr.height + gap; // true printed bottom
            var target = Math.ceil((naturalBottom - 1) / pageH) * pageH;
            var push = target - naturalBottom;
            if (push > 2 && push < pageH - 2) footer.style.marginTop = Math.round(push) + 'px';
        } catch (e) {}
    }
    window.addEventListener('load', function () {
        placeFooter();
        @if($autoPrint) setTimeout(function () { window.print(); }, 450); @endif
    });
    </script>
</body>
</html>
