@extends('backend.app')

@section('title', 'Business Home Banner')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <!--begin::Title-->
                <h1 class="my-1 text-dark fw-bold fs-2">
                    Business Home Banner <small class="text-muted fs-6 fw-normal ms-1"></small>
                </h1>
                <!--end::Title-->

                <!--begin::Breadcrumb-->
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item text-muted">CMS</li>
                    <li class="breadcrumb-item text-muted">Business Home Banner</li>
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
                <div class="mb-4 card-style">
                    <div class="card card-body">
                        <form method="POST" action="{{ route('admin.cms.businessHome.banner.update') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mt-4 col-md-12">
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

                                <div class="mt-4 col-md-12">
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
                                <div class="mt-4 col-md-6">
                                    <div class="input-style-1">
                                        <label for="button_text">Button Text:</label>
                                        <input type="text" placeholder="Enter Button Text" id="button_text"
                                            class="form-control @error('button_text') is-invalid @enderror"
                                            name="button_text" value="{{ old('button_text', $data->button_text ?? '') }}" />
                                        @error('button_text')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-4 col-md-6">
                                    <div class="input-style-1">
                                        <label for="button_link">Button Link:</label>
                                        <input type="text" placeholder="Enter Button Link" id="button_link"
                                            class="form-control @error('button_link') is-invalid @enderror"
                                            name="button_link" value="{{ old('button_link', $data->button_link ?? '') }}" />
                                        @error('button_link')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="mt-4 col-md-12">
                                    <div class="input-style-1">
                                        <label for="background_image">Background Image:</label>
                                        <input type="file"
                                            class="dropify @error('background_image') is-invalid @enderror"
                                            name="background_image" id="background_image"
                                            data-default-file="@isset($data->background_image){{ asset($data->background_image) }}@endisset" />
                                    </div>
                                    @error('background_image')
                                        <span class="text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-4 col-12">
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

@push('script')
    <script>
        $(document).ready(function() {
            $('.dropify').dropify();
        });
    </script>
@endpush
