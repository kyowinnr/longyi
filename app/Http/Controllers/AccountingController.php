<?php

namespace App\Http\Controllers;

use App\Models\BonusPayout;
use App\Models\Member;
use App\Models\RecruitmentEvent;
use App\Services\BonusCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AccountingController extends Controller
{
    public function index(Request $request): View
    {
        $company = $this->ensureDefaultCompany();

        $keyword = $request->string('q')->toString();

        $members = Member::query()
            ->when($keyword, fn ($query) => $query->where('name', 'like', "%{$keyword}%"))
            ->with('sponsor')
            ->orderBy('id')
            ->get();

        $events = RecruitmentEvent::query()
            ->with(['recruiter', 'newMember', 'bonusPayouts.member'])
            ->latest()
            ->get();

        $totalIncome = RecruitmentEvent::sum('company_income');
        $totalPayout = BonusPayout::sum('amount');

        return view('dashboard', compact('members', 'events', 'totalIncome', 'totalPayout', 'keyword', 'company'));
    }

    public function recruit(Request $request, BonusCalculator $calculator): RedirectResponse
    {
        $this->ensureDefaultCompany();

        $validated = $request->validate([
            'recruiter_id' => ['required', 'exists:members,id'],
            'new_member_name' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($validated, $calculator): void {
            $recruiter = Member::with('sponsor')->findOrFail($validated['recruiter_id']);
            $newMember = Member::create([
                'name' => $validated['new_member_name'],
                'sponsor_id' => $recruiter->id,
            ]);

            $distribution = $calculator->forRecruitment($recruiter);

            $event = RecruitmentEvent::create([
                'recruiter_id' => $recruiter->id,
                'new_member_id' => $newMember->id,
                'company_income' => 36000,
                'bonus_payout_total' => array_sum($distribution),
            ]);

            foreach ($distribution as $memberId => $amount) {
                BonusPayout::create([
                    'recruitment_event_id' => $event->id,
                    'member_id' => $memberId,
                    'amount' => $amount,
                ]);
            }
        });

        return back()->with('status', '招募與帳務已建立');
    }

    private function ensureDefaultCompany(): Member
    {
        return Member::query()->firstOrCreate(
            ['name' => '公司', 'sponsor_id' => null],
            ['name' => '公司', 'sponsor_id' => null]
        );
    }
}
