<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        /*
         * Letterhead layout (A4 = 210 x 297 mm)
         * Page margins reserve space for the letterhead header/footer.
         * Fixed elements are positioned relative to the content area, so:
         *   left = X - 15mm   |   top = Y - 58mm   (X, Y = position on the page)
         */
        @page {
            size: A4;
            margin: 58mm 15mm 30mm 15mm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.5;
            margin: 0;
        }

        .text-dark {
            color: #14396b;
        }

        .text-muted {
            color: #666;
        }

        .text-green {
            color: #1f9254;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .text-sm {
            font-size: 10px;
        }

        .text-xs {
            font-size: 9px;
        }

        .w-100 {
            width: 100%;
        }

        .mb-20 {
            margin-bottom: 20px;
        }

        .mb-5 {
            margin-bottom: 5px;
        }

        table {
            border-collapse: collapse;
        }

        /* ---------- LETTERHEAD (fixed on every page) ---------- */
        .lh-topbar {
            position: fixed;
            top: -58mm;
            left: -15mm;
            width: 210mm;
            height: 4.5mm;
            background-color: #14396b;
        }

        .lh-logo {
            position: fixed;
            top: -47mm;
            left: 0;
            width: 38mm;
            height: 38mm;
        }

        .lh-company {
            position: fixed;
            top: -46mm;
            right: 0;
            width: 100mm;
            text-align: right;
        }

        .lh-company .name {
            font-size: 26px;
            font-weight: bold;
            color: #14396b;
            line-height: 1.1;
        }

        .lh-company .tagline {
            font-size: 10px;
            color: #1f9254;
            margin: 3px 0 8px 0;
        }

        .lh-company .info {
            font-size: 9.5px;
            color: #444;
            line-height: 1.6;
        }

        .lh-line-green {
            position: fixed;
            top: -7mm;
            left: 0;
            width: 180mm;
            border-top: 0.9mm solid #1f9254;
        }

        .lh-line-navy {
            position: fixed;
            top: -5.6mm;
            left: 0;
            width: 180mm;
            border-top: 0.2mm solid #14396b;
        }

        .lh-watermark {
            position: fixed;
            top: 45mm;
            left: 34mm;
            width: 116mm;
            opacity: 0.07;
        }

        .lh-signature {
            position: fixed;
            top: 152mm;
            left: 140mm;
            height: 26mm;
        }

        .lh-stamp {
            position: fixed;
            top: 178mm;
            left: 134mm;
            width: 42mm;
            height: 42mm;
        }

        .lh-footer-line {
            position: fixed;
            top: 220mm;
            left: 0;
            width: 180mm;
            border-top: 0.6mm solid #1f9254;
        }

        .lh-footer {
            position: fixed;
            top: 222mm;
            left: 0;
            width: 180mm;
            text-align: center;
            font-size: 9px;
            color: #666;
            line-height: 1.6;
        }

        /* ---------- INVOICE CONTENT ---------- */
        .invoice-title {
            font-size: 28px;
            font-weight: bold;
            color: #14396b;
            letter-spacing: 2px;
            margin: 0;
            line-height: 1;
        }

        .items-table th {
            background-color: #14396b;
            color: #ffffff;
            padding: 8px 10px;
            font-size: 10px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
        }

        .section-label {
            font-size: 9px;
            font-weight: bold;
            color: #1f9254;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>

    @php
    $companyEmail = 'admin@us2pk.com';
    $hasGd = extension_loaded('gd');
    $img = function ($name) use ($hasGd) {
    $path = public_path('assets/' . $name);
    return ($hasGd && is_file($path))
    ? 'data:image/png;base64,' . base64_encode(file_get_contents($path))
    : '';
    };
    $logo = $img('logo.png');
    $signature = $img('signature.png');
    $stamp = $img('stamp.png');
    @endphp

    <!-- ===================== LETTERHEAD ===================== -->
    <div class="lh-topbar"></div>

    @if($logo)
    <img class="lh-watermark" src="{{ $logo }}" alt="">
    <img class="lh-logo" src="{{ $logo }}" alt="US2PK">
    @endif

    @unless($logo)
    <div style="position: fixed; top: -40mm; left: 0; font-size: 30px; font-weight: bold; color: #14396b;">US2<span style="color:#1f9254;">PK</span></div>
    @endunless

    <div class="lh-company">
        <div class="name">US2PK</div>
        <div class="tagline">BRINGING OPPORTUNITIES HOME</div>
        <div class="info">
            Office #407, Garden Tower, Babar Block<br>
            Garden Town, Lahore, Pakistan<br>
            +92 301 5233018 &nbsp;|&nbsp; {{ $companyEmail }}
        </div>
    </div>

    <div class="lh-line-green"></div>
    <div class="lh-line-navy"></div>

    @if($signature)
    <img class="lh-signature" src="{{ $signature }}" alt="">
    @endif
    @if($stamp)
    <img class="lh-stamp" src="{{ $stamp }}" alt="">
    @endif

    <div class="lh-footer-line"></div>
    <div class="lh-footer">
        US2PK &nbsp;|&nbsp; USA-to-Pakistan Shopping &amp; Shipping<br>
        <strong style="color: #14396b;">www.us2pk.com</strong>
    </div>

    <!-- ===================== INVOICE BODY ===================== -->

    <!-- TITLE -->
    <table class="w-100 mb-20">
        <tr>
            <td class="text-left" style="vertical-align: bottom;">
                <h1 class="invoice-title">INVOICE</h1>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <div class="text-sm fw-bold text-dark">#{{ $invoice->invoice_no }}</div>
                <div class="text-xs text-muted">Date: {{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</div>
            </td>
        </tr>
    </table>

    <!-- BILLING INFO (3 Columns) -->
    <table class="w-100 mb-20">
        <tr>
            <td width="33%" style="vertical-align: top;">
                <div class="section-label">Billed To</div>
                <div class="fw-bold text-dark" style="font-size: 12px;">{{ $invoice->shipment?->user?->name ?? 'N/A' }}</div>
                <div class="text-xs text-muted">{{ $invoice->shipment?->user?->email ?? 'N/A' }}</div>
                <div class="text-xs text-muted">{{ $invoice->shipment?->user?->city?->city_name ?? 'N/A' }}</div>
            </td>
            <td width="33%" style="vertical-align: top;">
                <div class="section-label">Shipment Details</div>
                <div class="fw-bold text-dark" style="font-size: 12px;">{{ $invoice->shipment?->shipment_code ?? 'N/A' }}</div>
                <div class="text-xs text-muted">Weight: {{ $invoice->shipment?->weight ?? '0' }} KG</div>
                <div class="text-xs text-muted">{{ $invoice->shipment?->description ?? 'N/A' }}</div>
            </td>
            <td width="33%" style="vertical-align: top;">
                <div class="section-label">Payment Details</div>
                <div class="text-xs text-muted">Method: <span class="fw-bold text-dark">{{ $invoice->payment_method ?? 'N/A' }}</span></div>
                <div class="text-xs text-muted">Status: <span class="fw-bold text-dark text-uppercase">{{ $invoice->status }}</span></div>
                @if($invoice->cod_date)
                <div class="text-xs text-muted">COD Date: {{ \Carbon\Carbon::parse($invoice->cod_date)->format('d M Y') }}</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- ITEMS TABLE -->
    <table class="w-100 items-table mb-20">
        <thead>
            <tr>
                <th class="text-left">Description</th>
                <th class="text-right" width="30%">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <div class="fw-bold text-dark">Shipment Charges</div>
                    <div class="text-xs text-muted">Ref: {{ $invoice->shipment?->shipment_code ?? 'N/A' }}</div>
                </td>
                <td class="text-right fw-bold">{{ number_format($invoice->amount_due, 2) }}</td>
            </tr>
            @if($invoice->cod > 0)
            <tr>
                <td>
                    <div class="fw-bold text-dark">Cash on Delivery (COD)</div>
                    <div class="text-xs text-muted">To be collected</div>
                </td>
                <td class="text-right fw-bold">{{ number_format($invoice->cod, 2) }}</td>
            </tr>
            @endif
            @if($invoice->output_tax > 0)
            <tr>
                <td class="fw-bold text-dark">Output Tax</td>
                <td class="text-right fw-bold">{{ number_format($invoice->output_tax, 2) }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    <!-- TOTALS -->
    <table class="w-100 mb-20">
        <tr>
            <td width="60%"></td>
            <td width="40%">
                <table class="w-100">
                    <tr>
                        <td class="text-left text-muted">Subtotal</td>
                        <td class="text-right text-dark">{{ number_format($invoice->amount_due, 2) }} PKR</td>
                    </tr>
                    @if($invoice->output_tax > 0)
                    <tr>
                        <td class="text-left text-muted">Tax</td>
                        <td class="text-right text-dark">{{ number_format($invoice->output_tax, 2) }} PKR</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-left fw-bold text-dark" style="padding-top: 10px; border-top: 1px solid #14396b;">Total Amount</td>
                        <td class="text-right fw-bold text-green" style="padding-top: 10px; border-top: 1px solid #14396b; font-size: 14px;">
                            {{ number_format($invoice->amount_due + $invoice->output_tax, 2) }} PKR
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- PAYMENT HISTORY -->
    @if($invoice->shipment && $invoice->shipment->payments->count() > 0)
    <table class="w-100 mb-20">
        <tr>
            <td>
                <div class="section-label">Payment History</div>
                <table class="w-100" style="border: 1px solid #e5e7eb;">
                    <thead style="background-color: #f1f5f9;">
                        <tr>
                            <th class="text-left text-xs" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb; color: #666;">Date</th>
                            <th class="text-left text-xs" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb; color: #666;">Method</th>
                            <th class="text-left text-xs" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb; color: #666;">Reference</th>
                            <th class="text-right text-xs" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb; color: #666;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->shipment->payments as $payment)
                        <tr>
                            <td class="text-xs" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb;">{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                            <td class="text-xs" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb;">{{ $payment->payment_method ?? '—' }}</td>
                            <td class="text-xs" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb;">{{ $payment->reference_number ?? '—' }}</td>
                            <td class="text-xs text-right" style="padding: 6px 10px; border-bottom: 1px solid #e5e7eb;">{{ number_format($payment->amount, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
    @endif

    <!-- THANK YOU -->
    <div class="fw-bold text-dark" style="font-size: 12px;">Thank you for your business!</div>
    <div class="text-xs text-muted">If you have any questions, please contact us at {{ $companyEmail }}</div>
    <div class="text-xs text-muted">Generated on {{ now()->format('d M Y \a\t H:i') }}</div>

</body>

</html>
