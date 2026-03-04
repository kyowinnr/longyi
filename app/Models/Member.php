<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'id_number',
        'birthday',
        'phone',
        'joined_at',
        'expires_at',
        'sponsor_id',
    ];

    protected $casts = [
        'birthday' => 'date',
        'joined_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function sponsor(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'sponsor_id');
    }

    public function recruits(): HasMany
    {
        return $this->hasMany(Member::class, 'sponsor_id');
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(BonusPayout::class);
    }
}
