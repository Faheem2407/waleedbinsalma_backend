@extends('backend.app')

@section('title', 'Create Client Review')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <h1 class="my-1 text-dark fw-bold fs-2">Client Review</h1>
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('client_review.index') }}" class="text-muted text-hover-primary">Client Reviews</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Create</li>
                </ul>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="card card-body mb-4 card-style">
            <form action="{{ route('client_review.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mt-4 input-style-1">
                    <label for="shop_name">Shop Name <span class="text-danger">*</span></label>
                    <input type="text" id="shop_name" name="shop_name"
                        class="form-control @error('shop_name') is-invalid @enderror"
                        value="{{ old('shop_name') }}" placeholder="Enter Shop Name" required />
                    @error('shop_name')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="shop_location">Shop Location <span class="text-danger">*</span></label>
                    <input type="text" id="shop_location" name="shop_location"
                        class="form-control @error('shop_location') is-invalid @enderror"
                        value="{{ old('shop_location') }}" placeholder="Enter Shop Location" required />
                    @error('shop_location')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="review">Review <span class="text-danger">*</span></label>
                    <textarea id="review" name="review" rows="5"
                        class="form-control @error('review') is-invalid @enderror"
                        placeholder="Enter Client Review">{{ old('review') }}</textarea>
                    @error('review')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="rating">Rating (out of 5) <span class="text-danger">*</span></label>
                    <input type="number" id="rating" name="rating"
                        class="form-control @error('rating') is-invalid @enderror"
                        value="{{ old('rating') }}" placeholder="Enter Rating" min="1" max="5" required />
                    @error('rating')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="client_avatar">Client Image <span class="text-danger">*</span></label>
                    <input type="file" id="client_avatar" name="client_avatar"
                        class="form-control @error('client_avatar') is-invalid @enderror dropify" />
                    @error('client_avatar')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Create Review</button>
                    <a href="{{ route('client_review.index') }}" class="btn btn-danger me-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
