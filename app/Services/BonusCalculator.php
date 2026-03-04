<?php

namespace App\Services;

use App\Models\Member;

class BonusCalculator
{
    /**
     * @return array<int, int> [member_id => amount]
     */
    public function forRecruitment(Member $recruiter): array
    {
        // 第一代（無 sponsor）招收第二代時，不分獎金。
        if (! $recruiter->sponsor_id) {
            return [];
        }

        $distribution = [$recruiter->id => 10000];

        if ($recruiter->sponsor) {
            $distribution[$recruiter->sponsor->id] = 5000;
        }

        return $distribution;
    }
}
