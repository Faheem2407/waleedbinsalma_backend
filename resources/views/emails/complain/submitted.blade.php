@component('mail::message')
# 🛑 New Store Complaint Received

## 🧑‍💼 Submitted By:
- **Name:** {{ $customer->first_name }} {{ $customer->last_name }}
- **Email:** {{ $customer->email }}

## 🏪 Store Info:
- **Store ID:** {{ $store->id }}
- **Store Name:** {{ $store->name }}

## 📝 Complaint Message:
{{ $complain->message }}

> Submitted at {{ $complain->created_at->format('Y-m-d H:i:s') }}

Thanks,  
{{ config('app.name') }}
@endcomponent
