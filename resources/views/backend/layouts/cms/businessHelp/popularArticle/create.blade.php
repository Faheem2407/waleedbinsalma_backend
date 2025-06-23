@extends('backend.app')

@section('title', 'Business Help Popular Articles')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <h1 class="my-1 text-dark fw-bold fs-2">
                    Dashboard <small class="text-muted fs-6 fw-normal ms-1"></small>
                </h1>
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Home</a>
                    </li>
                    <li class="breadcrumb-item text-muted">CMS</li>
                    <li class="breadcrumb-item text-muted">Business Help</li>
                    <li class="breadcrumb-item text-muted">Popular Articles</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="mb-4 card-style">
                    <div class="card card-body">
                        <form method="POST" action="{{ route('businessHelp.popularArticles.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mt-4 input-style-1">
                                        <label for="title">Title</label>
                                        <input type="text" placeholder="Enter Title" id="title"
                                            class="form-control @error('title') is-invalid @enderror" name="title"
                                            value="{{ old('title') }}" />
                                        @error('title')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-12">
                                    <div class="mt-4 input-style-1">
                                        <label for="description">Description:</label>
                                        <textarea placeholder="Enter Description" id="description"
                                            class="form-control @error('description') is-invalid @enderror"
                                            name="description">{{ old('description') }}</textarea>
                                        @error('description')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <!-- background_image -->
                                <div class="col-md-12">
                                    <div class="mt-4 input-style-1">
                                        <label for="background_image">Background Image:</label>
                                        <input type="file" id="background_image"
                                            class="dropify form-control @error('background_image') is-invalid @enderror"
                                            name="background_image" />
                                        @error('background_image')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                            <div class="mt-4 col-12">
                                <button type="submit" class="btn btn-primary">Create</button>
                                <a href="{{ route('businessHelp.popularArticles.index') }}" class="btn btn-danger me-2">Cancel</a>
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
    $('.dropify').dropify();
</script>
@endpush