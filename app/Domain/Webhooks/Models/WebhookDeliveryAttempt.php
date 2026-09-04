<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookDeliveryAttempt extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'tenant_id',
        'delivery_id',
        'attempt_number',
        'request_timestamp',
        'request_headers_sanitized',
        'request_body_hash',
        'response_status',
        'response_headers_sanitized',
        'response_body_excerpt',
        'duration_ms',
        'error_type',
        'error_message',
        'attempted_at',
    ];

    protected $casts = [
        'request_timestamp' => 'datetime',
        'request_headers_sanitized' => 'array',
        'response_headers_sanitized' => 'array',
        'attempted_at' => 'datetime',
    ];

    public function delivery(): BelongsTo
    {
        return $this->belongsTo(WebhookDelivery::class, 'delivery_id');
    }
}
