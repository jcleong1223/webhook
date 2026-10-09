@php($active = 'endpoint-health')
@extends('admin.layouts.master')

@section('title', 'Endpoint Health')

@section('content')
    <div class="card overflow-hidden mb-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Endpoint Health</h2>
            <a href="{{ route('admin.endpoint-health.index') }}" class="text-light btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1 "></i> Back
            </a>
        </div>

        <div class="card-body card-bg-secondary">
            @if (session('new_client_id') && session('new_client_secret'))
                <div class="alert alert-success border-success">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fa-solid fa-circle-check me-2"></i>
                        <strong>API client created successfully.</strong>
                    </div>
                    <p class="mb-2 small">
                        Copy the credentials below now. For security, the <strong>secret</strong> is stored
                        encrypted and <strong>will not be shown again</strong>.
                    </p>
                    <div class="mb-2">
                        <label class="form-label small fw-semibold mb-1">Client ID</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace bg-light text-dark" id="new-client-id"
                                   value="{{ session('new_client_id') }}" readonly>
                            <button class="btn btn-outline-secondary" type="button"
                                    data-copy-target="#new-client-id">Copy</button>
                        </div>
                    </div>
                    <div>
                        <label class="form-label small fw-semibold mb-1">Client Secret</label>
                        <div class="input-group">
                            <input type="text" class="form-control font-monospace bg-light text-dark" id="new-client-secret"
                                   value="{{ session('new_client_secret') }}" readonly>
                            <button class="btn btn-outline-secondary" type="button"
                                    data-copy-target="#new-client-secret">Copy</button>
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row mb-4">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Endpoint Health ID</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $endpointHealth->health_id ?? '-' }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Tenant ID</span>
                                    <input
                                        type="text" id='tn_id' class="form-control bg-secondary mt-2" value={{ $endpointHealth->tenant_id ?? '-' }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Legacy Webhook ID</span>
                                    <input
                                        type="text" id='legacy_webhk_id' class="form-control bg-secondary mt-2" value={{ $endpointHealth->legacy_webhook_id ?? '-' }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row my-3">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Name</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ $endpointHealth->snapshot_name ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">URL</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ $endpointHealth->snapshot_url ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Status</span>
                                    <div class="input-group mt-2">
                                        <?php
                                            $colorLabel = match ($endpointHealth->health_status) {
                                                App\Domain\Webhooks\Enums\EndpointHealthStatus::HEALTHY->value => 'text-success',
                                                App\Domain\Webhooks\Enums\EndpointHealthStatus::DEGRADED->value => 'text-warning',
                                                App\Domain\Webhooks\Enums\EndpointHealthStatus::DOWN->value => 'text-danger',
                                                App\Domain\Webhooks\Enums\EndpointHealthStatus::RECOVERED->value => 'text-success',
                                                default => 'text-muted',
                                            };
                                        ?>
                                        <input type="text" class="form-control bg-secondary {{ $colorLabel }}" value={{ $endpointHealth->health_status ?? '-' }} readonly aria-label="health status" aria-describedby="button-addon2">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row my-4">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Recent Success Count</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $endpointHealth->recent_success_count ?? '-' }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Last Success At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($endpointHealth->last_success_at)->format('Y-m-d H:i:s') ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Consecutive Failures</span>
                                    <div class="input-group mt-2">
                                        <input type="text" class="form-control bg-secondary" value={{ $endpointHealth->consecutive_failures ?? '-' }} readonly aria-label="Consec failure" aria-describedby="button-addon2">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row my-4">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Last Failure At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($endpointHealth->last_failure_at)->format('Y-m-d H:i:s') ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Recovered At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($endpointHealth->recovered_at)->format('Y-m-d H:i:s') ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Last Alert At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        value="{{ Carbon\Carbon::parse($endpointHealth->last_alert_at)->format('Y-m-d H:i:s') ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row my-3">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Degraded At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($endpointHealth->degraded_at)->format('Y-m-d H:i:s') }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Created At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($endpointHealth->health_created_at)->format('Y-m-d H:i:s') ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Number of Retry Deliveries</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="-"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row my-3">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Number of Permanently Failed Deliveries</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        value="-"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card overflow-hidden mt-5">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Endpoint Snapshots List</h2>
            <a class="btn btn-outline-secondary text-light endpointCollapsible"
                data-bs-toggle="collapse"
                href="#collapseEndpointSnapshots"
                role="button"
                aria-expanded="false"
                aria-controls="collapseEndpointSnapshots"
            >
                Expand
            </a>
        </div>


        <div class="collapse" id="collapseEndpointSnapshots">
            <div class="card card-body">
                <div class="table-responsive">
                    <table id="endpoint-snapshots-table" class="table table-hover table-responsive w-100">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Tenant</th>
                                <th>Legacy Webhook ID</th>
                                <th>Name</th>
                                <th>URL</th>
                                <th>Status</th>
                                <th>Timeout (seconds)</th>
                                <th>Max Attempts</th>
                                <th>Alert Config</th>
                                <th>Created At</th>

                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="alertConfigShowModal" tabindex="-1" role="dialog" aria-labelledby="alertConfigShowModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h5 class="modal-title" id="alertConfigShowModalLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="mb-0"><code id="alertConfigContent" class="text-break"></code></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('script')
<script>
    document.addEventListener('DOMContentLoaded', function () {

        $('#endpoint-snapshots-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            paging: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 10,
            searching: true,
            ordering: true,
            order: [
                [1, 'desc']
            ],
            info: true,
            dom: 'Bfrtip',
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.webhook-endpoint-snapshot.datatable') }}",
                type: "GET",
                data: function(d) {
                    d.tenant = $('#tn_id').val();
                    d.legacy_webhook_id = $('#legacy_webhk_id').val();
                    d.date_type = $('#date_type').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                },
            },
            layout: {
                topStart: 'buttons',
                topEnd: 'search',
                bottomStart: ['pageLength', 'info'],
                bottomEnd: 'paging',
            },
            buttons: [{
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'excel',
                    footer: true,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                {
                    extend: 'csv',
                    footer: true,
                    exportOptions: {
                        columns: ':visible'
                    }
                },
                'colvis',
            ],
            columnDefs: [{
                orderable: true,
                targets: [9]
            }, ],
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'tenant_id',
                    name: 'tenant_id',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'legacy_webhook_id',
                    name: 'legacy_webhook_id',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'name',
                    name: 'name',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'url',
                    name: 'url',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'status',
                    name: 'status',
                    orderable: true,
                    searchable: true
                },
                {
                    data: 'timeout_seconds',
                    name: 'timeout_seconds',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'max_attempts',
                    name: 'max_attempts',
                    orderable: true,
                    searchable: false
                },
                {
                    data: 'alert_configuration',
                    name: 'alert_configuration',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'created_at',
                    name: 'created_at',
                    orderable: true,
                    searchable: false
                },
            ],
        });

        document.querySelectorAll('[data-copy-target]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var input = document.querySelector(btn.getAttribute('data-copy-target'));
                if (!input) {
                    return;
                }

                navigator.clipboard.writeText(input.value).then(function () {
                    if (window.Swal) {
                        window.Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Copied to clipboard',
                            showConfirmButton: false,
                            timer: 1500,
                        });
                    }
                });
            });
        });

        var endpointCollapsible = document.getElementById('collapseEndpointSnapshots');
        var btn = document.querySelector('.endpointCollapsible');

        endpointCollapsible.addEventListener('shown.bs.collapse', function () {
            btn.textContent = 'Collapse';
        });
        endpointCollapsible.addEventListener('hidden.bs.collapse', function () {
            btn.textContent = 'Expand';
        });

        $(document).ready(function () {

            $('table#endpoint-snapshots-table').css('font-size', '14px');

            $(document).on('click', '.snapshot-config-btn', function () {
                var raw = $(this).attr('data-snapshot-config');
                var $target = $('#alertConfigContent');

                if (!$target.length) {
                    return;
                }

                try {
                    $target.text(JSON.stringify(JSON.parse(raw), null, 2));
                } catch (err) {
                    $target.text(raw || '');
                }
            });

        })
    });

</script>
@endsection