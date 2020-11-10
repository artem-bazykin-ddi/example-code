<?php

namespace App\Rules;

use App\Models\Interfaces\ContentItemTypeInterface;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ContentType
 * @package App\Rules
 */
class ContentTypeRule implements Rule
{
    private $modelType;

    /**
     * ContentTypeRule constructor.
     * @param ContentItemTypeInterface $modelType
     */
    public function __construct(ContentItemTypeInterface $modelType)
    {
        $this->modelType = $modelType;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return in_array((int) $value, $this->modelType::getAvailableTypes(), true);
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return 'Content Type is invalid. Available types are [' . implode(',', $this->modelType::getAvailableTypes()) . ']';
    }
}