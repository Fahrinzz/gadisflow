<style>
    /* ---- GADIS KREATIF branded document theme ---- */
    .doc {
        --brand: #e79bb3;          /* rose / pink (quotation header) */
        --maroon: #6e2033;         /* dark wine (invoice / DO header) */
        --band: #FFC5D3;           /* matches the logo's pink backdrop */
        --line: #b96b83;           /* table borders                   */
        --yellow: #ffef3d;         /* quotation total row             */
        --red: #d10000;            /* invoice balance / unpaid        */
        font-family: "Segoe UI", Arial, Helvetica, sans-serif;
        color: #1a1a1a;
        font-size: 12px;
        line-height: 1.45;
        -webkit-print-color-adjust: exact;
        color-adjust: exact;                 /* legacy Firefox */
        print-color-adjust: exact;
    }
    /* Force every branded element (pink bands, table headers, yellow TOTAL)
       to keep its colour when printed — on Chrome, Edge, Firefox & Safari. */
    .doc, .doc * {
        -webkit-print-color-adjust: exact;
        color-adjust: exact;
        print-color-adjust: exact;
        box-sizing: border-box;
    }
    .doc .w100 { width: 100%; border-collapse: collapse; }

    /* Pink header & footer bands; white body in between */
    .doc .band { background: var(--band); padding: 20px 34px; }
    .doc .band-top { padding: 2px 34px; }
    .doc .body { padding: 22px 34px 26px; background: #fff; }
    .doc .mid { padding: 14px 34px; background: #fff; }   /* legacy, kept for safety */

    /* Header: logo + company + top-right details */
    .doc .head { width: 100%; border-collapse: collapse; }
    .doc .head > tbody > tr > td { vertical-align: middle; }
    .doc .logo-box {
        width: 195px;
        background: transparent;      /* sits on the pink band, like the brand mark */
        color: var(--maroon);
        text-align: left;
        vertical-align: middle;
        padding: 0;
        line-height: .92;
        font-style: italic;
    }
    .doc .logo-box img { max-width: 210px; height: auto; display: block; }
    .doc .logo-box .l1 { font-size: 32px; font-weight: 900; letter-spacing: .5px; }
    .doc .logo-box .l2 { font-size: 32px; font-weight: 900; letter-spacing: .5px; }
    .doc .logo-box .l3 { display: none; }
    .doc .company { padding-left: 16px; vertical-align: middle; letter-spacing: .6px; }
    .doc .company-name { font-size: 13px; font-weight: bold; letter-spacing: 1.2px; margin-bottom: 1px; }
    .doc .company div { font-size: 11px; line-height: 1.55; }

    /* Top-right block: project details + meta */
    .doc .topright { text-align: left; font-size: 11px; }
    .doc .topright .pd-label { font-weight: bold; }
    .doc .topright .pd-text { margin-bottom: 6px; }
    .doc .meta-k { font-weight: bold; }
    .doc .status-unpaid { color: var(--red); font-weight: normal; }

    .doc .doc-title { text-align: center; font-size: 26px; font-weight: 800; letter-spacing: 2px; color: #222; margin: 6px 0 10px; }

    .doc .attn { margin-top: 2px; }
    .doc .attn-label { font-weight: bold; }
    .doc .section-label { font-weight: bold; margin: 8px 0 3px; }
    .doc .project { margin-bottom: 6px; }

    /* Items table */
    .doc table.items { border-collapse: collapse; width: 100%; }
    .doc table.items th, .doc table.items td { border: 1px solid var(--line); padding: 6px 7px; vertical-align: top; }
    .doc table.items thead th { background: var(--brand); color: #fff; font-size: 13px; text-align: center; font-weight: bold; }
    /* Invoice & delivery order use the dark wine header */
    .doc--invoice table.items thead th,
    .doc--delivery_order table.items thead th { background: var(--maroon); }

    .doc .checkbox { display: inline-block; width: 16px; height: 16px; border: 1.5px solid #333; vertical-align: middle; }
    .doc .desc { white-space: normal; }
    .doc .text-left { text-align: left; }
    .doc .text-right { text-align: right; }
    .doc .text-center { text-align: center; }

    /* Quotation: yellow TOTAL row inside the table */
    .doc table.items tfoot td { font-weight: bold; }
    .doc table.items tfoot .total-row td { background: var(--yellow); font-size: 13px; }

    /* Invoice: plain totals block below the table */
    .doc table.totals { border-collapse: collapse; float: right; margin-top: 8px; }
    .doc table.totals td { padding: 2px 6px; font-size: 12px; font-weight: bold; }
    .doc table.totals .tk { text-align: right; }
    .doc table.totals .tv { text-align: right; width: 110px; }
    .doc table.totals .balance .tk,
    .doc table.totals .balance .tv { color: var(--red); font-size: 13px; }

    /* Terms & footer — match the branded T&C layout */
    .doc .band-bottom { padding: 24px 34px 30px; }
    .doc .section-label { font-weight: bold; margin: 0 0 8px; }
    .doc .terms { white-space: pre-line; font-size: 11px; line-height: 1.85; padding-left: 22px; color: #333; }
    .doc .bank { font-weight: bold; margin: 4px 0 0 40px; color: #222; }
    .doc .contact { margin-top: 20px; color: #8a7d81; }
    .doc .thanks { margin-top: 2px; color: #8a7d81; font-weight: normal; }
    .doc .clearfix::after { content: ""; display: table; clear: both; }
</style>
