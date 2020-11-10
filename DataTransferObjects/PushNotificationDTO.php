<?php
declare(strict_types=1);

namespace App\DataTransferObjects;

class PushNotificationDTO
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
    private $additionalData;

    /**
     * PushNotification constructor.
     *
     * @param string $title
     * @param string $body
     * @param array|null $additionalData
     */
    public function __construct(string $title, string $body, ?array $additionalData = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->additionalData = $additionalData;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return array|null
     */
    public function getAdditionalData(): ?array
    {
        return $this->additionalData;
    }
}
