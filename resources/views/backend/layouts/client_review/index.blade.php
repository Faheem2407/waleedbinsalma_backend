@extends('backend.app')

@section('title', 'Client Review List')

@section('content')
    <!--begin::Toolbar-->
    <div class="toolbar" id="kt_toolbar">
        <div class="flex-wrap container-fluid d-flex flex-stack flex-sm-nowrap">
            <!--begin::Info-->
            <div class="flex-wrap d-flex flex-column align-items-start justify-content-center me-2">
                <h1 class="my-1 text-dark fw-bold fs-2">Client Reviews</h1>
                <ul class="my-1 breadcrumb fw-semibold fs-base">
                    <li class="breadcrumb-item text-muted">
                        <a href="{{ route('admin.dashboard') }}" class="text-muted text-hover-primary">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item text-muted">Client Review</li>
                </ul>
            </div>
            <!--end::Info-->
            <a href="{{ route('client_review.create') }}" class="btn btn-primary">Create Review</a>
        </div>
    </div>
    <!--end::Toolbar-->

    <div class="container-fluid">
        <div class="card p-5">
            <table class="table table-bordered data-table" id="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">#</th>
                        <th>Client Avatar</th>
                        <th>Review</th>
                        <th>Rating</th>
                        <th>Shop Name</th>
                        <th>Status</th>
                        <th style="width: 120px;">Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@push('script')
<script>
    $(document).ready(function () {
        $('#data-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('client_review.index') }}",
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'client_avatar', name: 'client_avatar', orderable: false, searchable: false },
                { data: 'review', name: 'review' },
                { data: 'rating', name: 'rating' },
                { data: 'shop_name', name: 'shop_name' },
                { data: 'status', name: 'status', orderable: false, searchable: false },
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
            text: 'This will permanently delete the review.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!',
        }).then((result) => {
            if (result.isConfirmed) {
                deleteReview(id);
            }
        });
    }

    function deleteReview(id) {
        let url = '{{ route('client_review.destroy', ':id') }}'.replace(':id', id);
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
                    toastr.error('Failed to delete review!');
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
        let url = '{{ route('client_review.status', ':id') }}'.replace(':id', id);
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
