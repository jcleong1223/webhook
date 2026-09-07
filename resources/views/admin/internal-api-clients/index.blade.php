<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>API Clients - Horizon</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:300,400,500,600" rel="stylesheet" />
    {!! Laravel\Horizon\Horizon::css() !!}
    @include('admin.component.theme-sync')
</head>

<body>
    <div class="container mb-5">
        <div class="d-flex align-items-center py-4 header">
            <a href="{{ url(config('horizon.path')) }}" class="logo d-flex align-items-center text-decoration-none">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 30 30" width="30" height="30">
                    <path class="" fill="red" d="M5.26176342 26.4094389C2.04147988 23.6582233 0 19.5675182 0 15c0-4.1421356 1.67893219-7.89213562 4.39339828-10.60660172C7.10786438 1.67893219 10.8578644 0 15 0c8.2842712 0 15 6.71572875 15 15 0 8.2842712-6.7157288 15-15 15-3.716753 0-7.11777662-1.3517984-9.73823658-3.5905611zM4.03811305 15.9222506C5.70084247 14.4569342 6.87195416 12.5 10 12.5c5 0 5 5 10 5 3.1280454 0 4.2991572-1.9569336 5.961887-3.4222502C25.4934253 8.43417206 20.7645408 4 15 4 8.92486775 4 4 8.92486775 4 15c0 .3105915.01287248.6181765.03811305.9222506z" />
                </svg>
                <h1 class="h4 mb-0 ms-2"><strong>SGDATAPOS</strong> Central Webhook</h1>
            </a>

        </div>

        <div class="row mt-4">
            <div class="col-2 sidebar">
                @include('admin.component.sidebar', ['active' => 'api-clients'])
            </div>

            <div class="col-10">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="h3 mb-0">API Clients</h2>
                    <span class="text-muted small">{{ $clients->total() }} total</span>
                </div>

                @if ($clients->isEmpty())
                    <div class="alert alert-info mb-0">No API clients found.</div>
                @else
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Client ID</th>
                                <th>Status</th>
                                <th>Allowed IPs</th>
                                <th>Last Used</th>
                                <th>Created</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($clients as $client)
                            <tr>
                                <td>{{ $client->id }}</td>
                                <td>{{ $client->name }}</td>
                                <td><code>{{ $client->client_id }}</code></td>
                                <td>
                                    @if ($client->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                    @else
                                    <span class="badge bg-secondary">{{ ucfirst($client->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if (!empty($client->allowed_ips))
                                    <span class="small">{{ implode(', ', $client->allowed_ips) }}</span>
                                    @else
                                    <span class="text-muted small">Any</span>
                                    @endif
                                </td>
                                <td>{{ $client->last_used_at ? $client->last_used_at->diffForHumans() : '—' }}</td>
                                <td>{{ $client->created_at->format('Y-m-d H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $clients->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>