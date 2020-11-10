<?php
declare(strict_types=1);

namespace App\Services\ScheduleService;

use App\Services\PushService\NotificationBuilder;
use App\Services\PushService\PushNotificationService;

class ScheduleService
{
    /**
     * @var PushNotificationService
     */
    private $notificationHub;
    /**
     * @var NotificationBuilder
     */
    private $notificationBuilder;

    /**
     * ScheduleService constructor.
     *
     * @param PushNotificationService $notificationHub
     * @param NotificationBuilder $notificationBuilder
     */
    public function __construct(PushNotificationService $notificationHub, NotificationBuilder $notificationBuilder)
    {
        $this->notificationHub = $notificationHub;
        $this->notificationBuilder = $notificationBuilder;
    }

    public function send(ScheduleTopic $scheduleTopic)
    {
        $user = $scheduleTopic->user;

        $devices = $user->devices;

        foreach ($devices as $device) {
            $notificationMessage = $this->notificationBuilder->getNotification($device);
                $this->notificationHub->sendNotification($notificationMessage);
        }


    }
}