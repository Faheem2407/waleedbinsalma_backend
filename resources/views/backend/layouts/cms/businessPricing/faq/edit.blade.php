@extends('backend.app')

@section('title', 'CMS | Edit Business Pricing FAQ Section')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <!--begin::Title-->
                <h1 class="my-1 text-dark fw-bold fs-2">
                    Dashboard <small class="text-muted fs-6 fw-normal ms-1"></small>
                </h1>
                <!--end::Title-->

                <!--begin::Breadcrumb-->
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item text-muted">CMS</li>
                    <li class="breadcrumb-item text-muted">Business Pricing</li>
                    <li class="breadcrumb-item text-muted">FAQ</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4 card-style">
                    <div class="card card-body">
                        <form method="POST" action="{{ route('businessPricing.faq.update', $data->id) }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mt-4 input-style-1">
                                        <label for="title">Question:</label>
                                        <input type="text" placeholder="Enter Question" id="title"
                                               class="form-control @error('title') is-invalid @enderror"
                                               name="title" value="{{ old('title', $data->title) }}">
                                        @error('title')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="mt-4 input-style-1">
                                        <label for="description">Answer:</label>
                                        <textarea placeholder="Enter Answer" id="description"
                                                  class="form-control @error('description') is-invalid @enderror"
                                                  name="description">{{ old('description', $data->description) }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                                        @enderror
                                    </div>
                                </div>

                            </div>

                            <div class="mt-4 col-12">
                                <button type="submit" class="btn btn-primary">Update</button>
                                <a href="{{ route('businessPricing.faq.index') }}" class="btn btn-danger me-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
