<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Throwable;

class JsonExceptionResponse extends JsonResponse
{
    /**
     * ErrorResponse constructor.
     *
     * @param Throwable $throwable
     * @param int $status
     * @param array $headers
     * @param int $options
     */
    public function __construct(Throwable $throwable, $status = 400, $headers = [], $options = 0)
    {
        $data['error'] = $throwable->getMessage();
        parent::__construct($data, $status, $headers, $options);
    }
}
