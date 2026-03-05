<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BonusPayout;
use App\Models\RecruitmentEvent;
use Illuminate\Http\JsonResponse;

class RwsController extends Controller
{
    public function ledger(): JsonResponse
    {
        return response()->json([
            'company_income_total' => RecruitmentEvent::sum('company_income'),
            'bonus_payout_total' => BonusPayout::sum('amount'),
            'company_net_total' => RecruitmentEvent::sum('company_income') - BonusPayout::sum('amount'),
            'events' => RecruitmentEvent::with(['recruiter', 'newMember', 'bonusPayouts.member'])->latest()->get(),
        ]);
    }
}
