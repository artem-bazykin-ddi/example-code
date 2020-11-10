<?php
declare(strict_types=1);

namespace App\Services\Azure\NotificationHubService;

use App\Services\Azure\AzureSender\Exception\SenderException;
use App\Services\Azure\AzureSender\RequestSender;
use App\Services\Azure\NotificationHubService\Exception\DeviceRegistrationException;
use App\Services\Azure\NotificationHubService\Response\DeviceRegistrationResponse;
use LogicException;

class NotificationHubService
{
    public const APPLE_DEVICE = 'apple';
    public const GCM_DEVICE = 'fcm';

    protected const NOTIFICATION_SEND_URI = 'messages?direct&api-version=2015-04';
    protected const REGISTER_DEVICE_URI = 'registrations?api-version=2015-01';

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $hubPath;

    /**
     * @var RequestSender
     */
    private $requestSender;

    /**
     * NotificationHubService constructor.
     *
     * @param RequestSender $requestSender
     */
    public function __construct(RequestSender $requestSender)
    {
        $this->requestSender = $requestSender;

    }

    /**
     * @param string $deviceToken
     * @param string $payload
     * @param array $headers
     *
     * @return bool
     * @throws SenderException
     */
    public function sendNotification(string $deviceToken, string $payload, array $headers = []): bool
    {
        $headers = array_merge($headers, ['ServiceBusNotification-DeviceHandle' => $deviceToken]);
        return !$this->requestSender->sendRequest(self::NOTIFICATION_SEND_URI, $payload, $headers);
    }

    /**
     * @param string $type
     * @param string $token
     *
     * @return DeviceRegistrationResponse
     * @throws DeviceRegistrationException
     */
    public function registerDevice(string $type, string $token): DeviceRegistrationResponse
    {
        switch ($type) {
            case self::APPLE_DEVICE:
                $body = $this->appleDeviceRegisterBody($token);
                break;
            case self::GCM_DEVICE:
                $body = $this->GCMDeviceRegisterBody($token);
                break;
            default:
                throw new LogicException('Could not resolve device type '.$type);
        }

        $headers['Content-Type'] = 'application/atom+xml;type=entry;charset=utf-8';

        try {
            $response = $this->requestSender->sendRequest(self::REGISTER_DEVICE_URI, $body, $headers);

            return new DeviceRegistrationResponse(
                (string) $response->content->AppleRegistrationDescription->RegistrationId,
                (string) $response->content->AppleRegistrationDescription->DeviceToken,
                (string) $response->content->AppleRegistrationDescription->ExpirationTime,
                (string) $response->content->AppleRegistrationDescription->ETag
            );
        } catch (SenderException $exception) {
            throw new DeviceRegistrationException($exception->getMessage());
        }
    }

    /**
     * @param string $token
     * @param array $tags
     *
     * @return string
     */
    protected function appleDeviceRegisterBody(string $token, $tags = []): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>
            <entry xmlns="http://www.w3.org/2005/Atom">
                <content type="application/xml">
                    <AppleRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
                        <DeviceToken>'.$token.'</DeviceToken> 
                    </AppleRegistrationDescription>
                </content>
            </entry>';
    }

    /**
     * @param string $token
     * @param array $tags
     *
     * @return string
     */
    protected function GCMDeviceRegisterBody(string $token, $tags = []): string
    {
        return '<?xml version="1.0" encoding="utf-8"?>
            <entry xmlns="http://www.w3.org/2005/Atom">
                <content type="application/xml">
                    <GcmRegistrationDescription xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://schemas.microsoft.com/netservices/2010/10/servicebus/connect">
                        <GcmRegistrationId>'.$token.'</GcmRegistrationId> 
                    </GcmRegistrationDescription>
                </content>
            </entry>';
    }
}
