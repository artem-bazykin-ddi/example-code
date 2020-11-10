<?php
declare(strict_types=1);

namespace App\Services\PushService\Types;

use LogicException;

class AppleNotification implements NotificationInterface
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var string
     */
    private $body;

    /**
     * @var array|null
     */
    private $additionalData = [];

    /**
     * AppleNotification constructor.
     *
     * @param string $title
     * @param string $subtitle
     * @param string $body
     * @param array|null $additionalData
     */
    public function __construct(string $title, string $subtitle, string $body, ?array $additionalData = null)
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
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
        $alert = [
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'body' => $this->body
        ];

        if ($this->additionalData) {
            $alert = array_merge($alert, $this->additionalData);
        }

        $message = [
            'aps' => [
                'alert' => $alert
            ]
        ];

        return json_encode($message, JSON_THROW_ON_ERROR);
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
        if (array_key_exists('subtitle', $data)) {
            throw new LogicException('Additional data could not replace subtitle filed');
        }
        if (array_key_exists('body', $data)) {
            throw new LogicException('Additional data could not replace body filed');
        }

        return true;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json;charset=utf-8.',
            'ServiceBusNotification-Format' => self::getFormat()
        ];
    }

    /**
     * @return string
     */
    public static function getFormat(): string
    {
        return 'apple';
    }
}
