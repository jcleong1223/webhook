<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\InternalApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Yajra\DataTables\Facades\DataTables;


class InternalApiClientController extends Controller
{
    //

    public function index()
    {
        return view('admin.internal-api-clients.index');
    }

    public function apiClientsDatatable(Request $request)
    {
        $clients = InternalApiClient::query()->orderByDesc('created_at');

        return DataTables::of($clients)
            ->addIndexColumn()
            ->editColumn('status', function ($client) {
                return $client->status ?? '-';
            })
            ->editColumn('allowed_ips', function ($client) {
                return ! empty($client->allowed_ips) ? implode(', ', $client->allowed_ips) : '-';
            })
            ->editColumn('last_used_at', function ($client) {
                return $client->last_used_at ? $client->last_used_at->format('Y-m-d H:i:s') : '-';
            })
            ->editColumn('created_at', function ($client) {
                return $client->created_at ? $client->created_at->format('Y-m-d H:i:s') : '-';
            })
            ->addColumn('action', function ($client) {
                // TODO: render row action buttons (view / edit / delete) as needed.
                return '';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
