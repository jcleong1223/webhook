@php($active = 'webhook-event')
@extends('admin.layouts.master')

@section('title', 'Webhook Events')


@push('styles')
<style>
        /* Date range input */
        #daterange-filter {
            color: #f6eeee !important;
        }

        /* Calendar dates */
        .daterangepicker .calendar-table td {
            color: #000 !important;
        }

        /* Calendar day names + month */
        .daterangepicker .calendar-table th {
            color: #000 !important;
        }

        /* Dates from previous/next month */
        .daterangepicker .calendar-table td.off {
            color: #000 !important;
        }

        /* Preset ranges: Today, Yesterday, Last 7 Days, etc. */
        .daterangepicker .ranges li {
            color: #000 !important;
        }

        /* Preset range hover */
        .daterangepicker .ranges li:hover {
            color: #000 !important;
        }

        /* Active preset */
        .daterangepicker .ranges li.active {
            color: #fff !important;
        }

        /* Selected calendar dates */
        .daterangepicker td.active,
        .daterangepicker td.active:hover {
            color: #fff !important;
        }

        /* Dates inside selected range */
        .daterangepicker td.in-range {
            color: #000 !important;
        }

        /* Apply / Cancel buttons */
        .daterangepicker .drp-buttons button {
            color: #000 !important;
        }

        .daterangepicker .drp-buttons .applyBtn {
            color: #fff !important;
        }
    </style>
@endpush

