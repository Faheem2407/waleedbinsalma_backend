@extends('backend.app')
@section('title', 'Complain Details')

@section('content')

<!--begin::Toolbar-->
<div class="toolbar" id="kt_toolbar">
    <div class="container-fluid d-flex flex-stack flex-wrap flex-sm-nowrap">
        <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
            <h1 class="text-dark fw-bold my-1 fs-2">Complain Details</h1>
            <ul class="breadcrumb fw-semibold fs-base my-1">
                <li class="breadcrumb-item text-muted">
                    <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('admin.complain.index') }}" class="text-muted text-hover-primary">Complaints</a>
                </li>
                <li class="breadcrumb-item text-dark">Details</li>
            </ul>
        </div>
    </div>
</div>
<!--end::Toolbar-->

<section>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="bg-white p-5 rounded">
                    <h4 class="mb-4">Complain Information</h4>

                    <table class="table table-bordered">
                        <tr>
                            <th>Customer Name</th>
                            <td>{{ $complain->user->first_name . ' ' . $complain->user->last_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Store Name</th>
                            <td>{{ $complain->store->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Message</th>
                            <td>{{ $complain->message }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td>{{ $complain->created_at->format('d M Y h:i A') }}</td>
                        </tr>
                    </table>

                    <a href="{{ route('admin.complain.index') }}" class="btn btn-secondary mt-3">
                        <i class="fas fa-arrow-left"></i> Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
