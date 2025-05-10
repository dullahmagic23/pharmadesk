<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1 style="text-align: center; margin-bottom: 20px;">Sales Report</h1>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Buyer Name</th>
                <th>Items Count</th>
                <th>Total</th>
                <th>Paid</th>
                <th>Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($sale->created_at)->format('Y-m-d') }}</td>
                    <td>{{ $sale->buyer->name }}</td>
                    <td>{{ $sale->items_count }}</td>
                    <td>{{ number_format($sale->total, 2) }}</td>
                    <td>{{ number_format($sale->paid, 2) }}</td>
                    <td>{{ number_format($sale->balance, 2) }}</td>
                    <td>
                        @if ($sale->status === 'paid')
                            Paid
                        @elseif ($sale->status === 'partial')
                            Partially Paid
                        @else
                            Unpaid
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <style>
        @page {
            size: A4 landscape;
            margin: 20mm;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        thead tr {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }

        th,
        td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tbody tr:hover {
            background-color: #f1f1f1;
        }

        tbody td {
            color: #555;
        }

        tbody td:last-child {
            font-weight: bold;
        }
    </style>
</body>

</html>