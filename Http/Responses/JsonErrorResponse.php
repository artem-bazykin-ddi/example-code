<?php
declare(strict_types=1);

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class JsonErrorResponse extends JsonResponse
{
    /**
     * ErrorResponse constructor.
     *
     * @param $message
     * @param int $status
     * @param array $headers
     * @param int $options
     */
    public function __construct($message, $status = 400, $headers = [], $options = 0)
    {
        $data['error'] = $message;
        parent::__construct($data, $status, $headers, $options);
    }
}
