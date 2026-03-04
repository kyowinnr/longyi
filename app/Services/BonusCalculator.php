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
        // 公司招收第一代，不分獎金。
        if ($recruiter->sponsor_id === null) {
            return [];
        }

        $distribution = [$recruiter->id => 10000];

        // 若上線為公司（sponsor_id 為 null），不再往上分配 5,000。
        if ($recruiter->sponsor && $recruiter->sponsor->sponsor_id !== null) {
            $distribution[$recruiter->sponsor->id] = 5000;
        }

        return $distribution;
    }
}
