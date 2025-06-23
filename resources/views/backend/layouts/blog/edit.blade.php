@extends('backend.app')

@section('title', 'Edit Blog')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <h1 class="my-1 text-dark fw-bold fs-2">Blog</h1>
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('blog.index') }}" class="text-muted text-hover-primary">Blogs</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Edit</li>
                </ul>
            </div>
            <a href="{{ route('blog.index') }}" class="btn btn-danger">Back to List</a>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="card card-body mb-4 card-style">
            <form action="{{ route('blog.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mt-4 input-style-1">
                    <label for="blog_category_id">Category <span class="text-danger">*</span></label>
                    <select id="blog_category_id" name="blog_category_id" 
                        class="form-control @error('blog_category_id') is-invalid @enderror" required>
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ (old('blog_category_id', $blog->blog_category_id) == $category->id) ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('blog_category_id')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="title">Title <span class="text-danger">*</span></label>
                    <input type="text" id="title" name="title" 
                        class="form-control @error('title') is-invalid @enderror"
                        value="{{ old('title', $blog->title) }}" placeholder="Enter Blog Title" required />
                    @error('title')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="image">Image</label>
                    <input type="file" id="image" name="image" 
                        class="form-control @error('image') is-invalid @enderror dropify" data-default-file="{{ asset($blog->image) }}" />
                    @error('image')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" 
                        class="form-control @error('description') is-invalid @enderror" placeholder="Enter Blog Description">{{ old('description', $blog->description) }}</textarea>
                    @error('description')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4 input-style-1">
                    <label for="blog_creator">Blog Creator</label>
                    <input type="text" id="blog_creator" name="blog_creator" 
                        class="form-control @error('blog_creator') is-invalid @enderror"
                        value="{{ old('blog_creator', $blog->blog_creator) }}" placeholder="Enter Blog Creator Name" />
                    @error('blog_creator')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Update Blog</button>
                    <a href="{{ route('blog.index') }}" class="btn btn-danger me-2">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
