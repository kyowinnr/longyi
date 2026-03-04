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

        return view('dashboard', compact('members', 'events', 'totalIncome', 'totalPayout', 'keyword'));
    }

    public function recruit(Request $request, BonusCalculator $calculator): RedirectResponse
    {
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

        return back()->with('status', '招募與獎金分配已建立');
    }

    public function seedExample(): RedirectResponse
    {
        DB::transaction(function (): void {
            BonusPayout::query()->delete();
            RecruitmentEvent::query()->delete();
            Member::query()->delete();

            $first = Member::create(['name' => '第一代']);
            $second = Member::create(['name' => '第二代', 'sponsor_id' => $first->id]);
            $third = Member::create(['name' => '第三代', 'sponsor_id' => $second->id]);
            $fourth = Member::create(['name' => '第四代', 'sponsor_id' => $third->id]);

            $events = [
                [$first, $second],
                [$second, $third],
                [$third, $fourth],
            ];

            $calculator = app(BonusCalculator::class);

            foreach ($events as [$recruiter, $newMember]) {
                $distribution = $calculator->forRecruitment($recruiter->load('sponsor'));

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
            }
        });

        return back()->with('status', '已建立範例資料（第一代到第四代）');
    }
}
