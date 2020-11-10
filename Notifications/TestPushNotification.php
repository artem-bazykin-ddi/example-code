<?php
declare(strict_types=1);

namespace App\Notifications;

use App\Channel\AzureNotificationHubChannel;
use App\DataTransferObjects\PushNotificationDTO;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TestPushNotification extends Notification
{
    use Queueable;
    /**
     * @var string
     */
    private $email;

    /**
     * Create a new notification instance.
     *
     * @param string $email
     */
    public function __construct(string  $email)
    {
        $this->email = $email;
    }

    /**
     * @param $notifiable
     *
     * @return array
     */
    public function via($notifiable): array
    {
        return [AzureNotificationHubChannel::class];
    }

    /**
     * @param $notifiable
     *
     * @return PushNotificationDTO
     */
    public function toAzureNotificationHubChanel($notifiable): PushNotificationDTO
    {
        return new PushNotificationDTO('Test push notification', 'Passed email '.$this->email);
    }
}
