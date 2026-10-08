@php($active = 'webhook-delivery')
@extends('admin.layouts.master')

@section('title', 'Webhook Delivery')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Webhook Delivery</h2>
            <a href="{{ route('admin.webhook-delivery.index') }}" class="text-light btn btn-sm btn-outline-secondary">
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
                                    <span class="info-box-text text-center text-muted">Webhook Delivery ID</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $webhookDelivery->id }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Tenant ID</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $webhookDelivery->tenant_id }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Merchant</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $webhookDelivery->event->merchant_id }} readonly
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
                                    <span class="info-box-text text-center text-muted">Event ID</span>
                                    <div class="input-group mt-2">
                                        <input type="text" class="form-control bg-secondary" value={{ $webhookDelivery->webhook_event_id }} readonly aria-label="Recipient's username" aria-describedby="button-addon2">
                                        <button class="btn btn-outline-primary button-view-detail" type="button" data-click-type="webhook-event" data-id="{{ $webhookDelivery->webhook_event_id }}" data-bs-toggle="modal" data-bs-target="#webhookDeliveryShowModal"><i class="fa-solid fa-eye me-1"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Endpoint Snapshot ID</span>
                                    <div class="input-group mt-2">
                                        <input type="text" class="form-control bg-secondary" value={{ $webhookDelivery->endpoint_snapshot_id }} readonly aria-label="Recipient's username" aria-describedby="button-addon2">
                                        <button class="btn btn-outline-primary button-view-detail" type="button" data-click-type="webhook-snapshot" data-id="{{ $webhookDelivery->endpoint_snapshot_id }}" data-bs-toggle="modal" data-bs-target="#webhookDeliveryShowModal"><i class="fa-solid fa-eye me-1"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Attempt Count</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $webhookDelivery->attempt_count }} readonly
                                    >
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
                                    <span class="info-box-text text-center text-muted">First Attempt At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($webhookDelivery->first_attempt_at)->format('Y-m-d H:i:s') }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Last Attempt At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($webhookDelivery->last_attempt_at)->format('Y-m-d H:i:s') }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Next Attempt At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($webhookDelivery->next_attempt_at)->format('Y-m-d H:i:s') }}"
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
                    <div class="row my-4">
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Delivered At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($webhookDelivery->delivered_at)->format('Y-m-d H:i:s') }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Failed At</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ Carbon\Carbon::parse($webhookDelivery->failed_at)->format('Y-m-d H:i:s') }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Last HTTP Status</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        value="{{ $webhookDelivery->last_http_status ?? '-' }}"
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
                                    <span class="info-box-text text-center text-muted">Last Error Code</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        value="{{ $webhookDelivery->last_error_code ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Last Response Duration</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ $webhookDelivery->last_response_duration_ms ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Status</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $webhookDelivery->status_label }} readonly
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
                                    <span class="info-box-text text-center text-muted">Event Type</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        value="{{ $webhookDelivery->event->event_type ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Aggregate Type</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $webhookDelivery->event->aggregate_type }} readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Aggregate ID</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2"
                                        value="{{ $webhookDelivery->event->aggregate_id ?? '-' }}"
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
                                    <span class="info-box-text text-center text-muted">Endpoint Name</span>
                                    <input
                                        type="text"
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        value="{{ $webhookDelivery->last_error_code ?? '-' }}"
                                        readonly
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Endpoint URL</span>
                                    <input
                                        type="text" class="form-control bg-secondary mt-2" value={{ $webhookDelivery->status_label }} readonly
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
                        <div class="col-12 col-sm-12">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Last Error Message</span>
                                    <textarea
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        rows="2"
                                        readonly
                                    >{{  $webhookDelivery->last_error_message ?? '-' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="row my-3">
                        <div class="col-12 col-sm-12">
                            <div class="info-box">
                                <div class="info-box-content">
                                    <span class="info-box-text text-center text-muted">Payload</span>
                                    <textarea
                                        class="form-control bg-secondary mt-2 font-monospace"
                                        rows="5"
                                        readonly
                                    >{{ $webhookDelivery->event->payload
                                        ? json_encode($webhookDelivery->event->payload, true, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                                        : '-' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="d-flex justify-content-end gap-2">
                    @if($webhookDelivery->status === 'pending' || $webhookDelivery->status === 'queued')
                        <button type="submit" class="btn btn-danger">
                            <i class="fa-solid fa-circle-xmark me-1"></i> Cancel Sending Webhook
                        </button>
                    @endif
                    @if($webhookDelivery->status === 'failed_permanently' || $webhookDelivery->status === 'cancelled')
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-refresh me-1"></i> Retry Webhook
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="webhookDeliveryShowModal" tabindex="-1" role="dialog" aria-labelledby="webhookDeliveryShowModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h5 class="modal-title" id="webhookDeliveryShowModalLabel"></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div></div>
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
        // Select2 tag input for allowed IPs.
        // Copy-to-clipboard buttons for the one-time credentials panel.
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

        $(document).ready(function () {

            function escapeHtml(value) {
                return String(value ?? '').replace(/[&<>"']/g, function (c) {
                    return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
                });
            }

            function buildEventDetailHtml(evt) {
                var rows = [
                    ['Event ID', evt.id  ?? '-'],
                    ['Tenant Code', evt.tenant_code ?? '-'],
                    ['Source System', evt.source_system ?? '-'],
                    ['Source ID', evt.source_instance_id ?? '-'],
                    ['Event Type', evt.event_type ?? '-'],
                    ['Merchant', evt.merchant_name ?? '-'],
                    ['Aggregate Type', evt.aggregate_type ?? '-'],
                    ['Aggregate ID', evt.aggregate_id ?? '-'],
                    ['Status', evt.delivery.status_label ?? '-'],
                    ['Last HTTP Status', evt.delivery.last_http_status ?? '-'],
                    ['Next Attempt At', evt.delivery.next_attempt_at ?? '-'],
                    ['Occurred At', evt.occurred_at ?? '-'],
                    ['Created At', evt.created_at ?? '-'],
                    ['API Version', evt.api_version ?? '-'],
                    ['Payload', evt.payload ? JSON.stringify(evt.payload, null, 2) : '-']
                ];

                return '<table class="table table-sm mb-0"><tbody>' +
                    rows.map(function (r) {
                        var value = r[0] === 'Payload'
                            ? '<pre class="mb-0 font-monospace" style="white-space: pre-wrap;">' +
                                escapeHtml(r[1]) +
                            '</pre>'
                            : escapeHtml(String(r[1]));

                        return '<tr>' +
                            '<th style="width:35%">' + escapeHtml(r[0]) + '</th>' +
                            '<td>' + value + '</td>' +
                            '</tr>';
                    }).join('') +
                    '</tbody></table>';
            }

            function buildSnapshotDetailHtml(snapshot) {
                var rows = [
                    ['Tenant', snapshot.tenant_id ?? '-'],
                    ['Legacy Webhook ID', snapshot.legacy_webhook_id ?? '-'],
                    ['Name', snapshot.name ?? '-'],
                    ['URL', snapshot.url ?? '-'],
                    ['Timeout (seconds)', snapshot.timeout_seconds ?? '-'],
                    ['Max Attempts', snapshot.max_attempts ?? '-'],
                    ['Status', snapshot.status ?? '-'],
                    ['Created At', snapshot.created_at ?? '-'],
                    ['Alert Config', snapshot.alert_configuration ? JSON.stringify(snapshot.alert_configuration, null, 2) : '-']
                ];

                return '<table class="table table-sm mb-0"><tbody>' +
                    rows.map(function (r) {
                        var value = r[0] === 'Alert Config'
                            ? '<pre class="mb-0 font-monospace" style="white-space: pre-wrap;">' +
                                escapeHtml(r[1]) +
                            '</pre>'
                            : escapeHtml(String(r[1]));

                        return '<tr>' +
                            '<th style="width:35%">' + escapeHtml(r[0]) + '</th>' +
                            '<td>' + value + '</td>' +
                            '</tr>';
                    }).join('') +
                    '</tbody></table>';
            }

            $(document).on('click', '.button-view-detail', function () {
                var $button = $(this);
                var id = $button.data('id');
                var clickType = $button.data('click-type');
                var $body = $('#webhookDeliveryShowModal .modal-body');

                if(clickType === 'webhook-snapshot')
                {
                    var url = "{{ url('admin/webhook-endpoint-snapshot') }}/" + id
                    $('#webhookDeliveryShowModalLabel')
                        .text('Webhook Snapshot Detail');

                } else {
                    var url = "{{ url('admin/webhook-event') }}/" + id
                    $('#webhookDeliveryShowModalLabel')
                        .text('Webhook Event Detail');
                }

                $body.html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function (response) {

                        if(clickType === 'webhook-snapshot')
                        {
                            $body.html(buildSnapshotDetailHtml(response.webhookEndpointSnapshot));
                        } else {
                            $body.html(buildEventDetailHtml(response.webhookEvent));
                        }
                    },
                    error: function () {
                        $body.html('<div class="alert alert-danger mb-0">Unable to load event details.</div>');
                    }
                });
            })
        })
    });

</script>
@endsection