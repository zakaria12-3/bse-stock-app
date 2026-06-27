@php
    $money = static fn ($amount) => number_format((float) $amount, 3, ',', ' ') . ' TND';
    $itemsSubtotal = $sale->items->sum(fn ($item) => (float) $item->subtotal);
    $cashReceived = (float) ($sale->cash_received ?? 0);
    $change = (float) ($sale->change ?? max($cashReceived - (float) $sale->total, 0));
    $minimumRows = 4;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $sale->invoice_number }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #000;
            background: #fff;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.35;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .toolbar {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin: 0 auto 14px;
            max-width: 277mm;
        }

        .toolbar button {
            border: 0;
            border-radius: 4px;
            padding: 9px 18px;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
        }

        .btn-back {
            background: #64748b;
        }

        .btn-print {
            background: #2563eb;
        }

        .receipt {
            width: 277mm;
            min-height: 190mm;
            margin: 0 auto;
            padding: 0 2mm;
        }

        .header {
            display: grid;
            grid-template-columns: 1fr 80mm;
            gap: 20mm;
            align-items: start;
            padding-top: 2mm;
            padding-bottom: 7mm;
            border-bottom: 2px solid #000;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 8mm;
        }

        .brand-logo {
            width: 30mm;
            height: 24mm;
            object-fit: contain;
        }

        .brand-name {
            font-size: 31px;
            line-height: 1;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .brand-phone {
            margin-top: 4px;
            font-size: 14px;
        }

        .receipt-meta {
            padding-top: 2mm;
            font-size: 17px;
        }

        .receipt-meta div + div {
            margin-top: 4mm;
        }

        .receipt-no {
            display: flex;
            gap: 6mm;
            padding: 5mm 0 8mm;
            font-size: 18px;
            font-weight: 800;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .items {
            border: 2px solid #000;
        }

        .items th,
        .items td {
            border: 1px solid #000;
            padding: 4mm 4mm;
            height: 13mm;
            vertical-align: middle;
        }

        .items th {
            background: #f3f3f3;
            font-size: 18px;
            text-align: left;
            font-weight: 800;
        }

        .items td {
            font-size: 18px;
        }

        .item-name {
            width: 47%;
        }

        .qty {
            width: 8.5%;
            text-align: center;
        }

        .price,
        .discount {
            width: 15%;
            text-align: center;
            white-space: nowrap;
        }

        .amount {
            width: 18%;
            text-align: center;
            white-space: nowrap;
        }

        .bottom {
            display: grid;
            grid-template-columns: 62mm 1fr 84mm;
            gap: 18mm;
            align-items: start;
            padding-top: 10mm;
        }

        .received {
            padding-left: 11mm;
            font-size: 18px;
        }

        .signature {
            margin-top: 22mm;
            font-size: 16px;
        }

        .signature span {
            display: inline-block;
            min-width: 31mm;
            border-bottom: 1px dotted #000;
            transform: translateY(-2px);
        }

        .notice {
            margin: 0 auto;
            width: 96mm;
            border: 1px solid #000;
            padding: 5mm 7mm;
            text-align: center;
            font-size: 14px;
            line-height: 1.25;
        }

        .totals {
            width: 100%;
            font-size: 18px;
            font-weight: 800;
        }

        .totals-row {
            display: grid;
            grid-template-columns: 1fr 44mm;
            gap: 6mm;
            align-items: end;
            margin-bottom: 5mm;
        }

        .totals-value {
            padding-bottom: 1.5mm;
            border-bottom: 1px solid #000;
            white-space: nowrap;
        }

        @media print {
            .toolbar {
                display: none !important;
            }

            body {
                font-size: 16px;
            }

            .receipt {
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" onclick="goBackFromReceipt();" class="btn-back">Retour</button>
        <button type="button" onclick="window.print();" class="btn-print">Telecharger / imprimer PDF</button>
    </div>

    <main class="receipt">
        <section class="header">
            <div class="brand">
                <img class="brand-logo" src="{{ asset('images/bselogo.png') }}" alt="BSE Logo">
                <div>
                    <div class="brand-name">BSE</div>
                    <div class="brand-phone">HP. {{ \App\Models\Setting::get('store_phone', '+216 79 297 450') }}</div>
                </div>
            </div>

            <div class="receipt-meta">
                <div>{{ $sale->sale_date->format('l, d F Y') }}</div>
                <div>Customer: {{ $sale->customer->name ?? 'Walk-in Customer' }}</div>
            </div>
        </section>

        <section class="receipt-no">
            <span>RECEIPT No.</span>
            <span>{{ $sale->invoice_number }}</span>
        </section>

        <table class="items">
            <thead>
                <tr>
                    <th class="item-name">Item</th>
                    <th class="qty">Qty</th>
                    <th class="price">Price</th>
                    <th class="discount">Discount</th>
                    <th class="amount">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                    @php
                        $unitPrice = (float) $item->unit_price + (float) $item->labor_total;
                    @endphp
                    <tr>
                        <td class="item-name">{{ $item->product?->designation ?? $item->product?->name ?? $item->product_name ?? 'Article supprime' }}</td>
                        <td class="qty">{{ $item->quantity }}</td>
                        <td class="price">{{ $money($unitPrice) }}</td>
                        <td class="discount">{{ (float) $item->discount > 0 ? $money($item->discount) : '-' }}</td>
                        <td class="amount">{{ $money($item->subtotal) }}</td>
                    </tr>
                @endforeach

                @for($i = $sale->items->count(); $i < $minimumRows; $i++)
                    <tr>
                        <td class="item-name">&nbsp;</td>
                        <td class="qty">&nbsp;</td>
                        <td class="price">&nbsp;</td>
                        <td class="discount">&nbsp;</td>
                        <td class="amount">&nbsp;</td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <section class="bottom">
            <div class="received">
                <div>Received By</div>
                <div class="signature">( <span></span> )</div>
            </div>

            <div class="notice">
                Please check all items at the time of delivery. Sold items are not returnable unless approved by BSE.
            </div>

            <div class="totals">
                <div class="totals-row">
                    <div>Subtotal</div>
                    <div class="totals-value">{{ $money($itemsSubtotal) }}</div>
                </div>
                <div class="totals-row">
                    <div>Total</div>
                    <div class="totals-value">{{ $money($sale->total) }}</div>
                </div>
                <div class="totals-row">
                    <div>Cash Received</div>
                    <div class="totals-value">{{ $money($cashReceived) }}</div>
                </div>
                <div class="totals-row">
                    <div>Change</div>
                    <div class="totals-value">{{ $money($change) }}</div>
                </div>
            </div>
        </section>
    </main>

    <script>
        function goBackFromReceipt() {
            if (window.opener && !window.opener.closed) {
                window.close();
                return;
            }

            if (window.history.length > 1) {
                window.history.back();
                return;
            }

            window.location.href = "{{ route('sales.index') }}";
        }
    </script>
</body>
</html>
