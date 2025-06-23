@extends('backend.app')

@section('title', 'Blog List')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <h1 class="my-1 text-dark fw-bold fs-2">Blogs</h1>
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Blog</li>
                </ul>
            </div>
            <!--end::Info-->
            <a href="{{ route('blog.create') }}" class="btn btn-primary">Create Blog</a>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="card p-5">
            <table class="table table-bordered data-table" id="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>Category</th>
                        <th>Title</th>
                        <th>Blog Creator</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('blog.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'category', name: 'category.name' },
                { data: 'title', name: 'title' },
                { data: 'blog_creator', name: 'blog_creator' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[5, 'desc']],
            language: {
                processing: `
                    <div class="text-center">
                        <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>`
            }
        });
    });

    // Delete confirmation
    function showDeleteConfirm(id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete the blog.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                deleteBlog(id);
            }
        });
    }

    function deleteBlog(id) {
        let url = '{{ route('blog.destroy', ':id') }}'.replace(':id', id);
        $.ajax({
            url: url,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(response) {
                $('#data-table').DataTable().ajax.reload();
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error('Failed to delete blog!');
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    }

    // Status change confirmation
    function showStatusChangeAlert(id) {
        event.preventDefault();
        Swal.fire({
            title: 'Are you sure?',
            text: 'You want to update the status?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'Yes',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.isConfirmed) {
                changeStatus(id);
            }
        });
    }

    function changeStatus(id) {
        let url = '{{ route('blog.status', ':id') }}'.replace(':id', id);
        $.get(url, function(response) {
            $('#data-table').DataTable().ajax.reload();
            if (response.success) {
                toastr.success(response.message);
            } else {
                toastr.error('Status update failed!');
            }
        }).fail(function() {
            toastr.error('Something went wrong!');
        });
    }
</script>
@endpush
