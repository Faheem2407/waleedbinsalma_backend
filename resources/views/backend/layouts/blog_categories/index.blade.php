@extends('backend.app')

@section('title', 'Blog Categories')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <h1 class="my-1 text-dark fw-bold fs-2">
                    Dashboard
                </h1>
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item text-muted">CMS</li>
                    <li class="breadcrumb-item text-muted">Blog Categories</li>
                </ul>
            </div>
            <!--end::Info-->

            <div class="d-flex align-items-center">
                <a href="{{ route('blogCategory.create') }}" class="btn btn-primary">Add Category</a>
            </div>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="card p-5">
            <table class="table table-bordered data-table" id="data-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">Serial No.</th>
                        <th>Name</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 150px;">Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#data-table')) {
            $('#data-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: "{{ route('blogCategory.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'name', name: 'name' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ],
                language: {
                    processing: `
                        <div class="text-center">
                            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>`
                }
            });
        }

        // Status change
        function statusChange(id) {
            let url = '{{ route('blogCategory.status', ':id') }}'.replace(':id', id);
            $.ajax({
                type: "GET",
                url: url,
                success: function(resp) {
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success) {
                        toastr.success(resp.message);
                    } else {
                        toastr.error(resp.errors ? resp.errors[0] : resp.message);
                    }
                },
                error: function() {
                    toastr.error('Something went wrong.');
                }
            });
        }

        // Show status confirm dialog
        window.showStatusChangeAlert = function(id) {
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
                    statusChange(id);
                }
            });
        }

        // Delete confirm dialog
        window.showDeleteConfirm = function(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure you want to delete this record?',
                text: 'If you delete this, it will be gone forever.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }

        // Delete item
        function deleteItem(id) {
            let url = '{{ route('blogCategory.destroy', ':id') }}';
            let csrfToken = '{{ csrf_token() }}';
            $.ajax({
                type: "DELETE",
                url: url.replace(':id', id),
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(resp) {
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success === true) {
                        toastr.success(resp.message);
                    } else if (resp.errors) {
                        toastr.error(resp.errors[0]);
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error: function() {
                    toastr.error('Something went wrong.');
                }
            });
        }
    });
</script>
@endpush