@section('content')
    <div class="card overflow-hidden">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h2 class="h6 m-0">Webhook Events</h2>
        </div>

        <div class="card-body card-bg-secondary ">
            <div class="d-flex align-items-center justify-content-start gap-3 mb-2">
                <div class="form-group">
                    <label for="tenant" class="form-label small">Tenant</label>
                    <select class="form-select form-select-sm @error('tenant') is-invalid @enderror" id="tenant"
                            name="tenant">
                        <option value="">All</option>
                        @foreach ($tenants as $tenant)
                            <option value="{{ $tenant }}" @selected(old('tenant') === $tenant)>{{ $tenant }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group ">
                    <label for="merchant" class="form-label small">Merchant</label>
                    <select class="form-select form-select-sm @error('merchant') is-invalid @enderror" id="merchant"
                            name="merchant">
                        <option value="">All</option>
                        @foreach ($merchants as $merchant)
                            <option value="{{ $merchant }}" @selected(old('merchant') === $merchant)>{{ $merchant }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="event_type" class="form-label small">Event Type</label>
                    <select class="form-select form-select-sm @error('event_type') is-invalid @enderror" id="event_type"
                            name="event_type">
                        <option value="">All</option>
                        @foreach ($eventTypes as $eventType)
                            <option value="{{ $eventType }}" @selected(old('event_type') === $eventType)>{{ $eventType }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="bootstrap-timepicker">
                    <div class="form-group">
                        <label for="start_date" class="form-label small">Start Date</label>
                        <div class="input-group input-group-sm date" id='startdatetimepicker' data-target-input="nearest">
                            <input type="date"
                                class="form-control"
                                data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy"
                                data-mask name="start_date" id="start_date" data-target="#startdatetimepicker" value="{{old('start_date')}}" required>
                        </div>
                    </div>
                </div>
                <div class="bootstrap-timepicker">
                    <div class="form-group">
                        <label for="end_date" class="form-label small">End Date</label>
                        <div class="input-group input-group-sm date" id='enddatetimepicker' data-target-input="nearest">
                            <input type="date"
                                class="form-control "
                                data-inputmask-alias="datetime" data-inputmask-inputformat="dd/mm/yyyy"
                                data-mask name="end_date" id="end_date" data-target="#enddatetimepicker" value="{{old('end_date')}}" required>
                        </div>
                        <!-- /.input group -->
                    </div>
                </div>

                {{-- <div class="form-group">
                    <label for="daterange-filter" class="form-label small">Date Range</label>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fa-solid fa-calendar"></i></span>
                        <input type="text" class="form-control" id="daterange-filter" name="daterange"
                               autocomplete="off" placeholder="Select date range">
                    </div>
                    <input type="hidden" name="start_date" id="start_date" value="{{ old('start_date') }}">
                    <input type="hidden" name="end_date" id="end_date" value="{{ old('end_date') }}">
                </div> --}}
            </div>
            <div class="d-flex align-items-center justify-content-start gap-3 mb-2">
                <button class="btn btn-sm btn-primary mb-4 event-filter-btn">
                <i class="fa-solid fa-sliders me-1"></i> Filter
            </button>
            <button class="btn btn-sm btn-danger mb-4 text-white clear-event-filter-btn">
                <i class="fa-solid fa-close me-1"></i> Reset Filter
            </button>
            </div>


            <div class="table-responsive"><table id="webhook-event-table" class="table table-hover table-responsive w-100">
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
                        <th>Status</th>
                        <th>Last HTTP Status</th>
                        <th>Next Attempt At</th>
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
            dom: 'Bfrtip',
            autoWidth: false,
            ajax: {
                url: "{{ route('admin.webhook-event.datatable') }}",
                type: "GET",
                data: function(d){
                    console.log($('#tenant').val());
                    d.tenant = $('#tenant').val();
                    d.merchant = $('#merchant').val();
                    d.event_type = $('#event_type').val();
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
                { data: 'status', name: 'status', orderable: true, searchable: false },
                { data: 'last_http_status', name: 'last_http_status', orderable: true, searchable: false },
                { data: 'next_attempt_at', name: 'next_attempt_at', orderable: true, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $('table#webhook-event-table').css('font-size', '14px');

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (c) {
                return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
            });
        }

        function buildDetailHtml(evt) {
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
                ['api_version', evt.api_version ?? '-'],
            ];

            return '<table class="table table-sm mb-0"><tbody>' +
                rows.map(function (r) {
                    return '<tr><th style="width:35%">' + escapeHtml(r[0]) +
                        '</th><td>' + escapeHtml(r[1]) + '</td></tr>';
                }).join('') +
                '</tbody></table>';
        }

        $(document).on('click', '.webhook-event-payload-btn', function () {
            var raw = $(this).attr('data-webhook_payload');
            var $target = $('#webhookEventPayloadContent');

            if (!$target.length) {
                return;
            }

            try {
                $target.text(JSON.stringify(JSON.parse(raw), null, 2));
            } catch (err) {
                $target.text(raw || '');
            }
        });

        $('#webhookEventViewModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget)
            var id     = button.data('id');
            var $body  = $(this).find('.modal-body');

            $body.html('<div class="text-center py-4"><div class="spinner-border" role="status"></div></div>');

            $.ajax({
                  url: "{{ url('admin/webhook-event') }}/" + id,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json'
                },
                success: function (response) {
                    console.log(response)
                    $body.html(buildDetailHtml(response.webhookEvent));
                },
                error: function () {
                    $body.html('<div class="alert alert-danger mb-0">Unable to load event details.</div>');
                }
            });
        });

        $(document).on('click', '.event-filter-btn', function (e) {

            $('#webhook-event-table').DataTable().ajax.reload();
            e.preventDefault();
        });

        $(document).on('click', '.clear-event-filter-btn', function (e) {
            $('#tenant').val('');
            $('#merchant').val('');
            $('#event_type').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#daterange-filter').val('');
            $('#webhook-event-table').DataTable().ajax.reload();

            e.preventDefault();
        });

        var drpConfig = {
            opens: 'right',
            startDate: moment().startOf('month'),
            endDate: moment().endOf('month'),
            minDate: '01/01/2013',
            maxDate: '12/31/' + new Date().getFullYear(),
            dateLimit: {
                days: 365
            },
            showDropdowns: false,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract('days', 1), moment().subtract('days', 1)],
                'Last 7 Days': [moment().subtract('days', 6), moment()],
                'Last 30 Days': [moment().subtract('days', 29), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract('month', 1).startOf('month'), moment().subtract('month', 1).endOf('month')]
            },
            buttonClasses: ['btn btn-sm'],
            applyClass: ' blue',
            cancelClass: 'default',
            format: 'MM/DD/YYYY',
            separator: ' to ',
            locale: {
                applyLabel: 'Apply',
                fromLabel: 'From',
                toLabel: 'To',
                customRangeLabel: 'Custom Range',
                daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                firstDay: 1
            }
        };

        function applyDateRange(start, end) {
            $('#start_date').val(start.format('YYYY-MM-DD'));
            $('#end_date').val(end.format('YYYY-MM-DD'));
            $('#daterange-filter').val(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }

        $('#daterange-filter').daterangepicker(drpConfig, function(start, end) {
            applyDateRange(start, end);
        });

        // Seed the visible input + hidden fields with the picker's default range (this month).
        applyDateRange(drpConfig.startDate, drpConfig.endDate);
    });
</script>
@endsection