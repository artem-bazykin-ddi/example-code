<?php
declare(strict_types=1);

namespace App\Rules;

use App\Models\Interfaces\ModelStatusInterface;
use Illuminate\Contracts\Validation\Rule;

class StatusRule implements Rule
{
    /**
     * @var ModelStatusInterface
     */
    private $modelStatus;

    /**
     * Create a new rule instance.
     *
     * @param ModelStatusInterface $modelStatus
     */
    public function __construct(ModelStatusInterface $modelStatus)
    {
        $this->modelStatus = $modelStatus;
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
        return in_array((int) $value, $this->modelStatus::getAvailableStatuses(), true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'Status is invalid. Available statuses ['.implode(',', $this->modelStatus::getAvailableStatuses()).']';
    }
}
