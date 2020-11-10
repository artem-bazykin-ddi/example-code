<?php
declare(strict_types=1);

namespace App\Services\PushService;

use RuntimeException;

class Notification
{
    /**
     * @var string
     */
    public $format;

    /**
     * @var array
     */
    public $payload;

    # array with keynames for headers
    # Note: Some headers are mandatory: Windows: X-WNS-Type, WindowsPhone: X-NotificationType
    # Note: For Apple you can set Expiry with header: ServiceBusNotification-ApnsExpiry in W3C DTF, YYYY-MM-DDThh:mmTZD (for example, 1997-07-16T19:20+01:00).
    public $headers;

    /**
     * Notification constructor.
     *
     * @param string $format
     * @param string $payload
     *
     * @throws RuntimeException
     */
    public function __construct(string $format, string $payload)
    {
        if (!in_array($format, ['template', 'apple', 'windows', 'fcm', 'windowsphone'])) {
            throw new RuntimeException('Invalid format: ' . $format);
        }

        $this->format = $format;
        $this->payload = $payload;
    }
}
