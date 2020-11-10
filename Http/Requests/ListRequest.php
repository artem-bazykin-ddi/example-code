<?php
declare(strict_types=1);

namespace App\Http\Requests;

class ListRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'limit' => 'integer',
            'offset' => 'integer',
            'sort' => 'in:asc,desc',
        ]);
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return (int) $this->get('offset', 0);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) $this->get('limit', 10);
    }

    /**
     * @return string
     */
    public function getSortDirection(): string
    {
        return $this->get('sort', 'asc');
    }
}
