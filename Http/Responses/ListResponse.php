<?php
declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Requests\ListRequest;
use App\Transformers\TransformerInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

/**
 * Class ListResponse
 */
class ListResponse implements ListResponseInterface
{
    /**
     * @var TransformerInterface
     */
    protected $transformer;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var string
     */
    protected $sortDirection = 'asc';

    /**
     * @var string
     */
    protected $sortField = 'id';

    /**
     * @var array
     */
    protected $where = [];

    /**
     * @var array
     */
    private $additionalValues = [];

    /**
     * @var array|null
     */
    protected $data;

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Closure
     */
    protected $resultHandler;

    /**
     * @param Builder $builder
     * @return ListResponseInterface
     */
    public function setBuilder(Builder $builder): ListResponseInterface
    {
        $this->builder = $builder;

        return $this;
    }

    /**
     * @param $data
     * @return ListResponseInterface
     */
    public function setData($data): ListResponseInterface
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @param TransformerInterface $transformer
     * @return ListResponseInterface
     */
    public function setTransformer(TransformerInterface $transformer): ListResponseInterface
    {
        $this->transformer = $transformer;

        return $this;
    }

    /**
     * @param int $offset
     * @return ListResponseInterface
     */
    public function setOffset(int $offset): ListResponseInterface
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     * @return ListResponseInterface
     */
    public function setLimit(int $limit): ListResponseInterface
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * @param string $sortDirection
     * @return ListResponseInterface
     */
    public function setSortDirection(string $sortDirection): ListResponseInterface
    {
        $this->sortDirection = $sortDirection;

        return $this;
    }

    /**
     * @param string $sortField
     * @return ListResponseInterface
     */
    public function setSortField(string $sortField): ListResponseInterface
    {
        $this->sortField = $sortField;

        return $this;
    }

    /**
     * @param array $where
     * @return ListResponseInterface
     */
    public function setWhere(array $where): ListResponseInterface
    {
        $this->where = $where;

        return $this;
    }

    /**
     * @return JsonResponse
     */
    public function getResponse(): JsonResponse
    {
        return new JsonResponse(array_merge($this->getAdditionalValues(), $this->getList()));
    }

    /**
     * @param string $key
     * @param $value
     * @return ListResponseInterface
     */
    public function addValue(string $key, $value): ListResponseInterface
    {
        $this->additionalValues[$key] = $value;

        return $this;
    }


    /**
     * @return array
     */
    protected function getList(): array
    {
        return fractal()
            ->collection($this->getData())
            ->transformWith($this->transformer)
            ->toArray();
    }

    /**
     * @return array
     */
    protected function getAdditionalValues(): array
    {
        return $this->additionalValues;
    }

    /**
     * @return Builder[]|Collection
     */
    protected function getData()
    {
        if ($this->data !== null) {
            return $this->data;
        }

        $builder = $this->applyWhere($this->builder, $this->where);

        $result = $this->applySort($builder, $this->sortField, $this->sortDirection)->get();

        if ($this->resultHandler !== null) {
            $resultHandler = $this->resultHandler;
            $result = $resultHandler($result);
        }

        return $result;
    }

    /**
     * @param Builder $builder
     * @param array $where
     *
     * @return Builder
     */
    protected function applyWhere(Builder $builder, array $where): Builder
    {
        foreach ($where as $key => $value) {
            if ($value) {
                $builder->where($key, $value);
            }
        }

        return $builder;
    }

    /**
     * @param Builder $builder
     * @param string $sortField
     * @param string $sortDirection
     *
     * @return Builder
     */
    protected function applySort(Builder $builder, string $sortField = 'id', string $sortDirection = 'asc'): Builder
    {
        $builder->orderBy($sortField, $sortDirection);

        return $builder;
    }

    /**
     * @param Closure $closure
     *
     * @return ListResponseInterface
     */
    public function setResultHandler(Closure $closure): ListResponseInterface
    {
        $this->resultHandler = $closure;

        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @param TransformerInterface $transformer
     *
     * @return ListResponseInterface
     */
    public function addValueWithTransformer(string $key, $value, TransformerInterface $transformer): ListResponseInterface
    {
        $this->addValue($key, fractal($value, $transformer)->toArray()['data']);

        return $this;
    }

    /**
     * @param ListRequest $request
     *
     * @return ListResponseInterface
     */
    public function setRequest(ListRequest $request): ListResponseInterface
    {
        $this->request = $request;

        return $this;
    }
}
