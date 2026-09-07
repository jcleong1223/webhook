<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Webhooks\Models\InternalApiClient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class InternalApiClientController extends Controller
{
    //

    public function index()
    {
        $clients = InternalApiClient::query()
            ->orderBy('name')
            ->paginate(25);

        return view('admin.internal-api-clients.index', compact('clients'));
    }
}
