@extends('backend.app')

@section('title', 'Subscription Price Settings')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <h1 class="text-dark fw-bold my-1 fs-2">
                    Dashboard <small class="text-muted fs-6 fw-normal ms-1"></small>
                </h1>
                <ul class="breadcrumb fw-semibold fs-base my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">
                            Home </a>
                    </li>
                    <li class="breadcrumb-item text-muted">Setting</li>
                    <li class="breadcrumb-item text-muted">Subscription Price</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-style mb-4">
                    <div class="card card-body">
                        <form method="POST" action="{{ route('subscription-price.update') }}">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mt-4">
                                    <div class="input-style-1">
                                        <label for="name">Price Name:</label>
                                        <input type="text" placeholder="Enter price name" id="name"
                                            class="form-control @error('name') is-invalid @enderror" name="name"
                                            value="{{ old('name', $subscriptionPrice->name ?? '') }}" />
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="input-style-1">
                                        <label for="price">Price (USD):</label>
                                        <input type="text" placeholder="Enter price in USD" id="price"
                                            class="form-control @error('price') is-invalid @enderror" name="price"
                                            value="{{ old('price', $subscriptionPrice->price ?? '') }}" />
                                        @error('price')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('admin.dashboard') }}" class="btn btn-danger me-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
