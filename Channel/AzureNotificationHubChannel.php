<?php
declare(strict_types=1);

namespace App\Channel;

use App\DataTransferObjects\PushNotificationDTO;
use App\Models\User;
use App\Services\Azure\NotificationHubService\NotificationHubService;
use App\Services\PushService\Types\AppleNotification;
use App\Services\PushService\Types\FCMNotification;
use App\Services\PushService\Types\NotificationInterface;
use LogicException;

class AzureNotificationHubChannel
{
    /**
     * @var NotificationHubService
     */
    private $notificationHubService;

    /**
     * AzureNotificationHubChannel constructor.
     *
     * @param NotificationHubService $notificationHubService
     */
    public function __construct(NotificationHubService $notificationHubService)
    {

        $this->notificationHubService = $notificationHubService;
    }

    /**
     * Send the given notification.
     *
     * @param User $user
     *
     * @return void
     */
    public function send(User $user, $notification): void
    {
        $pushNotificationDTO = $notification->toAzureNotificationHubChanel($user);

        $devices = $user->devices;

        foreach ($devices as $device) {
            $universalNotification = $this->build($device->type, $pushNotificationDTO);
            $this->notificationHubService->sendNotification($device->device_token, $universalNotification->getPayload(), $universalNotification->getHeaders());
        }
    }

    /**
     * @param string $type
     * @param PushNotificationDTO $pushNotificationDTO
     *
     * @return NotificationInterface
     */
    private function build(string $type, PushNotificationDTO $pushNotificationDTO): NotificationInterface
    {
        switch ($type) {
            case AppleNotification::getFormat():
                return new AppleNotification($pushNotificationDTO->getTitle(), '', $pushNotificationDTO->getBody(), $pushNotificationDTO->getAdditionalData());
            case FCMNotification::getFormat():
                return new FCMNotification($pushNotificationDTO->getTitle(), $pushNotificationDTO->getBody(), $pushNotificationDTO->getAdditionalData());
            default:
                throw new LogicException('Could not resolve notification type '.$type);
        }
    }
}
