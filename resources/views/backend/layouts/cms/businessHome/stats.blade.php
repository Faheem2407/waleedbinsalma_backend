@extends('backend.app')

@section('title', 'Business Home Stats')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bold my-1 fs-2">
                    Business Home Stats <small class="text-muted fs-6 fw-normal ms-1"></small>
                </h1>
                <!--end::Title-->

                <!--begin::Breadcrumb-->
                <ul class="breadcrumb fw-semibold fs-base my-1">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item text-muted">CMS</li>
                    <li class="breadcrumb-item text-muted">Business Home</li>
                    <li class="breadcrumb-item text-muted">Stats</li>
                </ul>
                <!--end::Breadcrumb-->
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card-style mb-4">
                    <div class="card card-body">
                        <form method="POST" action="{{ route('admin.cms.businessHome.stats.update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12 mt-4">
                                    <div class="input-style-1">
                                        <label for="title">Title:</label>
                                        <input type="text" placeholder="Enter Title" id="title"
                                            class="form-control @error('title') is-invalid @enderror" name="title"
                                            value="{{ old('title', $data->title ?? '') }}" />
                                        @error('title')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-md-12 mt-4">
                                    <div class="input-style-1">
                                        <label for="sub_title">Subtitle:</label>
                                        <input type="text" placeholder="Enter Subtitle" id="sub_title"
                                            class="form-control @error('sub_title') is-invalid @enderror" name="sub_title"
                                            value="{{ old('sub_title', $data->sub_title ?? '') }}" />
                                        @error('sub_title')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- satisfied_clients,pro_consultants,years_in_businesses,successful_cases -->
                                <div class="col-md-6 mt-4">
                                    <div class="input-style-1">
                                        <label for="satisfied_clients">Satisfied Clients:</label>
                                        <input type="text" placeholder="Enter Satisfied Clients" id="satisfied_clients"
                                            class="form-control @error('satisfied_clients') is-invalid @enderror" name="satisfied_clients"
                                            value="{{ old('satisfied_clients', $data->satisfied_clients ?? '') }}" />
                                        @error('satisfied_clients')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <div class="input-style-1">
                                        <label for="pro_consultants">Pro Consultants:</label>
                                        <input type="text" placeholder="Enter Pro Consultants" id="pro_consultants"
                                            class="form-control @error('pro_consultants') is-invalid @enderror" name="pro_consultants"
                                            value="{{ old('pro_consultants', $data->pro_consultants ?? '') }}" />
                                        @error('pro_consultants')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <div class="input-style-1">
                                        <label for="years_in_businesses">Years in Businesses:</label>
                                        <input type="text" placeholder="Enter Years in Businesses" id="years_in_businesses"
                                            class="form-control @error('years_in_businesses') is-invalid @enderror" name="years_in_businesses"
                                            value="{{ old('years_in_businesses', $data->years_in_businesses ?? '') }}" />
                                        @error('years_in_businesses')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6 mt-4">
                                    <div class="input-style-1">
                                        <label for="successful_cases">Successful Cases:</label>
                                        <input type="text" placeholder="Enter Successful Cases" id="successful_cases"
                                            class="form-control @error('successful_cases') is-invalid @enderror" name="successful_cases"
                                            value="{{ old('successful_cases', $data->successful_cases ?? '') }}" />
                                        @error('successful_cases')
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
