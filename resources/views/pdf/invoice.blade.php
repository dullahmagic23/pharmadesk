<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <link rel="icon" href="{{asset('assests/logo.png')}}">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 13px;
            background: #fff;
            color: #2c3e50;
            margin: 0;
            padding: 0;
        }
        .invoice-container {
            background: #fff;
            max-width: 700px;
            margin: 30px auto;
            border-radius: 8px;
            box-shadow: 0 4px 24px rgba(44,62,80,.09);
            padding: 32px 30px 24px 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 16px;
        }
        .header h1 {
            letter-spacing: 2px;
            color: #4d63ff;
            margin-bottom: 0.2em;
            font-size: 2rem;
            font-weight: 700;
        }
        .header h2 {
            font-size: 1.08rem;
            margin: 0;
            font-weight: normal;
            color: #888;
        }
        .info-section {
            margin: 12px 0 16px 0;
        }
        .info-section p {
            margin: 2px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
            background: #fafbff;
            border-radius: 4px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(44,62,80,0.02);
        }
        thead tr {
            background: #4d63ff;
            color: #fff;
        }
        th {
            padding: 9px 8px;
            font-weight: 600;
            border: none;
            font-size: 1em;
            letter-spacing: 1px;
        }
        td {
            padding: 8px 8px;
            border-top: 1px solid #e0e3ec;
            font-size: 1em;
        }
        tfoot td {
            background: #f6f8fd;
            border-top: 2px solid #4d63ff;
        }
        .summary {
            margin-top: 22px;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }
        .summary-row {
            font-size: 1.08em;
        }
        .summary-row strong {
            min-width: 74px;
            display: inline-block;
        }
        .status {
            font-weight: 600;
            color: #4d63ff;
            padding: 2px 18px;
            background: #fff;
            border-radius: 1em;
            font-size: 0.93em;
            display: inline-block;
            margin-top: 2px;
        }
        @media print {
            body {
                background: #fff !important;
            }
            .invoice-container {
                box-shadow: none;
                border: 1px solid #ccc;
            }
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="header">
        <h1>INVOICE</h1>
        <h2>To: {{ $invoice->patient->first_name.' '.$invoice->patient->last_name }}</h2>
    </div>

    <div class="info-section">
        <p><strong>Date:</strong> {{ $invoice->invoice_date }}</p>
        <p><strong>Invoice#</strong> {{ $invoice->invoice_number }}</p>
        <p><strong>Email:</strong> {{ $invoice->patient->email }}</p>
        <p><strong>Phone:</strong> {{ $invoice->patient->phone }}</p>
    </div>

    <table>
        <thead>
        <tr>
            <th>Item</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Total</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item->billable->name ?? 'N/A' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }}</td>
                <td>{{ number_format($item->total_price, 2) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-row"><strong>Total:</strong> {{ number_format($invoice->total, 2) }}</div>
        <div class="summary-row"><strong>Balance:</strong> {{ number_format($invoice->balance, 2) }}</div>
        <div class="summary-row"><strong>Status:</strong> <span class="status">{{ ucfirst($invoice->status) }}</span></div>
    </div>
</div>
</body>
</html>
