<!DOCTYPE html>
<html>
<head>
    <title>Purchase #{{ $purchase->id }}</title>
    <style>
    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 13px;
        background-color: #fff;
        margin: 32px;
        color: #222;
    }
    h1 {
        font-size: 1.7em;
        letter-spacing: 1px;
        color: #253858;
        margin-bottom: 8px;
        font-weight: 700;
    }
    h3 {
        font-size: 1.25em;
        margin: 24px 0 10px 0;
        font-weight: 600;
        color: #34495e;
    }
    p {
        margin: 6px 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        box-shadow: 0 2px 8px rgba(60,72,88,0.08);
        background: #fff;
    }
    th, td {
        border: 1px solid #C1C7D0;
        padding: 8px 10px;
    }
    th {
        background: #f3f5fa;
        color: #21243d;
        font-size: 1.05em;
        font-weight: 600;
        text-align: left;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    tbody tr:nth-child(even) {
        background: #f8fafc;
    }
    tbody tr:last-child {
        background: #f5f7fa;
        font-weight: 700;
        border-top: 2px solid #767da0;
    }
    tbody tr:last-child td {
        font-size: 1.1em;
        color: #23315b;
    }
    td {
        vertical-align: middle;
    }
    td[colspan] {
        background: transparent;
        border: none;
    }
    strong {
        color: #182650;
    }
</style>
</head>
<body>
<h1>Purchase Details</h1>
<p><strong>Vendor:</strong> {{ $purchase->vendor->name }}</p>
<p><strong>Date:</strong> {{ $purchase->created_at->format('Y-m-d') }}</p>
<h3>Items</h3>
<table>
    <thead>
    <tr>
        <th>Type</th>
        <th>Name</th>
        <th>Batch</th>
        <th>Expiry</th>
        <th>Qty</th>
        <th>Unit Cost</th>
        <th>Subtotal</th>
    </tr>
    </thead>
    <tbody>
    @foreach($purchase->purchasables as $item)
        <tr>
            <td>{{ class_basename($item->purchasable_type) }}</td>
            <td>{{ $item->purchasable->name ?? '-' }}</td>
            <td>{{ $item->batch_number }}</td>
            <td>{{ $item->expiry_date }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ number_format($item->unit_cost, 2) }}</td>
            <td>{{ number_format($item->subtotal, 2) }}</td>
        </tr>
    @endforeach
    <tr>
        <td colspan="6"></td>
        <td>{{number_format($purchase->total_amount,2)}}</td>
    </tr>
    </tbody>
</table>
</body>
</html>
