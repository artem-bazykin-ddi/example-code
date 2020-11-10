<?php
declare(strict_types=1);

namespace App\Services\MagicLinkTokenGenerator;

interface MagicLinkTokenGeneratorInterface
{
    /**
     * @return string
     */
    public function generate(): string;
}