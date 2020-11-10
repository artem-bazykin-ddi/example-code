<?php
declare(strict_types=1);

namespace App\Manager;

use App\Exceptions\MagicTokenIsInvalid;
use App\Models\User;

class AuthenticateManager
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * AuthenticateManger constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param $magicLinkToken
     *
     * @return User
     * @throws MagicTokenIsInvalid
     */
    public function verifyMagicLink($magicLinkToken): User
    {
        $user = User::getByMagicToken($magicLinkToken);

        if (!$user instanceof User) {
            throw new MagicTokenIsInvalid();
        }

        $this->userManager->setConfirm($user);

        return $user;
    }
}