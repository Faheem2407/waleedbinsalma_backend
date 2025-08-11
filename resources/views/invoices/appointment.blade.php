<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            font-size: 16px;
            line-height: 24px;
            color: #555;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }
        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.item.last td {
            border-bottom: none;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .logo {
            max-width: 150px;
            max-height: 100px;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <div>
                <h1>INVOICE</h1>
                <p>Invoice #: {{ $invoice_number }}</p>
                <p>Date: {{ $invoice_date }}</p>
            </div>
            @if($store->logo)
                <img src="{{ $store->logo }}" class="logo">
            @endif
        </div>
        
        <table>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>From:</strong><br>
                                {{ $store->name }}<br>
                                {{ $store->address ?? '' }}<br>
                                {{ $store->city ?? '' }}, {{ $store->country ?? '' }}
                            </td>
                            <td>
                                <strong>To:</strong><br>
                                {{ $customer->first_name.' '.$customer->last_name }}<br>
                                {{ $customer->email }}<br>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class="heading">
                <td>Appointment Details</td>
                <td></td>
            </tr>
            
            <tr class="details">
                <td>Date</td>
                <td>{{ $appointment->date }}</td>
            </tr>
            
            <tr class="details">
                <td>Time</td>
                <td>{{ $appointment->time }}</td>
            </tr>
            
            <tr class="details">
                <td>Status</td>
                <td>{{ ucfirst($appointment->status) }}</td>
            </tr>
            
            <tr class="heading">
                <td>Service</td>
                <td>Price</td>
            </tr>
            
            @foreach($services as $service)
            <tr class="item">
                <td>
                    {{ $service['name'] }}<br>
                    <small>{{ $service['description'] }}</small><br>
                    <small>Duration: {{ $service['duration'] }} mins</small>
                </td>
                <td>{{ number_format($service['price'], 2) }} SAR</td>
            </tr>
            @endforeach
            
            <tr class="total">
                <td></td>
                <td>Total: {{ number_format($total_amount, 2) }} SAR</td>
            </tr>
        </table>
        
        <div style="margin-top: 50px; text-align: center; font-size: 12px;">
            <p>Thank you for booking appointment!</p>
            <p>For any questions, please contact {{ $store->name }} at {{ $store->email ?? $store->phone ?? '' }}</p>
        </div>
    </div>
</body>
</html>