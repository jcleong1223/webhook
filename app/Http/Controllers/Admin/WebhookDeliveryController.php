<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Webhooks\Models\WebhookEvent;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class WebhookDeliveryController extends Controller
{
    protected $folder = '';

    public function __construct()
    {
        $this->folder = 'admin.webhook-delivery';
    }

    public function index()
    {
        $tenants = WebhookDelivery::query()
            ->whereNotNull('tenant_id')
            ->distinct()
            ->orderBy('tenant_id')
            ->pluck('tenant_id');

        $statuses = WebhookDelivery::query()
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $events = WebhookEvent::query()
            ->whereNotNull('event_type')
            ->whereHas('webhookDelivery')
            ->distinct()
            ->pluck('event_type');

        $datesColumns = WebhookDelivery::$dateFields;
        return view($this->folder . '.index', compact('tenants', 'statuses', 'datesColumns', 'events'));
    }


    public function webhookDeliveriesDatatable(Request $request)
    {

        $webhookDelivery = WebhookDelivery::query()
                            ->select('webhook_deliveries.*')
                            ->leftJoin(
                                'webhook_events',
                                'webhook_deliveries.webhook_event_id',
                                '=',
                                'webhook_events.id'
                            )
                            ->with(['event'])
                            ->when($request->tenant, function ($query) use ($request) {
                                return $query->where(
                                    'webhook_deliveries.tenant_id',
                                    $request->tenant
                                );
                            })
                            ->when($request->event_type, function ($query) use ($request) {
                                return $query->where('event_type', $request->event_type);
                            })
                            ->when($request->event_status, function ($query) use ($request) {
                                return $query->where('status', $request->event_status);
                            })
                            ->when($request->date_type, function ($query) use ($request) {

                                $dateColumn = match ($request->date_type) {
                                    'first_attempt_at' => 'webhook_deliveries.first_attempt_at',
                                    'last_attempt_at'  => 'webhook_deliveries.last_attempt_at',
                                    'next_attempt_at'  => 'webhook_deliveries.next_attempt_at',
                                    'delivered_at'     => 'webhook_deliveries.delivered_at',
                                    'failed_at'        => 'webhook_deliveries.failed_at',
                                    default            => 'webhook_deliveries.created_at',
                                };

                                if ($request->start_date && $request->end_date) {
                                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                                    $endDate = Carbon::parse($request->end_date)->endOfDay();

                                    return $query
                                        ->where($dateColumn, '>=', $startDate)
                                        ->where($dateColumn, '<=', $endDate);
                                }
                            });

        return DataTables::of($webhookDelivery)
            ->addIndexColumn()
            ->editColumn('event_type', function ($webhookDelivery) {
                return $webhookDelivery->event ? $webhookDelivery->event->event_type : '-';
            })
            ->editColumn('status', function ($webhookDelivery) {
                switch ($webhookDelivery->status) {
                    case WebhookDelivery::STATUS_PENDING:
                        return '<span class="badge badge-sm badge-secondary">Pending</span>';
                    case WebhookDelivery::STATUS_QUEUED:
                        return '<span class="badge badge-sm badge-secondary">Queued</span>';
                    case WebhookDelivery::STATUS_PROCESSING:
                        return '<span class="badge badge-sm badge-success">Processing</span>';
                    case WebhookDelivery::STATUS_RETRY_SCHEDULED:
                        return '<span class="badge badge-sm badge-warning">Retry Scheduled</span>';
                    case WebhookDelivery::STATUS_DELIVERED:
                        return '<span class="badge badge-sm badge-success">Delivered</span>';
                    case WebhookDelivery::STATUS_FAILED_PERMANENTLY:
                        return '<span class="badge badge-sm badge-danger text-wrap text-start">Failed Permanently</span>';
                    case WebhookDelivery::STATUS_CANCELLED:
                        return '<span class="badge badge-sm badge-danger">Cancelled</span>';
                    default:
                        return '<span class="badge badge-sm badge-info">N/A</span>';
                }
            })
            ->editColumn('first_attempt_at', function ($webhookDelivery) {
                return $webhookDelivery->first_attempt_at ? $webhookDelivery->first_attempt_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('last_attempt_at', function ($webhookDelivery) {
                return $webhookDelivery->last_attempt_at ? $webhookDelivery->last_attempt_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('next_attempt_at', function ($webhookDelivery) {
                return $webhookDelivery->next_attempt_at ? $webhookDelivery->next_attempt_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('delivered_at', function ($webhookDelivery) {
                return $webhookDelivery->delivered_at ? $webhookDelivery->delivered_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('failed_at', function ($webhookDelivery) {
                return $webhookDelivery->failed_at ? $webhookDelivery->failed_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('last_http_status', function ($webhookDelivery) {

                $lastHttpStatus = $webhookDelivery->last_http_status ? $webhookDelivery->last_http_status : '-';

                return '<span class="font-monospace text-start">' . $lastHttpStatus . '</span>';
            })
            ->editColumn('last_error_code', function ($webhookDelivery) {

                $lastErrorStatus = $webhookDelivery->last_error_code ? $webhookDelivery->last_error_code : '-';

                return '<span class="font-monospace text-start">' . $lastErrorStatus . '</span>';
            })
            ->editColumn('last_error_message', function ($webhookDelivery) {

                $lastErrorStatus = $webhookDelivery->last_error_message ? Str::limit($webhookDelivery->last_error_message, 30, '...') : '-';

                return $lastErrorStatus;
            })
            ->editColumn('created_at', function ($webhookDelivery) {
                return $webhookDelivery->created_at ? $webhookDelivery->created_at->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('action', function ($webhookDelivery) {
                return sprintf(
                '<a href="'. route('admin.webhook-delivery.show',$webhookDelivery->id) .'" style="font-size: 12px; cursor: pointer" class="btn-sm btn-primary" ><i class="fa fa-eye" aria-hidden="true"></i></a>',
                    $webhookDelivery->id
                );
            })
            ->rawColumns(['action', 'last_http_status', 'last_error_code'])
            ->orderColumn('event_type', 'webhook_events.event_type $1')
            ->make(true);
    }

    public function show(string $id)
    {
        $webhookDelivery = WebhookDelivery::query()->find($id);
        return view($this->folder . '.show', compact('webhookDelivery'));
    }
}
