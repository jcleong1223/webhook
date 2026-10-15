@php($active = 'api-client')
@extends('admin.layouts.master')

@section('title', 'API Clients')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">API Clients</h2>
            @can('api-client.create')
                <a href="{{ route('admin.api-client.create') }}" class="btn btn-sm btn-primary">Create API Client</a>
            @endcan
        </div>

        <div class="card-body card-bg-secondary ">
            <div class="table-responsive"><table id="api-client-table" class="table table-hover table-responsive mb-0 w-100">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Name</th>
                        <th>Client ID</th>
                        <th>Status</th>
                        <th>Allowed IPs</th>
                        <th>Last Used</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table></div>

        </div>
    </div>
@endsection


@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        $('#api-client-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            paging: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.api-client.datatable') }}",
                type: "GET",
            },
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: ['pageLength', 'info'],
                bottomEnd: 'paging',
            },
            buttons: ['csv', 'excel', 'print'],
            columnDefs: [
                { orderable: false, targets: [7] },
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'client_id', name: 'client_id' },
                { data: 'status', name: 'status' },
                { data: 'allowed_ips', name: 'allowed_ips', orderable: false },
                { data: 'last_used_at', name: 'last_used_at', orderable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $(document).on('click', '.delete-api-client', function (e) {
            e.preventDefault();
            var id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('admin.api-client.destroy', ':id') }}".replace(':id', id),
                        method: 'DELETE',
                        dataType: 'json',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function (response) {
                            Swal.fire(
                                'Deleted!',
                                response.message || 'API client has been deleted.',
                                'success'
                            );
                            $('#api-client-table').DataTable().ajax.reload();
                        },
                        error: function () {
                            Swal.fire(
                                'Error!',
                                'Unable to delete the api client.',
                                'error'
                            );
                        }
                    });
                }
            });
        })

    });
</script>
@endsection