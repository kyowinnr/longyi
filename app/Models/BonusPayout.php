<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BonusPayout extends Model
{
    use HasFactory;

    protected $fillable = ['recruitment_event_id', 'member_id', 'amount'];

    public function event(): BelongsTo
    {
        return $this->belongsTo(RecruitmentEvent::class, 'recruitment_event_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}
