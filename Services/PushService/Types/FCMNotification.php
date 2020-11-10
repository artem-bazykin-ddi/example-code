<?php
declare(strict_types=1);

namespace App\Services\PushService\Types;

use LogicException;

class FCMNotification implements NotificationInterface
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $body;

    /**
     * @var array|null
     */
    private $additionalData = [];

    /**
     * FCMNotification constructor.
     *
     * @param string $title
     * @param string $body
     * @param array|null $additionalData
     */
    public function __construct(string $title, string $body, ?array $additionalData = null)
    {
        $this->title = $title;
        $this->body = $body;
        if (is_array($additionalData) && $this->validateAdditionalData($additionalData)) {
            $this->additionalData = $additionalData;
        }
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        $data = [
            'title' => $this->title,
            'message' => $this->body,
        ];

        if ($this->additionalData) {
            $data = array_merge($data, $this->additionalData);
        }

        $message = [
            'data' => $data
        ];

        return json_encode($message, JSON_THROW_ON_ERROR);
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json;charset=utf-8.',
            'ServiceBusNotification-Format' => 'gcm'
        ];
    }

    /**
     * @return string
     */
    public static function getFormat(): string
    {
        return 'fcm';
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private function validateAdditionalData(array $data): bool
    {
        if (array_key_exists('title', $data)) {
            throw new LogicException('Additional data could not replace title filed');
        }
        if (array_key_exists('message', $data)) {
            throw new LogicException('Additional data could not replace message filed');
        }

        return true;
    }
}
