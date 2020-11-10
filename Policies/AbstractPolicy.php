<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Client;
use App\Models\Interfaces\AuditableInterface;
use App\Models\User;

abstract class AbstractPolicy
{
    /**
     * @param User $user
     * @param array|int $roles
     *
     * @return bool
     */
    protected static function roleIn(User $user, $roles): bool
    {
        if (is_array($roles)) {
            return in_array($user->permission, $roles, true);
        }

        return $user->permission !== null && $user->permission === $roles;
    }

    /**
     * @param User $user
     * @param AuditableInterface $model
     *
     * @return bool
     */
    protected static function ownedByHisClient(User $user, AuditableInterface $model): bool
    {
        return $user->client_id !== null && $model->getCreatedBy()
            && self::roleIn($model->getCreatedBy(), User::$clientRoles)
            && $user->client_id === $model->getCreatedBy()->client_id;
    }

    /**
     * @param AuditableInterface $model
     *
     * @return bool
     */
    protected static function ownedByLi(AuditableInterface $model): bool
    {
        return $model->getCreatedBy() === null
            || ($model->getCreatedBy() !== null && self::roleIn($model->getCreatedBy(), User::$liRoles) && $model->getCreatedBy()->client_id === null);
    }

    /**
     * @param User $user
     * @param AuditableInterface $model
     *
     * @return bool
     */
    protected static function canRead(User $user, AuditableInterface $model): bool
    {
        $client = $user->client;

        if ($client instanceof Client && $client->deactivated_at === null && self::roleIn($user, User::$clientRoles)) {
            if ($client->content_model === Client::CONTENT_ONLY) {
                return !self::ownedByHisClient($user, $model) && self::ownedByLi($model);
            }

            if ($client->content_model === Client::BLANK) {
                return self::ownedByHisClient($user, $model) && !self::ownedByLi($model);
            }

            if ($client->content_model === Client::MIXED_CONTENT) {
                return self::ownedByHisClient($user, $model) || self::ownedByLi($model);
            }
        }

        return self::roleIn($user, User::$liRoles);
    }

    /**
     * @param User $user
     * @param int|array $contentModels
     *
     * @return bool
     */
    public static function contentModelIn(User $user, $contentModels): bool
    {
        if (is_array($contentModels)) {
            return in_array($user->client->content_model, $contentModels, true);
        }

        return $user->client->content_model === (int) $contentModels;
    }
}
