<!DOCTYPE html>
<html>
<head>
    <title>New Appointment Assignment</title>
</head>
<body>
    <h1>New Appointment Assignment</h1>
    <p>Hello {{ $team->first_name }},</p>
    <p>You have been assigned to a new appointment at {{ $store->name }}.</p>
    
    <h2>Appointment Details</h2>
    <p><strong>Date:</strong> {{ $appointment->date }}</p>
    <p><strong>Time:</strong> {{ $appointment->time }}</p>
    <p><strong>Notes:</strong> {{ $appointment->booking_notes }}</p>
    
    <h2>Services</h2>
    <ul>
        @foreach ($services as $service)
            <li>{{ $service['name'] }} - {{ $service['price'] }} SAR</li>
        @endforeach
    </ul>
    
    <p>Please confirm your availability for this appointment:</p>
    <p>
        <a href="{{ $accept_url }}" style="padding: 10px 20px; background-color: #28a745; color: white; text-decoration: none;">Accept</a>
        <a href="{{ $decline_url }}" style="padding: 10px 20px; background-color: #dc3545; color: white; text-decoration: none;">Decline</a>
    </p>
    
    <p>If you decline, the appointment will be reassigned to another team member.</p>
    
    <p>Best regards,<br>{{ $store->name }}</p>
</body>
</html>