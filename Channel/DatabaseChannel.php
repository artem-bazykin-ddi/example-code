<?php
declare(strict_types=1);

namespace App\Channel;

use App\Notifications\Interfaces\StorableNotificationInterface;
use Illuminate\Notifications\Channels\DatabaseChannel as DatabaseChannelAlias;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends DatabaseChannelAlias
{
    /**
     * @param mixed $notifiable
     * @param Notification|StorableNotificationInterface $notification
     *
     * @return array
     */
    public function buildPayload($notifiable, Notification $notification): array
    {
        return array_merge(
            parent::buildPayload($notifiable, $notification),
            [
                'current_type' => $notification->getType()
            ]
        );
    }
}
