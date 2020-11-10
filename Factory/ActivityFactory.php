<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Activity;
use App\Models\User;


class ActivityFactory
{
    /**
     * @param User $user
     * @param string $type
     * @param array $payloads
     * @return Activity
     */
    public function create(User $user, string $type, array $payloads): Activity
    {
        return new Activity([
            'user_id' => $user->id,
            'type' => $type,
            'payloads' => json_encode($payloads)
        ]);
    }
}
