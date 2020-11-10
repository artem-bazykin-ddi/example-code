<?php
declare(strict_types=1);

namespace App\DataTransferObjects;

use App\ObjectValue\SortObjectValue;

class RequestListsDTO
{
    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var array
     */
    private $where;

    /**
     * @var SortObjectValue
     */
    private $sort;

    /**
     * RequestListsDTO constructor.
     *
     * @param int|null $limit
     * @param int|null $offset
     * @param SortObjectValue $sort
     * @param array $where
     */
    public function __construct($limit, $offset, SortObjectValue $sort, array $where = [])
    {
        $this->limit = (int) $limit;
        $this->offset = (int) $offset;
        $this->where = $where;
        $this->sort = $sort;
    }

    /**
     * @return int
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * @return SortObjectValue
     */
    public function getSort(): SortObjectValue
    {
        return $this->sort;
    }
}
