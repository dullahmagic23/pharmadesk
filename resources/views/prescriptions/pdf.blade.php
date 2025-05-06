
<!DOCTYPE html>
<html>
<head>
    <title>Prescription</title>
    <style>
body { font-family: sans-serif; }
        h2 { margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        td, th { border: 1px solid #ccc; padding: 8px; }
.section { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h2>Prescription</h2>

    <div class="section">
        <strong>Patient:</strong> {{ $prescription->patient->first_name.' '.$prescription->patient->last_name }}<br>
<strong>Doctor:</strong> {{ $prescription->doctor->first_name.' '.$prescription->doctor->last_name }}<br>
<strong>Date:</strong> {{ $prescription->prescribed_at }}
</div>

@if($prescription->notes)
    <div class="section">
        <strong>Notes:</strong><br>
        {{ $prescription->notes }}
    </div>
@endif

<div class="section">
    <strong>Medicines:</strong>
    <table>
        <thead>
        <tr>
            <th>Medicine</th>
            <th>Dosage</th>
        </tr>
        </thead>
        <tbody>
        @foreach($prescription->medicines as $medicine)
            <tr>
                <td>{{ $medicine->name }}</td>
                <td>{{ $medicine->dosages->first()->frequency ?? 'N/A' }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
