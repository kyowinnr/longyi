<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RecruitmentEvent extends Model
{
    use HasFactory;

    protected $fillable = ['recruiter_id', 'new_member_id', 'company_income', 'bonus_payout_total'];

    public function recruiter(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'recruiter_id');
    }

    public function newMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'new_member_id');
    }

    public function bonusPayouts(): HasMany
    {
        return $this->hasMany(BonusPayout::class);
    }
}
