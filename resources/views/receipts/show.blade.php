<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $receipt->reference }}</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
            width: 100%;
            margin: 0;
            padding: 0;
        }

        .receipt {
            width: 100%;
            max-width: 226.77px;
            /* 8cm in points */
            padding: 0;
        }

        table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 2px;
            word-wrap: break-word;
            overflow-wrap: break-word;
            font-size: 9px;
        }

        th {
            background: #f5f5f5;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }
    </style>
</head>

<body>
   <div class="receipt">
    <h2 class="text-center">{{ config('app.name') }}</h2>
    <h4 class="text-center">RECEIPT</h4>

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

    <p class="text-right"><strong>Total:</strong> {{ number_format($receipt->sale->total, 2) }}</p>
    <p class="text-right"><strong>Paid:</strong> {{ number_format($receipt->sale->payments()->sum('amount'), 2) }}</p>
    <p class="text-right"><strong>Balance:</strong> {{ number_format($receipt->sale->balance, 2) }}</p>
</div>

    <div class="totals">
        <p><strong>Total: </strong>TSh {{ number_format($receipt->sale->total, 2) }}</p>
        <p><strong>Paid: </strong>TSh {{ number_format($receipt->sale->payments()->sum('amount'), 2) }}</p>
        <p><strong>Balance: </strong>TSh {{ number_format($receipt->sale->balance, 2) }}</p>
    </div>

    <p class="right"><em>Thank you for your purchase!</em></p>
</body>

</html>