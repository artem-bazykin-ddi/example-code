<?php

namespace App\Rules;

use App\Models\Interfaces\ModelPermissionInterface;
use Illuminate\Contracts\Validation\Rule;

class PermissionRule implements Rule
{
    /**
     * @var ModelPermissionInterface
     */
    private $modelPermission;

    /**
     * Create a new rule instance.
     *
     * @param ModelPermissionInterface $modelPermission
     */
    public function __construct(ModelPermissionInterface $modelPermission)
    {
        $this->modelPermission = $modelPermission;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return in_array((int) $value, $this->modelPermission::getAvailablePermissions(), true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Permission is invalid. Available permissions ['.implode(',', $this->modelPermission::getAvailablePermissions()).']';
    }
}
