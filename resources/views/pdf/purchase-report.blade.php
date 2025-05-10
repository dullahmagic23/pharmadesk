<!DOCTYPE html>
<html>
<head>
    <title>Purchase Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Purchase Report</h2>

    <table>
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Date</th>
                <th>Item Type</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchases as $purchase)
                @foreach ($purchase->purchasables as $item)
                    <tr>
                        <td>{{ $purchase->vendor->name ?? '—' }}</td>
                        <td>{{ \Carbon\Carbon::parse($purchase->created_at)->format('Y-m-d') }}</td>
                        <td>{{ class_basename($item->purchasable_type) }}</td>
                        <td>{{ $item->purchasable->name ?? '—' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->unit_cost }}</td>
                        <td>{{ $item->quantity * $item->unit_cost }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</body>
</html>
