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

        // 若有上線且上線不是公司，則上線拿 5,000。
        if ($recruiter->sponsor && $recruiter->sponsor->sponsor_id !== null) {
            $distribution[$recruiter->sponsor->id] = 5000;

            return $distribution;
        }

        // 若沒有上線，或上線為公司，該 5,000 回補給招募人（共 15,000）。
        $distribution[$recruiter->id] += 5000;

        return $distribution;
    }
}
