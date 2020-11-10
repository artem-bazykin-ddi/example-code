<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

use App\Models\User;

interface AuditableInterface
{
    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User;
}
