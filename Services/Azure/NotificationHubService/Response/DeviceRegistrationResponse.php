<?php
declare(strict_types=1);

namespace App\Services\Azure\NotificationHubService\Response;

class DeviceRegistrationResponse
{
    /**
     * @var string
     */
    private $registrationId;

    /**
     * @var string
     */
    private $deviceToken;

    /**
     * @var string
     */
    private $expirationTime;

    /**
     * @var string
     */
    private $ETag;

    /**
     * DeviceRegistrationResponse constructor.
     *
     * @param string $registrationId
     * @param string $deviceToken
     * @param string $expirationTime
     * @param string $ETag
     */
    public function __construct(string $registrationId, string $deviceToken, string $expirationTime, string $ETag)
    {
        $this->registrationId = $registrationId;
        $this->deviceToken = $deviceToken;
        $this->expirationTime = $expirationTime;
        $this->ETag = $ETag;
    }

    /**
     * @return string
     */
    public function getRegistrationId(): string
    {
        return $this->registrationId;
    }

    /**
     * @return string
     */
    public function getDeviceToken(): string
    {
        return $this->deviceToken;
    }

    /**
     * @return string
     */
    public function getExpirationTime(): string
    {
        return $this->expirationTime;
    }

    /**
     * @return string
     */
    public function getETag(): string
    {
        return $this->ETag;
    }
}
