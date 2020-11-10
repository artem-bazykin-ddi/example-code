<?php
declare(strict_types=1);

namespace App\Services\PushService\Types;

interface NotificationInterface
{
    /**
     * @return array
     */
    public function getHeaders(): array;

    /**
     * @return string
     */
    public static function getFormat(): string;

    /**
     * @return string
     */
    public function getPayload(): string;
}
