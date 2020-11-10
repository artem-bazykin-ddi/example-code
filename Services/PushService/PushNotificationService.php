<?php
declare(strict_types=1);

namespace App\Services\PushService;

use App\Models\ContentItem;
use App\Models\Interfaces\ContentItemTypeInterface;
use App\Services\Azure\NotificationHubService\NotificationHubService;
use App\Services\PushService\Types\NotificationInterface;
use LogicException;
use RuntimeException;

class PushNotificationService
{
    /**
     * @var NotificationHubService
     */
    private $notificationHubService;

    /**
     * @var string
     */
    private $sasKeyValue;

    /**
     * NotificationHub constructor.
     *
     * @param $connectionString
     * @param $hubPath
     *
     * @throws RuntimeException
     */
    public function __construct(string $connectionString, string $hubPath)
    {
        $this->hubPath = $hubPath;

        $this->parseConnectionString($connectionString);
    }

    /**
     * @param $connectionString
     *
     * @throws RuntimeException
     */
    private function parseConnectionString(string $connectionString): void
    {
        $parts = explode(';', $connectionString);
        if (count($parts) !== 3) {
            throw new RuntimeException('Error parsing connection string: ' . $connectionString);
        }

        foreach ($parts as $part) {
            if (strpos($part, 'Endpoint') === 0) {
                $this->endpoint = 'https' . substr($part, 11);
            } elseif (strpos($part, 'SharedAccessKeyName') === 0) {
                $this->sasKeyName = substr($part, 20);
            } elseif (strpos($part, 'SharedAccessKey') === 0) {
                $this->sasKeyValue = substr($part, 16);
            }
        }
    }


    /**
     * @param string $uri
     *
     * @return string
     */
    private function generateSasToken(string $uri): string
    {
        $targetUri = strtolower(rawurlencode(strtolower($uri)));
        $expires = time();
        $week = 60 * 60 * 24 * 7;
        $expires += $week;
        $toSign = $targetUri . "\n" . $expires;
        $signature = rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $this->sasKeyValue, true)));

        $token = 'SharedAccessSignature sr=' . $targetUri . '&sig=' . $signature . '&se=' . $expires . '&skn=' . $this->sasKeyName;
        return $token;
    }

    /**
     * @param NotificationInterface $notification
     * @param string $deviceId
     */
    public function sendNotification(NotificationInterface $notification, string $deviceId): void
    {
        $uri = $this->endpoint . $this->hubPath . '/messages?direct&' . self::API_VERSION;
        $ch = curl_init($uri);

        if (in_array($notification->getFormat(), ['template', 'apple', 'fcm'])) {
            $contentType = 'application/json';
        } else {
            $contentType = 'application/xml';
        }

        $token = $this->generateSasToken($uri);

        $headers = [
            'Authorization' => $token,
            'Content-Type' => $contentType,
            'ServiceBusNotification-Format' => $notification->getFormat(),
            'ServiceBusNotification-DeviceHandle' => $deviceId
        ];

        $headers = array_merge($headers, $notification->getHeaders());

        foreach ($headers as $key => $header) {
            $newHeaders[] = $key.': '.$header;
        }

        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $newHeaders,
            CURLOPT_POSTFIELDS => $notification->getPayload()
        ));

        $response = curl_exec($ch);

        if ($response === false) {
            throw new RuntimeException(curl_error($ch));
        }

        $info = curl_getinfo($ch);

        if ($info['http_code'] !== 201) {
            throw new RuntimeException('Error sending notification: '. $info['http_code'] . ' msg: ' . $response);
        }
    }
}
