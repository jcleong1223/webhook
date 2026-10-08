<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\InternalApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;


class InternalApiClientController extends Controller
{
    //

    public function index()
    {
        return view('admin.internal-api-client.index');
    }

    public function apiClientsDatatable()
    {
        $clients = InternalApiClient::query();

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
                return '<a class="btn btn-primary" href="' . route('admin.api-client.create', $client->id) . '"><i class="fa fa-search" aria-hidden="true"></i></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create()
    {
        return view('admin.internal-api-client.create');
    }

    public function store(Request $request)
    {
        if ($request->has('allowed_ips') && is_string($request->input('allowed_ips'))) {
            $ips = array_values(array_filter(
                array_map('trim', preg_split('/\r\n|\r|\n/', $request->input('allowed_ips'))),
                fn($ip) => !empty($ip)
            ));

            // Overwrite request input with the parsed array
            $request->merge(['allowed_ips' => $ips]);
        }

        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'allowed_ips'   => 'nullable|array',
            'allowed_ips.*' => 'ip',
            'status'        => 'required|in:active,inactive',
        ], [
            'allowed_ips.*.ip' => 'One or more of the entered IP addresses are invalid.',
        ]);

        // Generate the client identifier and secret. The secret is stored encrypted
        // and is only ever shown to the operator once, immediately after creation.
        $clientId = 'sgdp_' . Str::lower(Str::random(32));
        $plainSecret = Str::random(64);

        InternalApiClient::create([
            'client_id'        => $clientId,
            'name'             => $data['name'],
            'secret_encrypted' => Crypt::encryptString($plainSecret),
            'allowed_ips'      => $data['allowed_ips'] ?? null,
            'status'           => $data['status'],
        ]);

        return redirect()
            ->route('admin.api-client.create')
            ->with('success', 'API client created successfully.')
            ->with('new_client_id', $clientId)
            ->with('new_client_secret', $plainSecret);
    }
}
