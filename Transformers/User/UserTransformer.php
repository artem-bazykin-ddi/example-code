<?php
declare(strict_types=1);

namespace App\Transformers\User;

use App\Models\User;
use App\Transformers\AbstractTransformer;

class UserTransformer extends AbstractTransformer implements UserTransformerInterface
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'client_id' => $user->client_id,
            'job_role' => $user->job_role,
            'job_dept' => $user->job_dept,
            'phone' => $user->phone,
            'permission' => $user->permission,
            'created_at' => $this->date($user->created_at),
            'updated_at' => $this->date($user->updated_at),
        ];
    }
}
