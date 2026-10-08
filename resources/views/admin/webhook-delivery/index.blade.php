@php($active = 'webhook-delivery')
@extends('admin.layouts.master')

@section('title', 'Webhook Deliveries')


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
            <h2 class="h6 m-0">Webhook Deliveries</h2>
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

                <div class="form-group">
                    <label for="event_status" class="form-label small">Event Status</label>
                    <select class="form-select form-select-sm @error('event_status') is-invalid @enderror" id="event_status"
                            name="event_status">
                        <option value="">All</option>
                        @foreach ($statuses as $status)
                            <option value="{{ $status }}" @selected(old('event_status') === $status)>{{ $status }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="event_type" class="form-label small">Event Type</label>
                    <select class="form-select form-select-sm @error('event_type') is-invalid @enderror" id="event_type"
                            name="event_type">
                        <option value="">All</option>
                        @foreach ($events as $event)
                            <option value="{{ $event }}" @selected(old('event_type') === $event)>{{ $event }}</option>
                        @endforeach
                    </select>
                </div>

                <div class='form-group d-flex bg-secondary p-2 rounded-3 gap-2'>
                    <div class="form-group">
                        <label for="date_type" class="form-label small">Date </label>
                        <select class="form-select form-select-sm @error('date_type') is-invalid @enderror" id="date_type"
                                name="date_type">
                            @foreach ($datesColumns as $idx => $datesColumn)
                                <option value="{{ $idx }}" @selected(old('date_type') === $idx)>{{ $datesColumn['label'] }}</option>
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
                        </div>
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
                <button class="btn btn-sm btn-primary mb-4 delivery-filter-btn">
                    <i class="fa-solid fa-sliders me-1"></i> Filter
                </button>
                <button class="btn btn-sm btn-danger mb-4 text-white clear-delivery-filter-btn">
                    <i class="fa-solid fa-close me-1"></i> Reset Filter
                </button>
            </div>


            <div class="table-responsive"><table id="webhook-delivery-table" class="table table-hover table-responsive w-100">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Tenant</th>
                        <th>Event ID</th>
                        <th>Endpoint Snapshot ID</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Attempt</th>
                        <th>First Attempt At</th>
                        <th>Last Attempt At</th>
                        <th>Next Attempt At</th>
                        <th>Delivered At</th>
                        <th>Failed At</th>
                        <th>Last Http Status</th>
                        <th>Last Error Code</th>
                        <th>Last Error Message</th>
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
        $('#webhook-delivery-table').DataTable({
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
                url: "{{ route('admin.webhook-delivery.datatable') }}",
                type: "GET",
                data: function(d){
                    console.log($('#date_type').val())
                    d.tenant = $('#tenant').val();
                    d.event_status = $('#event_status').val();
                    d.event_type = $('#event_type').val();
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
            columnDefs: [
                { orderable: true, targets: [15] },
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'tenant_id', name: 'tenant_id', orderable: true, searchable: true },
                { data: 'webhook_event_id', name: 'webhook_event_id', orderable: true, searchable: true },
                { data: 'endpoint_snapshot_id', name: 'endpoint_snapshot_id', orderable: true, searchable: true },
                { data: 'event_type', name: 'event_type', orderable: true, searchable: true },
                { data: 'status', name: 'status', orderable: true, searchable: true },
                { data: 'attempt_count', name: 'attempt_count', orderable: true, searchable: true },
                { data: 'first_attempt_at', name: 'first_attempt_at', orderable: true, searchable: true },
                { data: 'last_attempt_at', name: 'last_attempt_at', orderable: true, searchable: true },
                { data: 'next_attempt_at', name: 'next_attempt_at', orderable: true, searchable: true },
                { data: 'delivered_at', name: 'delivered_at', orderable: true, searchable: true },
                { data: 'failed_at', name: 'failed_at', orderable: true, searchable: true },
                { data: 'last_http_status', name: 'last_http_status', orderable: true, searchable: true },
                { data: 'last_error_code', name: 'last_error_code', orderable: true, searchable: true },
                { data: 'last_error_message', name: 'last_error_message', orderable: true, searchable: true },
                { data: 'created_at', name: 'created_at', orderable: true, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false },
            ],
        });

        $('table#webhook-delivery-table').css('font-size', '14px');

        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (c) {
                return { '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#39;' }[c];
            });
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

        $(document).on('click', '.delivery-filter-btn', function (e) {
            $('#webhook-delivery-table').DataTable().ajax.reload();
            e.preventDefault();
        });

        $(document).on('click', '.clear-delivery-filter-btn', function (e) {
            $('#tenant').val('');
            $('#event_status').val('');
            $('#event_type').val('');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#daterange-filter').val('');
            $('#webhook-delivery-table').DataTable().ajax.reload();

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