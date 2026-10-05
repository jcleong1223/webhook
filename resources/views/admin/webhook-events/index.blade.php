@php($active = 'webhook-events')
@extends('admin.layouts.master')

@section('title', 'Webhook Events')

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Webhook Events</h2>
        </div>

        <div class="card-body card-bg-secondary ">
            <div class="table-responsive"><table id="webhook-event-table" class="table table-hover table-responsive mb-0 w-100">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Occurred At</th>
                        <th>Tenant</th>
                        <th>Merchant</th>
                        <th>Event Type</th>
                        <th>Aggregate ID</th>
                        <th>Source System</th>
                        <th>Payload</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table></div>

        </div>
    </div>

    <div class="modal fade" id="webhookEventPayloadModal" tabindex="-1" role="dialog" aria-labelledby="webhookEventPayloadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h5 class="modal-title" id="webhookEventPayloadModalLabel">Webhook Event Payload</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre class="mb-0"><code id="webhookEventPayloadContent" class="text-break"></code></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="webhookEventViewModal" tabindex="-1" role="dialog" aria-labelledby="webhookEventViewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h5 class="modal-title" id="webhookEventViewModalLabel">Webhook Event Detail</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div><p>Text</p></div>
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
        $('#webhook-event-table').DataTable({
            processing: true,
            serverSide: true,
            stateSave: true,
            paging: true,
            lengthChange: true,
            lengthMenu: [10, 25, 50, 100],
            pageLength: 25,
            searching: true,
            ordering: true,
            order: [[1, 'desc']],
            info: true,
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.webhook-events.datatable') }}",
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
                { orderable: true, targets: [1] },
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'occurred_at', name: 'occurred_at', orderable: true, searchable: true },
                { data: 'tenant_code', name: 'tenant_code', orderable: true, searchable: true },
                { data: 'merchant_name', name: 'merchant_name', orderable: true, searchable: true },
                { data: 'event_type', name: 'event_type', orderable: true, searchable: true },
                { data: 'aggregate_id', name: 'aggregate_id', orderable: true, searchable: true },
                { data: 'source_system', name: 'source_system', orderable: true, searchable: true },
                { data: 'payload', name: 'payload', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (c) {
                return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
            });
        }

        function buildDetailHtml(evt) {
            var rows = [
                ['Event ID', evt.id],
                ['Tenant Code', evt.tenant_code],
                ['Source System', evt.source_system],
                ['Source ID', evt.source_instance_id],
                ['Event Type', evt.event_type],
                ['Merchant', evt.merchant_name],
                ['Aggregate Type', evt.aggregate_type],
                ['Aggregate ID', evt.aggregate_id],
                ['Occurred At', evt.occurred_at],
                ['Created At', evt.created_at],
                ['api_version', evt.api_version],
            ];

            return '<table class="table table-sm mb-0"><tbody>' +
                rows.map(function (r) {
                    return '<tr><th style="width:35%">' + escapeHtml(r[0]) +
                        '</th><td>' + escapeHtml(r[1]) + '</td></tr>';
                }).join('') +
                '</tbody></table>';
        }

        document.addEventListener('click', function (e) {
            var btn = e.target.closest('.webhook-event-payload-btn');
            if (!btn) {
                return;
            }

            var raw = btn.getAttribute('data-webhook_payload');
            var target = document.getElementById('webhookEventPayloadContent');
            if (!target) {
                return;
            }

            try {
                target.textContent = JSON.stringify(JSON.parse(raw), null, 2);
            } catch (err) {
                target.textContent = raw || '';
            }
        });

        $('#webhookEventViewModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var id     = button.data('id');
        var $body  = $(this).find('.modal-body');

        $body.html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');

        $.ajax({
            url: "{{ url('admin/webhook-events') }}/" + id,
            type: 'GET',
            dataType: 'json',
            headers: {
                'Accept': 'application/json'
            },
            success: function (response) {
                $body.html(buildDetailHtml(response.webhookEvent));
            },
            error: function () {
                $body.html('<div class="alert alert-danger mb-0">Unable to load event details.</div>');
            }
        });
    });
    });
</script>
@endsection