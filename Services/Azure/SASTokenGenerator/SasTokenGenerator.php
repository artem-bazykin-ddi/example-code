<?php
declare(strict_types=1);

namespace App\Services\Azure\SASTokenGenerator;

use App\Services\Azure\ConnectionStringParser\ConnectionStringParser;
use Carbon\Carbon;

class SasTokenGenerator
{
    /**
     * @var ConnectionStringParser
     */
    protected $connectionStringParser;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * NotificationHub constructor.
     *
     * @param ConnectionStringParser $connectionStringParser
     * @param string $connectionString
     */
    public function __construct(ConnectionStringParser $connectionStringParser, string $connectionString)
    {
        $this->connectionStringParser = $connectionStringParser;
        $this->connectionStringParser->parse($connectionString);
        $this->endpoint = $connectionStringParser->getEndpoint();
        $connectionStringParser->parse($connectionString);
    }

    /**
     * @param string $uri
     *
     * @return string
     */
    public function generate(string $uri): string
    {
        $targetUri = strtolower(rawurlencode(strtolower($uri)));
        /** @var Carbon $expires */
        $expires = time();
        $expiresInMin = 60;
        $expires += $expiresInMin * 60;
        $toSign = $targetUri . "\n" . $expires;
        $signature = rawurlencode(base64_encode(hash_hmac('sha256', $toSign, $this->connectionStringParser->getSasKeyValue(), true)));

        $token = 'SharedAccessSignature sr=' . $targetUri . '&sig=' . $signature . '&se=' . $expires . '&skn=' . $this->connectionStringParser->getSasKeyName();

        return $token;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }
}
