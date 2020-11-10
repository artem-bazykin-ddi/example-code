<?php
declare(strict_types=1);

namespace App\ObjectValue;

use App\Exceptions\ValueObjectException;

class SortObjectValue
{
    /**
     * @var string
     */
    private $field;
    /**
     * @var string
     */
    private $direction;

    /**
     * @var array
     */
    private $availableDirections = ['asc', 'desc'];

    /**
     * SortObjectValue constructor.
     *
     * @param string $field
     * @param string $direction
     */
    public function __construct(string $field, string $direction)
    {
        $this->field = $field;
        $this->direction = $direction;
        $this->validate();
    }

    private function validate(): void
    {
        if (!in_array($this->direction, $this->availableDirections, true)) {
            throw new ValueObjectException('Direction can has only ['.implode(',', $this->availableDirections).'] values');
        }
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }
}
