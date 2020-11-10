<?php
declare(strict_types=1);

namespace App\Services\Azure\ConnectionStringParser;

use RuntimeException;

class ConnectionStringParser
{
     /**
     * @var string
     */
    private $endpoint;

    /**
     * @var string
     */
    private $sasKeyName;

    /**
     * @var string
     */
    private $sasKeyValue;

    /**
     * @param string $connectionString
     */
    public function parse(string $connectionString): void
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
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @return string
     */
    public function getSasKeyName(): string
    {
        return $this->sasKeyName;
    }

    /**
     * @return string
     */
    public function getSasKeyValue(): string
    {
        return $this->sasKeyValue;
    }
}