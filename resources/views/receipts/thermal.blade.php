<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $receipt->reference }}</title>
    <style>
        body {
            font-family: monospace;
            font-size: 10px;
            width: 226.77px; /* 8cm */
            margin: 0;
            padding: 0;
        }

        .receipt {
            width: 100%;
            padding: 0;
        }

        h2, h4 {
            text-align: center;
            margin: 2px 0;
        }

        p {
            margin: 2px 0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }

        th, td {
            padding: 2px 0;
            font-size: 9px;
        }

        th {
            border-bottom: 1px dashed #000;
        }

        .totals {
            margin-top: 5px;
        }

        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }

        .thankyou {
            text-align: center;
            margin-top: 5px;
            font-size: 9px;
        }

        /* Optional: auto-print on page load */
        @media print {
            body {
                width: 80mm;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <h2>{{ config('app.name') }}</h2>
        <h4>RECEIPT</h4>

        <p><strong>Date:</strong> {{ $receipt->sale->created_at->format('d-m-Y H:i') }}</p>
        <p><strong>Buyer:</strong> {{ $receipt->sale->buyer->name ?? '-' }}</p>
        <p><strong>Receipt #:</strong> {{ $receipt->reference }}</p>
        <p><strong>Served by #:</strong> {{ $receipt->issued_by }}</p>
        <p style="text-transform: uppercase;">SALE ID: {{ $receipt->sale_id }}</p>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="text-center">Qty</th>
                    <th class="text-right">Price</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($receipt->sale->items as $item)
                    <tr>
                        <td>{{ \Illuminate\Support\Str::limit($item->sellable->name ?? 'Item', 20) }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->price, 2) }}</td>
                        <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <p><strong>Total:</strong> <span>TSh {{ number_format($receipt->sale->total, 2) }}</span></p>
            <p><strong>Paid:</strong> <span>TSh {{ number_format($receipt->sale->payments()->sum('amount'), 2) }}</span></p>
            <p><strong>Balance:</strong> <span>TSh {{ number_format($receipt->sale->balance, 2) }}</span></p>
        </div>

        <p class="thankyou"><em>Thank you for your purchase!</em></p>
    </div>

    <script>
        // Optional: Auto-trigger print dialog
        window.print();
    </script>
</body>

</html>
