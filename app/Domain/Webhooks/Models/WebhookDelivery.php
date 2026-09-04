<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class WebhookDelivery extends Model
{
    /*** ULID string primary key, e.g. dlv_01K0HJW3FMQWQ9 (spec §9.4) ***/
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'tenant_id',
        'webhook_event_id',
        'endpoint_snapshot_id',
        'status',
        'attempt_count',
        'first_attempt_at',
        'last_attempt_at',
        'next_attempt_at',
        'delivered_at',
        'failed_at',
        'last_http_status',
        'last_error_code',
        'last_error_message',
        'last_response_duration_ms'
    ];

    protected $casts = [
        'first_attempt_at' => 'datetime',
        'last_attempt_at' => 'datetime',
        'next_attempt_at' => 'datetime',
        'delivered_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_QUEUED = 'queued';
    const STATUS_PROCESSING = 'processing';
    const STATUS_RETRY_SCHEDULED = 'retry_scheduled';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_FAILED_PERMANENTLY = 'failed_permanently';
    const STATUS_CANCELLED = 'cancelled';

    protected static function booted(): void
    {
        static::creating(function (self $delivery) {
            if (empty($delivery->id)) {
                $delivery->id = 'dlv_' . Str::ulid();
            }
        });
    }

    /****** Relations ******/
    public function event(): BelongsTo
    {
        return $this->belongsTo(WebhookEvent::class, 'webhook_event_id');
    }

    public function snapshot(): BelongsTo
    {
        return $this->belongsTo(WebhookEndpointSnapshot::class, 'endpoint_snapshot_id');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(WebhookDeliveryAttempt::class, 'delivery_id');
    }

    public function alerts(): HasMany
    {
        return $this->hasMany(WebhookAlert::class, 'delivery_id');
    }

    /**
     * Atomic CAS transition: pending/queued/retry_scheduled → processing (spec §20.4).
     *
     * Returns true when this worker owns the delivery; false when another
     * worker has already claimed it (the job must exit silently).
     * Increments attempt_count and sets first/last_attempt_at atomically.
     */
    public function transitionToProcessing(): bool
    {
        $affected = static::where('id', $this->id)
            ->whereIn('status', [
                self::STATUS_PENDING,
                self::STATUS_QUEUED,
                self::STATUS_RETRY_SCHEDULED,
            ])
            ->update([
                'status'           => self::STATUS_PROCESSING,
                'attempt_count'    => DB::raw('attempt_count + 1'),
                'first_attempt_at' => DB::raw('COALESCE(first_attempt_at, NOW())'),
                'last_attempt_at'  => now(),
            ]);

        if ($affected > 0) {
            $this->refresh();
            return true;
        }

        return false;
    }
}
