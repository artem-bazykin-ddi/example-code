<?php
declare(strict_types=1);

namespace App\Http\Responses;

use App\Http\Requests\ListRequest;
use App\Transformers\TransformerInterface;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

interface ListResponseInterface
{
    public function setBuilder(Builder $builder): ListResponseInterface;

    public function setData($data): ListResponseInterface;

    public function setTransformer(TransformerInterface $transformer): ListResponseInterface;

    public function setOffset(int $offset): ListResponseInterface;

    public function setLimit(int $limit): ListResponseInterface;

    public function setSortDirection(string $sortDirection): ListResponseInterface;

    public function setSortField(string $sortField): ListResponseInterface;

    public function setWhere(array $where): ListResponseInterface;

    public function getResponse(): JsonResponse;

    public function addValue(string $key, $value): ListResponseInterface;

    public function setResultHandler(Closure $closure): ListResponseInterface;

    public function addValueWithTransformer(string $key, $value, TransformerInterface $transformer): ListResponseInterface;

    public function setRequest(ListRequest $request): ListResponseInterface;
}
