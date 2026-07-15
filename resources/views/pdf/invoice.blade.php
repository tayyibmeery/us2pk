<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        /* ============================================================
           RESET & PAGE SETUP
           ============================================================ */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 0;
        }

        html,
        body {
            width: 190mm;
            height: 250mm;
            background: #ffffff;
        }

        body {
            font-family: 'Helvetica Neue', Arial, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #2a2a2a;
            font-size: 10px;
            line-height: 1.4;
            -webkit-font-smoothing: antialiased;
        }

        /* ============================================================
           INVOICE CONTAINER
           ============================================================ */
        .invoice-container {
            width: 190mm;
            height: 250mm;
            padding: 14mm 16mm;
            background: #ffffff;
            display: flex;
            flex-direction: column;
        }

        /* ============================================================
           HEADER
           ============================================================ */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 12px;
            margin-bottom: 16px;
            border-bottom: 2.5px solid #16213e;
        }

        .company-name {
            font-size: 18px;
            font-weight: 700;
            color: #16213e;
            letter-spacing: -0.3px;
        }

        .company-address {
            font-size: 8.5px;
            color: #6b7280;
            margin-top: 3px;
            line-height: 1.5;
        }

        .header-right {
            text-align: right;
        }

        .invoice-label {
            font-size: 20px;
            font-weight: 700;
            color: #16213e;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .invoice-no {
            font-size: 10px;
            color: #6b7280;
            font-weight: 500;
            margin-top: 4px;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 2px 10px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 6px;
        }

        .status-paid {
            background: #e8f5ee;
            color: #1b7a43;
        }

        .status-pending {
            background: #fef6e7;
            color: #a16207;
        }

        .status-overdue {
            background: #fdecec;
            color: #b3261e;
        }

        /* ============================================================
           FROM / TO / DATES — inline layout
           ============================================================ */
        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .info-block h4 {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #9ca3af;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-block .name {
            font-weight: 700;
            font-size: 11px;
            color: #16213e;
            display: block;
            margin-bottom: 1px;
        }

        .info-block p {
            font-size: 9px;
            line-height: 1.5;
            color: #4b5563;
        }

        .info-block .date-line {
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #4b5563;
            padding: 2px 0;
        }

        .info-block .date-line span:first-child {
            color: #9ca3af;
        }

        .info-block .date-line strong {
            color: #16213e;
            font-weight: 600;
        }

        /* ============================================================
           MAIN TABLE (Invoice items)
           ============================================================ */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table {
            margin-bottom: 2px;
        }

        .items-table thead th {
            background: #16213e;
            color: #ffffff;
            padding: 5px 10px;
            text-align: left;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            font-weight: 700;
        }

        .items-table thead th:last-child {
            text-align: right;
        }

        .items-table tbody td {
            padding: 5px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 9.5px;
            color: #2a2a2a;
        }

        .items-table tbody td:last-child {
            text-align: right;
            font-weight: 600;
            color: #16213e;
        }

        .item-meta {
            color: #9ca3af;
            font-size: 7.5px;
            font-weight: 400;
            display: inline;
            margin-left: 4px;
        }

        /* Totals summary aligned right under table */
        .totals-wrap {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 16px;
        }

        .totals-box {
            width: 220px;
        }

        .totals-box .line {
            display: flex;
            justify-content: space-between;
            padding: 4px 10px;
            font-size: 9.5px;
            color: #4b5563;
        }

        .totals-box .grand-total {
            display: flex;
            justify-content: space-between;
            padding: 6px 10px;
            margin-top: 2px;
            background: #16213e;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            border-radius: 2px;
        }

        /* ============================================================
           PAYMENT HISTORY
           ============================================================ */
        .section-title {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #9ca3af;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .payment-history {
            margin-bottom: 14px;
        }

        .payment-history table {
            width: 100%;
        }

        .payment-history thead th {
            padding: 3px 8px;
            text-align: left;
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            font-weight: 700;
            color: #9ca3af;
            border-bottom: 1px solid #e5e7eb;
        }

        .payment-history thead th:last-child {
            text-align: right;
        }

        .payment-history tbody td {
            padding: 3px 8px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 8.5px;
            color: #2a2a2a;
        }

        .payment-history tbody td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .payment-history .balance-line td {
            padding-top: 6px;
            font-size: 9.5px;
            font-weight: 700;
            color: #16213e;
            border-bottom: none;
        }

        .payment-history .balance-line .amt-paid {
            color: #1b7a43;
        }

        .payment-history .balance-line.due .amt-due {
            color: #b3261e;
        }

        .payment-history .balance-line.due .amt-due.zero {
            color: #1b7a43;
        }

        /* ============================================================
           SHIPMENT DETAILS
           ============================================================ */
        .shipment-details {
            margin-bottom: 14px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
        }

        .shipment-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        .shipment-grid .label {
            font-size: 6.5px;
            color: #9ca3af;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .shipment-grid .value {
            font-size: 9.5px;
            color: #2a2a2a;
            font-weight: 600;
            margin-top: 1px;
        }

        /* ============================================================
           NOTES
           ============================================================ */
        .notes {
            margin-bottom: 12px;
            padding: 6px 10px;
            background: #fafafa;
            border-left: 2.5px solid #16213e;
            border-radius: 2px;
        }

        .notes p {
            font-size: 8.5px;
            color: #4b5563;
        }

        .notes strong {
            font-weight: 700;
            color: #16213e;
        }

        /* ============================================================
           FOOTER
           ============================================================ */
        .footer {
            margin-top: auto;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-left .thankyou {
            font-size: 10px;
            font-weight: 700;
            color: #16213e;
        }

        .footer-left .generated {
            font-size: 7px;
            color: #9ca3af;
            margin-top: 1px;
        }

        .footer-right {
            text-align: right;
            font-size: 7.5px;
            color: #9ca3af;
        }

        /* ============================================================
           PRINT
           ============================================================ */
        @media print {

            html,
            body {
                width: 190mm;
                height: 250mm;
            }

            .invoice-container {
                width: 190mm;
                height: 250mm;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>

<body>

    @php
    $isPaid = !is_null($invoice->cod_date) && $invoice->cod > 0;
    $isOverdue = !$isPaid && \Carbon\Carbon::parse($invoice->date)->addDays(7)->isPast();
    $status = $isPaid ? 'paid' : ($isOverdue ? 'overdue' : 'pending');
    $statusLabels = ['paid' => 'Paid', 'pending' => 'Pending', 'overdue' => 'Overdue'];
    $statusColors = ['paid' => 'status-paid', 'pending' => 'status-pending', 'overdue' => 'status-overdue'];

    $shipment = $invoice->shipment;
    $payments = $shipment?->payments ?? collect();
    $totalPaid = $payments->sum('amount');
    $totalPayable = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
    $remaining = $totalPayable - $totalPaid;
    $totalAmount = ($invoice->amount_due ?? 0) + ($invoice->output_tax ?? 0);
    $subtotal = $invoice->amount_due ?? 0;
    @endphp

    <div class="invoice-container">

        <!-- ============================================================
        HEADER
        ============================================================ -->
        <div class="header">
            <div>
                <div class="company-name">{{ $company['name'] ?? 'US2PK Logistics' }}</div>
                <div class="company-address">
                    {{ $company['address'] ?? '123 Business Street, Lahore, Pakistan' }} &nbsp;·&nbsp;
                    {{ $company['phone'] ?? '+92-300-1234567' }} &nbsp;·&nbsp;
                    {{ $company['email'] ?? 'info@us2pk.com' }}
                </div>
            </div>
            <div class="header-right">
                <div class="invoice-label">Invoice</div>
                <div class="invoice-no">#{{ $invoice->invoice_no }}</div>
                <span class="status-badge {{ $statusColors[$status] }}">{{ $statusLabels[$status] }}</span>
            </div>
        </div>

        <!-- ============================================================
        FROM / TO / DATES
        ============================================================ -->
        <div class="info-row">
            <div class="info-block">
                <h4>Billed To</h4>
                <span class="name">{{ $invoice->shipment?->user?->name ?? 'N/A' }}</span>
                <p>
                    {{ $invoice->shipment?->user?->email ?? 'N/A' }}<br>
                    {{ $invoice->shipment?->user?->city?->city_name ?? 'N/A' }}
                </p>
            </div>
            <div class="info-block">
                <h4>Shipment</h4>
                <p>
                    <strong style="color:#16213e;">{{ $shipment?->shipment_code ?? 'N/A' }}</strong><br>
                    {{ $shipment?->bought_by ?? 'N/A' }} &nbsp;·&nbsp; {{ $shipment?->description ?? 'N/A' }}
                </p>
            </div>
            <div class="info-block">
                <h4>Dates</h4>
                <div class="date-line">
                    <span>Issued</span>
                    <strong>{{ \Carbon\Carbon::parse($invoice->date)->format('d M Y') }}</strong>
                </div>
                @if($invoice->cod_date)
                <div class="date-line">
                    <span>COD Date</span>
                    <strong>{{ \Carbon\Carbon::parse($invoice->cod_date)->format('d M Y') }}</strong>
                </div>
                @endif
            </div>
        </div>

        <!-- ============================================================
        INVOICE TABLE
        ============================================================ -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align:right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <strong>Shipment Charges</strong>
                        <span class="item-meta">{{ $shipment?->shipment_code ?? 'N/A' }}</span>
                    </td>
                    <td>{{ number_format($invoice->amount_due, 2) }} PKR</td>
                </tr>
                @if($invoice->cod > 0)
                <tr>
                    <td>
                        <strong>Cash on Delivery</strong>
                        <span class="item-meta">COD</span>
                    </td>
                    <td>{{ number_format($invoice->cod, 2) }} PKR</td>
                </tr>
                @endif
                @if($invoice->output_tax > 0)
                <tr>
                    <td>
                        <strong>Output Tax</strong>
                    </td>
                    <td>{{ number_format($invoice->output_tax, 2) }} PKR</td>
                </tr>
                @endif
            </tbody>
        </table>

        <div class="totals-wrap">
            <div class="totals-box">
                <div class="line">
                    <span>Subtotal</span>
                    <span>{{ number_format($subtotal, 2) }} PKR</span>
                </div>
                @if($invoice->output_tax > 0)
                <div class="line">
                    <span>Output Tax</span>
                    <span>{{ number_format($invoice->output_tax, 2) }} PKR</span>
                </div>
                @endif
                <div class="grand-total">
                    <span>Total Amount</span>
                    <span>{{ number_format($totalAmount, 2) }} PKR</span>
                </div>
            </div>
        </div>

        <!-- ============================================================
        PAYMENT HISTORY
        ============================================================ -->
        @if($payments->count() > 0)
        <div class="payment-history">
            <div class="section-title">Payment History</div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Reference</th>
                        <th style="text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td>{{ $payment->payment_method ?? 'N/A' }}</td>
                        <td>{{ $payment->reference_number ?? '—' }}</td>
                        <td>{{ number_format($payment->amount, 2) }} PKR</td>
                    </tr>
                    @endforeach
                    <tr class="balance-line">
                        <td colspan="3" style="text-align:right;">Total Paid</td>
                        <td style="text-align:right;" class="amt-paid">{{ number_format($totalPaid, 2) }} PKR</td>
                    </tr>
                    <tr class="balance-line due">
                        <td colspan="3" style="text-align:right;">Remaining Balance</td>
                        <td style="text-align:right;" class="amt-due {{ $remaining <= 0 ? 'zero' : '' }}">
                            {{ number_format($remaining, 2) }} PKR
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        <!-- ============================================================
        SHIPMENT DETAILS
        ============================================================ -->
        @if($shipment)
        <div class="shipment-details">
            <div class="section-title">Shipment Details</div>
            <div class="shipment-grid">
                <div>
                    <div class="label">Shipment Code</div>
                    <div class="value">{{ $shipment->shipment_code ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="label">Weight</div>
                    <div class="value">{{ $shipment->weight_kgs ?? $shipment->weight ?? 'N/A' }} kg</div>
                </div>
                <div>
                    <div class="label">Description</div>
                    <div class="value">{{ $shipment->description ?? 'N/A' }}</div>
                </div>
                <div>
                    <div class="label">Bought By</div>
                    <div class="value">{{ $shipment->bought_by ?? 'N/A' }}</div>
                </div>
            </div>
        </div>
        @endif

        <!-- ============================================================
        NOTES
        ============================================================ -->
        @if(!empty($invoice->notes))
        <div class="notes">
            <p><strong>Note:</strong> {{ $invoice->notes }}</p>
        </div>
        @endif

        <!-- ============================================================
        FOOTER
        ============================================================ -->
        <div class="footer">
            <div class="footer-left">
                <div class="thankyou">Thank you for your business</div>
                <div class="generated">Generated on {{ now()->format('d M Y \a\t H:i') }}</div>
            </div>
            <div class="footer-right">
                {{ $company['name'] ?? 'US2PK Logistics' }} &nbsp;·&nbsp; {{ $company['email'] ?? 'info@us2pk.com' }}
            </div>
        </div>

    </div>

</body>

</html>
