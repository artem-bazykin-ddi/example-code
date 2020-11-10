<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface ModelStatusInterface
{
    /**
     * Available statuses
     */
    public const STATUS_DRAFT = 1;
    public const STATUS_REVIEW = 2;
    public const STATUS_PUBLISHED = 3;

    /**
     * @return array
     */
    public static function getAvailableStatuses(): array;
}
