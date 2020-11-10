<?php

namespace App\Rules;

use App\Models\Interfaces\SilScoreInterface;
use Illuminate\Contracts\Validation\Rule;

/**
 * Class ActivityEventRule
 * @package App\Rules
 */
class ActivityEventRule implements Rule
{

    /**
     * @var array
     */
    private $silScoreEvents;

    /**
     * ActivityEventRule constructor.
     * @param array $silScoreEvents
     */
    public function __construct(array $silScoreEvents)
    {
        $this->silScoreEvents = $silScoreEvents;
    }

    /**
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return in_array($value, $this->silScoreEvents, true);
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return 'Event type is invalid. Available event types [' . implode(',', $this->silScoreEvents) . ']';
    }
}