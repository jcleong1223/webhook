<?php

/**
 * Temporary end-to-end smoke test for the internal ingestion API.
 * Run: php smoke_test.php
 */
require __DIR__ . '/vendor/autoload.php';

use App\Domain\Webhooks\Models\InternalApiClient;
use App\Domain\Webhooks\Models\WebhookDelivery;
use App\Domain\Webhooks\Models\WebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

$secret = 'whsec_test_' . bin2hex(random_bytes(16));

InternalApiClient::query()->delete();
InternalApiClient::create([
    'client_id' => 'legacy-prod-01',
    'name' => 'legacy-backoffice',
    'secret_encrypted' => Crypt::encryptString($secret),
    'status' => 'active',
    'allowed_ips' => null,
]);

function makeBody(string $sourceEventId): string
{
    return json_encode([
        'source' => [
            'system' => 'legacy-backoffice',
            'instance_id' => 'sgdp-prod-01',
            'event_id' => $sourceEventId,
            'occurred_at' => '2026-07-24T06:20:15.312Z',
        ],
        'tenant' => ['id' => 'tenant_1001', 'code' => 'ABC_RESTAURANT'],
        'merchant' => ['id' => 'merchant_1001', 'name' => 'ABC Restaurant'],
        'event' => [
            'type' => 'order.created',
            'api_version' => 'v1',
            'aggregate_type' => 'order',
            'aggregate_id' => 'order_12345',
            'resource_version' => 1,
        ],
        'endpoint' => [
            'legacy_webhook_id' => 'webhook_22',
            'name' => 'Merchant ERP',
            'url' => 'https://merchant.example.com/webhooks/orders',
            'signing_secret' => 'whsec_xxxxxxxxx',
            'timeout_seconds' => 10,
            'max_attempts' => 8,
            'status' => 'active',
        ],
        'alert' => [
            'emails' => ['integration@example.com'],
            'notify_after_attempt' => 3,
            'notify_on_permanent_failure' => true,
            'notify_on_recovery' => true,
        ],
        'payload' => [
            'order' => [
                'id' => 'order_12345',
                'order_number' => 'HH01-000123',
                'business_date' => '2026-07-24',
                'status' => 'confirmed',
                'currency' => 'MYR',
                'total' => '110.00',
            ],
        ],
    ]);
}

function send(string $body, string $secret, ?string $requestId = null, ?string $signature = null, ?int $timestamp = null): array
{
    global $kernel;
    $ts = $timestamp ?? time();
    $reqId = $requestId ?? 'req_' . bin2hex(random_bytes(8));
    $sig = $signature ?? 'v1=' . hash_hmac('sha256', $ts . '.' . $body, $secret);

    $request = Request::create('/api/internal/v1/webhook-events', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_SGDP_SOURCE' => 'legacy-backoffice',
        'HTTP_X_SGDP_CLIENT_ID' => 'legacy-prod-01',
        'HTTP_X_SGDP_TIMESTAMP' => (string) $ts,
        'HTTP_X_SGDP_REQUEST_ID' => $reqId,
        'HTTP_X_SGDP_SIGNATURE' => $sig,
        'REMOTE_ADDR' => '127.0.0.1',
    ], $body);

    $response = $kernel->handle($request);
    return [$response->getStatusCode(), $response->getContent(), $reqId];
}

WebhookEvent::query()->delete();

echo "--- 1. Valid new event (expect 202, evt_/dlv_ ULIDs, status queued) ---\n";
[$status, $content, $reqId] = send(makeBody('legacy-order-1001-created'), $secret);
echo $status . "\n" . $content . "\n\n";

echo "--- 2. Duplicate submission (expect 200, duplicate=true) ---\n";
[$status, $content] = send(makeBody('legacy-order-1001-created'), $secret);
echo $status . "\n" . $content . "\n\n";

echo "--- 3. Tampered body / bad signature (expect 401 INVALID_SIGNATURE) ---\n";
[$status, $content] = send(makeBody('legacy-order-1002-created'), $secret, null, 'v1=' . str_repeat('a', 64));
echo $status . "\n" . $content . "\n\n";

echo "--- 4. Stale timestamp (expect 401 REQUEST_TIMESTAMP_EXPIRED) ---\n";
[$status, $content] = send(makeBody('legacy-order-1003-created'), $secret, null, null, time() - 3600);
echo $status . "\n" . $content . "\n\n";

echo "--- 5. Replay with reused request ID (expect 401 REPLAY_DETECTED) ---\n";
$body = makeBody('legacy-order-1004-created');
[, , $firstReqId] = send($body, $secret);
[$status, $content] = send($body, $secret, $firstReqId);
echo $status . "\n" . $content . "\n\n";

echo "--- 6. Missing event.type (expect 422 VALIDATION_ERROR with fields) ---\n";
$bad = json_decode(makeBody('legacy-order-1005-created'), true);
unset($bad['event']['type']);
[$status, $content] = send(json_encode($bad), $secret);
echo $status . "\n" . $content . "\n\n";

echo "--- 7. SSRF: internal URL (expect 422) ---\n";
$bad = json_decode(makeBody('legacy-order-1006-created'), true);
$bad['endpoint']['url'] = 'http://127.0.0.1:8080/admin';
[$status, $content] = send(json_encode($bad), $secret);
echo $status . "\n" . $content . "\n\n";

echo "--- DB state ---\n";
$event = WebhookEvent::first();
echo 'events: ' . WebhookEvent::count() . ', deliveries: ' . WebhookDelivery::count() . "\n";
echo 'event id: ' . $event->id . "\n";
$delivery = WebhookDelivery::first();
echo 'delivery id: ' . $delivery->id . ', status: ' . $delivery->status . "\n";
$snapshot = $delivery->snapshot;
echo 'snapshot secret decrypts: ' . ($snapshot->signing_secret_encrypted === 'whsec_xxxxxxxxx' ? 'YES' : 'NO') . "\n";
echo 'secret stored encrypted at rest: ' . (str_contains(\Illuminate\Support\Facades\DB::table('webhook_endpoint_snapshots')->value('signing_secret_encrypted'), 'whsec_xxxxxxxxx') ? 'NO (plaintext!)' : 'YES') . "\n";
