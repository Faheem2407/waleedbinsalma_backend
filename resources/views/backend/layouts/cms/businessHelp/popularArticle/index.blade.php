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
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">
                            Home
                        </a>
                    </li>
                    <li class="breadcrumb-item text-muted">CMS</li>
                    <li class="breadcrumb-item text-muted">Business Help</li>
                    <li class="breadcrumb-item text-muted">Popular Articles</li>
                </ul>
            </div>
            <!--end::Info-->
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="card p-5">
            <div class="mb-3 d-flex justify-content-end">
                <a href="{{ route('businessHelp.popularArticles.create') }}" class="btn btn-primary"> Add New Section </a>
            </div>

            <table class="table table-bordered data-table" id="data-table">
                <thead>
                    <tr>
                        <th style="width: 100px">Serial No.</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
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
                    ajax: "{{ route('businessHelp.popularArticles.index') }}",
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'title', name: 'title' },
                        { data: 'description', name: 'description' },
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
        });

        // Status Change Function
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
                    statusChange(id);
                }
            });
        }

        function statusChange(id) {
            let url = '{{ route('businessHelp.popularArticles.status', ':id') }}'.replace(':id', id);
            $.ajax({
                type: "GET",
                url: url,
                success: function (resp) {
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success) {
                        toastr.success(resp.message);
                    } else {
                        toastr.error(resp.errors ? resp.errors[0] : resp.message);
                    }
                },
                error: function () {
                    toastr.error('Something went wrong.');
                }
            });
        }

        // Delete Function
        function showDeleteConfirm(id) {
            event.preventDefault();
            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the section.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteItem(id);
                }
            });
        }

        function deleteItem(id) {
            let url = '{{ route('businessHelp.popularArticles.destroy', ':id') }}'.replace(':id', id);
            $.ajax({
                type: "DELETE",
                url: url,
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (resp) {
                    $('#data-table').DataTable().ajax.reload();
                    if (resp.success) {
                        toastr.success(resp.message);
                    } else {
                        toastr.error(resp.errors ? resp.errors[0] : resp.message);
                    }
                },
                error: function (error) {
                    toastr.error('Something went wrong.');
                }
            });
        }
    </script>
@endpush