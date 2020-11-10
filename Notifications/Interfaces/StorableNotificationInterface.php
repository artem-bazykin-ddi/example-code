<?php
declare(strict_types=1);

namespace App\Notifications\Interfaces;

interface StorableNotificationInterface
{
    /**
     * @return array
     */
    public function toDatabase($notifiable): array;

    /**
     * @return string
     */
    public function getType(): string;
}
