<?php
declare(strict_types=1);

namespace App\Services\PushService;

use App\Models\Device;

class NotificationBuilder
{
    public function getNotification(Device $device, array $payload): Notification
    {


        return new Notification();
    }
}