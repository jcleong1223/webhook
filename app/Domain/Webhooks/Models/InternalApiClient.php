<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InternalApiClient extends Model
{
    //
    use SoftDeletes;

    protected $fillable = [
        'internal_api_client_id',
        'client_id',
        'name',
        'secret_encrypted',
        'allowed_ips',
        'status',
        'last_used_at',
        'created_at',
        'updated_at',
    ];

    protected $hidden = [
        'secret_encrypted',
    ];

    protected $casts = [
        'allowed_ips' => 'array',
        'last_used_at' => 'datetime'
    ];

    /****** Scope function ******/
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

}
