<?php
declare(strict_types=1);

namespace App\Services\MagicLinkTokenGenerator;

use Str;

class BaseMagicLinkTokenGeneratorService implements MagicLinkTokenGeneratorInterface
{
    protected const TOKEN_LENGTH = 32;

    /**
     * @return string
     */
    public function generate(): string
    {
        return Str::random(self::TOKEN_LENGTH);
    }
}
