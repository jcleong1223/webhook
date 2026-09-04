<?php

namespace App\Domain\Webhooks\Models;

use Illuminate\Database\Eloquent\Model;

class InternalApiClient extends Model
{
    //

    protected $fillable = [
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
