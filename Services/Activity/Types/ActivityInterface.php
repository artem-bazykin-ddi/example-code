<?php
declare(strict_types=1);

namespace App\Services\Activity\Types;

use App\Models\User;
use Illuminate\Support\Carbon;


interface ActivityInterface
{

    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return User
     */
    public function getUser(): User;

    /**
     * @return array
     */
    public function getPayloads(): array;

    /**
     * @param array $payloads
     */
    public function setPayloads(array $payloads): void;

    /**
     * @param User $user
     */
    public function setUser(User $user): void;

    /**
     * @return int
     */
    public function getRecordId(): int;

    /**
     * @param int $recordId
     */
    public function setRecordId(int $recordId): void;

    /**
     * @return string
     */
    public function getText(): string;

    /**
     * @return Carbon
     */
    public function getDate(): Carbon;

    /**
     * @param Carbon $date
     */
    public function setDate(Carbon $date): void;

}
