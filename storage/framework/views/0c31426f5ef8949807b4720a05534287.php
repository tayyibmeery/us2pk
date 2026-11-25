<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Invoice <?php echo e($invoice->invoice_no); ?></title>
    <style>
        @page {
            size: A4;
            /* Increased top margin to 58mm to clear the letterhead address */
            margin: 58mm 15mm 35mm 15mm;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #374151;
            line-height: 1.4;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Prevents table rows from splitting across pages */
        tr,
        td,
        th {
            page-break-inside: avoid !important;
        }

        .text-navy {
            color: #14396b;
        }

        .text-green {
            color: #1f9254;
        }

        .text-muted {
            color: #6b7280;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        /* ── HEADER ── */
        .header-table {
            margin-bottom: 15px;
        }

        .invoice-title {
            font-size: 32px;
            font-weight: 900;
            color: #14396b;
            margin: 0;
            line-height: 1;
            letter-spacing: 1px;
        }

        .invoice-title-underline {
            width: 50px;
            height: 4px;
            background-color: #1f9254;
            margin-top: 6px;
            border-radius: 2px;
        }

        .meta-box {
            background-color: #f3f6f9;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            text-align: left;
            display: inline-block;
        }

        .meta-box-title {
            font-size: 12px;
            font-weight: bold;
            color: #14396b;
            margin-bottom: 4px;
        }

        .meta-box-date {
            font-size: 11px;
            color: #4b5563;
        }

        /* ── 3-COLUMN INFO ── */
        .info-section {
            margin-bottom: 15px;
            background-color: #fafbfc;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 12px 0;
        }

        .info-table td {
            vertical-align: top;
            padding: 0 15px;
        }

        .info-table td:first-child {
            padding-left: 15px;
        }

        .info-table td:last-child {
            padding-right: 15px;
        }

        .info-label {
            font-size: 9px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 3px;
        }

        .info-value-main {
            font-size: 11px;
            font-weight: bold;
            color: #14396b;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 10px;
            color: #4b5563;
            line-height: 1.4;
        }

        .status-badge {
            display: inline-block;
            background-color: #eef2f6;
            color: #14396b;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 9px;
            font-weight: bold;
            border: 1px solid #d1d5db;
        }

        /* ── ITEMS TABLE ── */
        .items-table {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 12px;
        }

        .items-table th {
            background-color: #14396b;
            color: #ffffff;
            padding: 6px 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .items-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .items-table tr:last-child td {
            border-bottom: none;
        }

        /* ── TOTALS ── */
        .totals-wrapper {
            width: 100%;
            margin-bottom: 15px;
        }

        .totals-table {
            width: 45%;
            margin-left: 55%;
        }

        .totals-table td {
            padding: 6px 10px;
            font-size: 11px;
        }

        .subtotal-row {
            background-color: #f3f6f9;
            color: #14396b;
            font-weight: bold;
            border-top: 1px solid #e5e7eb;
            border-bottom: 1px solid #e5e7eb;
        }

        .paid-row {
            background-color: #dcfce7;
            color: #166534;
            font-weight: bold;
            border-bottom: 1px solid #e5e7eb;
        }

        .grand-total-row {
            background-color: #14396b;
            color: #ffffff;
            font-weight: bold;
            font-size: 13px !important;
        }

        .grand-total-row td {
            padding: 8px 10px;
        }

        /* ── PAYMENT HISTORY ── */
        .section-heading {
            font-size: 12px;
            font-weight: bold;
            color: #14396b;
            margin-bottom: 6px;
        }

        .payment-table {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 15px;
        }

        .payment-table th {
            background-color: #14396b;
            color: #ffffff;
            padding: 5px 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .payment-table td {
            padding: 5px 10px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
        }

        .payment-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .payment-table tr:last-child td {
            border-bottom: none;
        }

        .payment-table tfoot td {
            background-color: #f3f6f9;
            font-weight: bold;
            color: #14396b;
            border-top: 1px solid #e5e7eb;
        }

        /* ── TERMS & THANK YOU ── */
        .terms-section {
            margin-top: 10px;
        }

        .terms-heading {
            font-size: 11px;
            font-weight: bold;
            color: #14396b;
            margin-bottom: 4px;
        }

        .term-line {
            font-size: 9.5px;
            color: #4b5563;
            margin-bottom: 4px;
            vertical-align: middle;
        }

        .check-icon {
            width: 10px;
            height: 10px;
            color: #1f9254;
            vertical-align: text-bottom;
            margin-right: 4px;
        }

        .thank-you {
            font-size: 13px;
            font-style: italic;
            font-weight: bold;
            color: #14396b;
            margin-top: 10px;
        }

        /* ── CANCELLED WATERMARK ── */
        .cancel-watermark {
            position: absolute;
            top: 100mm;
            left: 30mm;
            width: 150mm;
            text-align: center;
            font-size: 80px;
            font-weight: bold;
            color: rgba(192, 57, 43, 0.1);
            transform: rotate(-25deg);
            letter-spacing: 8px;
            z-index: -1;
        }

        .cancel-banner {
            border: 2px solid #c0392b;
            background-color: #fdecea;
            color: #c0392b;
            padding: 6px 10px;
            margin-bottom: 12px;
            font-size: 11px;
            text-align: center;
            font-weight: bold;
            border-radius: 6px;
        }
    </style>
</head>

<body>

    <?php
    $companyEmail = 'admin@us2pk.com';
    $isCancelled = $invoice->status === 'cancelled';

    $allPayments = collect();
    if ($invoice->shipments->isNotEmpty()) {
    foreach ($invoice->shipments as $s) {
    $allPayments = $allPayments->concat($s->payments ?? collect());
    }
    } elseif ($invoice->shipment) {
    $allPayments = $invoice->shipment->payments ?? collect();
    }

    $allPayments = $allPayments->sortByDesc('payment_date');

    $billedUser = $invoice->user ?? $invoice->shipment?->user;

    // Calculate correct totals based on Shipment Values
    $totalShipmentValue = 0;
    if ($invoice->shipments->isNotEmpty()) {
    foreach ($invoice->shipments as $s) {
    $totalShipmentValue += ($s->item_value_pkr ?? 0) + ($s->company_charges ?? 0);
    }
    } else {
    $totalShipmentValue = $invoice->amount_due;
    }

    $totalPaid = $allPayments->sum('amount');
    $balanceDue = max(0, $totalShipmentValue - $totalPaid);
    ?>

    <?php if($isCancelled): ?>
    <div class="cancel-watermark">CANCELLED</div>
    <?php endif; ?>

    <!-- HEADER -->
    <table class="header-table">
        <tr>
            <td style="vertical-align: bottom;">
                <h1 class="invoice-title">INVOICE</h1>
                <div class="invoice-title-underline"></div>
            </td>
            <td class="text-right" style="vertical-align: bottom;">
                <div class="meta-box">
                    <div class="meta-box-title">Invoice No: <?php echo e($invoice->invoice_no); ?></div>
                    <div class="meta-box-date">Date: <?php echo e(\Carbon\Carbon::parse($invoice->date)->format('d M Y')); ?></div>
                </div>
            </td>
        </tr>
    </table>

    <?php if($isCancelled): ?>
    <div class="cancel-banner">
        THIS INVOICE HAS BEEN CANCELLED — NOT VALID FOR PAYMENT
    </div>
    <?php endif; ?>

    <!-- 3-COLUMN INFO SECTION -->
    <div class="info-section">
        <table class="info-table">
            <tr>
                <td width="33%" style="border-right: 1px solid #e5e7eb;">
                    <div class="info-label">Billed To</div>
                    <div class="info-value-main"><?php echo e($billedUser?->name ?? 'N/A'); ?></div>
                    <div class="info-value"><?php echo e($billedUser?->email ?? 'N/A'); ?></div>
                    <div class="info-value"><?php echo e($billedUser?->city?->city_name ?? 'N/A'); ?></div>
                </td>

                <td width="33%" style="border-right: 1px solid #e5e7eb;">
                    <div class="info-label">Shipments Covered</div>
                    <?php if($invoice->shipments->count()): ?>
                    <div class="info-value-main"><?php echo e($invoice->shipments->count()); ?> Shipment(s)</div>
                    <div class="info-value">Total Weight: <?php echo e($invoice->shipments->sum('weight')); ?> KG</div>
                    <?php else: ?>
                    <div class="info-value-main"><?php echo e($invoice->shipment?->shipment_code ?? 'N/A'); ?></div>
                    <div class="info-value">Total Weight: <?php echo e($invoice->shipment?->weight ?? '0'); ?> KG</div>
                    <?php endif; ?>
                </td>

                <td width="33%">
                    <div class="info-label">Payment Details</div>
                    <div class="info-value">Method: <span class="fw-bold text-navy"><?php echo e($invoice->payment_method ?? 'N/A'); ?></span></div>
                    <div class="info-value" style="margin-top: 4px;">Status: <span class="status-badge"><?php echo e($invoice->status); ?></span></div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ITEMS TABLE -->
    <table class="items-table">
        <thead>
            <tr>
                <th width="6%" class="text-center">#</th>
                <th width="44%">Description</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Unit Price (PKR)</th>
                <th width="20%" class="text-right">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            <?php $rowNum = 1; ?>

            <?php $__empty_1 = true; $__currentLoopData = $invoice->shipments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shipment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php
            $itemAmount = ($shipment->item_value_pkr ?? 0) + ($shipment->company_charges ?? 0);
            ?>
            <tr>
                <td class="text-center fw-bold text-navy"><?php echo e($rowNum++); ?></td>
                <td>
                    <div class="fw-bold text-navy" style="font-size: 10.5px;">Shipment Charges</div>
                    <div class="text-muted" style="font-size: 9px; margin-top: 2px;">
                        Ref: <?php echo e($shipment->shipment_code ?? 'N/A'); ?>

                        <?php if($shipment->description): ?> &middot; <?php echo e(\Illuminate\Support\Str::limit($shipment->description, 45)); ?> <?php endif; ?>
                        <?php if($shipment->weight): ?> &middot; <?php echo e($shipment->weight); ?> KG <?php endif; ?>
                    </div>
                </td>
                <td class="text-center">1</td>
                <td class="text-right"><?php echo e(number_format($itemAmount, 2)); ?></td>
                <td class="text-right fw-bold"><?php echo e(number_format($itemAmount, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td class="text-center fw-bold text-navy"><?php echo e($rowNum++); ?></td>
                <td>
                    <div class="fw-bold text-navy" style="font-size: 10.5px;">Shipment Charges</div>
                    <div class="text-muted" style="font-size: 9px; margin-top: 2px;">Ref: <?php echo e($invoice->shipment?->shipment_code ?? 'N/A'); ?></div>
                </td>
                <td class="text-center">1</td>
                <td class="text-right"><?php echo e(number_format($invoice->amount_due, 2)); ?></td>
                <td class="text-right fw-bold"><?php echo e(number_format($invoice->amount_due, 2)); ?></td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- TOTALS -->
    <div class="totals-wrapper">
        <table class="totals-table">
            <tr class="subtotal-row">
                <td class="text-left">Sub Total:</td>
                <td class="text-right"><?php echo e(number_format($totalShipmentValue, 2)); ?></td>
            </tr>
            <tr class="subtotal-row">
                <td class="text-left">Total Amount:</td>
                <td class="text-right"><?php echo e(number_format($totalShipmentValue, 2)); ?> PKR</td>
            </tr>
            <tr class="paid-row">
                <td class="text-left">Paid Amount:</td>
                <td class="text-right"><?php echo e(number_format($totalPaid, 2)); ?> PKR</td>
            </tr>
            <tr class="grand-total-row">
                <td class="text-left">Balance Due:</td>
                <td class="text-right"><?php echo e(number_format($balanceDue, 2)); ?> PKR</td>
            </tr>
        </table>
    </div>

    <!-- PAYMENT HISTORY -->
    <?php if($allPayments->count() > 0): ?>
    <div class="section-heading">Payment History</div>
    <table class="payment-table">
        <thead>
            <tr>
                <th width="25%">Date</th>
                <th width="25%">Method</th>
                <th width="30%">Reference</th>
                <th width="20%" class="text-right">Amount (PKR)</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $allPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>
                    <?php echo e($payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') : ($payment->created_at ? \Carbon\Carbon::parse($payment->created_at)->format('d M Y') : 'N/A')); ?>

                </td>
                <td><?php echo e($payment->payment_method ?? '—'); ?></td>
                <td><?php echo e($payment->reference_number ?? '—'); ?></td>
                <td class="text-right"><?php echo e(number_format($payment->amount, 2)); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="text-right text-navy">Total Paid:</td>
                <td class="text-right text-green"><?php echo e(number_format($totalPaid, 2)); ?></td>
            </tr>
        </tfoot>
    </table>
    <?php endif; ?>

    <!-- TERMS & THANK YOU -->
    <div class="terms-section">
        <div class="terms-heading">Terms & Conditions</div>

        <div class="term-line">
            <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
            Payment is due within 7 days of invoice date.
        </div>

        <div class="term-line">
            <svg class="check-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
            </svg>
            For any queries, please contact us at <?php echo e($companyEmail); ?>.
        </div>

        <div class="thank-you">Thank you for your business!</div>
    </div>

</body>

</html>
<?php /**PATH D:\us2pk\resources\views/pdf/invoice_content.blade.php ENDPATH**/ ?>