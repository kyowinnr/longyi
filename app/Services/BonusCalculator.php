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
        $distribution = [$recruiter->id => 10000];

        $secondLevel = $recruiter->sponsor;

        if ($secondLevel) {
            $distribution[$secondLevel->id] = 5000;
        } else {
            $distribution[$recruiter->id] += 5000;
        }

        return $distribution;
    }
}
