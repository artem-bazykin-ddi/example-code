<?php
declare(strict_types=1);

namespace App\Http;

use Illuminate\Http\Response;

/**
 * Class DataResponse
 * @package App\Http
 *
 * Design pattern Singleton
 * class for a unified response standard
 */
final class DataResponse
{

    /**
     * @var int
     */
    private $status;

    /**
     * @var array
     */
    private $data;

    /**
     * DataResponse constructor.
     *
     * @param int $status
     * @param array $data
     */
    public function __construct(int $status = Response::HTTP_OK, $data = [])
    {
        $this->status = $status;
        $this->data = $data;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setData(array $data): void
    {
        $this->data = $data;
    }
}
