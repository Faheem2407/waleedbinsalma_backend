<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoiceNumber }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
    </style>
</head>
<body>
    <h1>Appointment Invoice</h1>
    <p><strong>Invoice #:</strong> {{ $invoiceNumber }}</p>
    <p><strong>Date:</strong> {{ now()->format('Y-m-d H:i') }}</p>

    <p>
        <strong>Customer:</strong> {{ $user->first_name ?? '' }} {{ $user->last_name ?? '' }}<br>
        <strong>Email:</strong> {{ $user->email ?? '' }}
    </p>

    <p>
        <strong>Appointment Date:</strong> {{ $appointment->date }} at {{ $appointment->time }}
    </p>

    <h3>Services</h3>
    <table>
        <thead>
            <tr>
                <th>Service</th>
                <th>Price (SAR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($services as $service)
                <tr>
                    <td>{{ $service->name ?? 'N/A' }}</td>
                    <td>{{ number_format($service->price ?? 0, 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <th>Total</th>
                <th>{{ number_format($totalAmount, 2) }}</th>
            </tr>
        </tbody>
    </table>
</body>
</html>
