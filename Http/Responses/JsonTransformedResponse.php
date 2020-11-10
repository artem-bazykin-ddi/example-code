<?php
declare(strict_types=1);

namespace App\Http\Responses;

use App\Transformers\TransformerInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;

class JsonTransformedResponse extends JsonResponse
{
    /**
     * ErrorResponse constructor.
     *
     * @param Model $model
     * @param TransformerInterface $transformer
     * @param int $status
     * @param array $headers
     * @param int $options
     */
    public function __construct(Model $model, TransformerInterface $transformer, $status = 200, $headers = [], $options = 0)
    {
        parent::__construct(fractal($model, $transformer)->toArray(), $status, $headers, $options);
    }
}
