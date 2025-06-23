@extends('backend.app')

@section('title', 'Business Help Knowledge Base Banner')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class=" container-fluid  d-flex flex-stack flex-wrap flex-sm-nowrap">
            <!--begin::Info-->
            <div class="d-flex flex-column align-items-start justify-content-center flex-wrap me-2">
                <!--begin::Title-->
                <h1 class="text-dark fw-bold my-1 fs-2">
                    Business Help Knowledge Base Banner <small class="text-muted fs-6 fw-normal ms-1"></small>
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
                    <li class="breadcrumb-item text-muted">Business Help</li>
                    <li class="breadcrumb-item text-muted">Knowledge Base Banner</li>
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
                        <form method="POST" action="{{ route('businessHelp.knowledgeBaseBanner.update') }}">
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
                                <!-- subtitle -->
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
