<?php
declare(strict_types=1);

namespace App\Services\Azure\AzureSender;

use App\Services\Azure\AzureSender\Exception\SenderException;
use App\Services\Azure\SASTokenGenerator\SasTokenGenerator;
use LogicException;
use Sentry;
use SimpleXMLElement;

class RequestSender
{
    /**
     * @var SasTokenGenerator
     */
    private $sasTokenGenerator;

    /**
     * @var string
     */
    private $hubPath;

    /**
     * AzureSender constructor.
     *
     * @param SasTokenGenerator $sasTokenGenerator
     * @param string $hubPath
     */
    public function __construct(SasTokenGenerator $sasTokenGenerator, string $hubPath)
    {
        $this->sasTokenGenerator = $sasTokenGenerator;
        $this->hubPath = $hubPath;
    }

    /**
     * @param string $uri
     * @param string $data
     * @param array $headers
     *
     * @return SimpleXMLElement
     * @throws SenderException
     */
    public function sendRequest(string $uri, string $data, array $headers = []): ?SimpleXMLElement
    {
        $url = $this->sasTokenGenerator->getEndpoint() . $this->hubPath . '/' . $uri;
        $ch = curl_init($url);

        $headers['Authorization'] = $this->sasTokenGenerator->generate($url);

        $newHeaders = [];

        foreach ($headers as $key => $value) {
            $newHeaders[] = $key.': '.$value;
        }

        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $newHeaders,
            CURLOPT_POSTFIELDS => $data
        ));

        $response = curl_exec($ch);

        if ($response === false) {
            throw new LogicException(curl_error($ch));
        }

        $info = curl_getinfo($ch);

//        Sentry::captureMessage(json_encode(['response' => $response], JSON_THROW_ON_ERROR, 512), Sentry\Severity::info());

        if ($response !== '') {
            $response = new SimpleXMLElement($response);
        } else {
            return null;
        }

        if (!in_array($info['http_code'], [200, 201], true)) {
            $message = (string) $response->Detail[0];

            throw new SenderException($message);
        }

        return $response;
    }
}
