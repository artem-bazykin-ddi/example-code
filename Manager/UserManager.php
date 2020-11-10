<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\UserFactory;
use App\Models\Client;
use App\Models\User;
use App\Services\MagicLinkTokenGenerator\MagicLinkTokenGeneratorInterface;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Str;

class UserManager implements ManagerInterface
{
    protected const TOKEN_LENGTH = 32;

    /**
     * @var User
     */
    protected static $model = User::class;

    /**
     * @var UserFactory
     */
    protected $userFactory;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * @var MagicLinkTokenGeneratorInterface
     */
    private $magicLinkTokenGenerator;

    /**
     * UserManager constructor.
     *
     * @param UserFactory $userFactory
     * @param Hasher $hasher
     * @param MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator
     */
    public function __construct(UserFactory $userFactory, Hasher $hasher, MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator)
    {
        $this->userFactory = $userFactory;
        $this->hasher = $hasher;
        $this->magicLinkTokenGenerator = $magicLinkTokenGenerator;
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $password
     * @param string $jobRole
     * @param string $jobDept
     * @param int $permission
     * @param Client|null $client
     *
     * @return User
     */
    public function create(string $email, string $firstName, string $lastName, ?string $password, string $jobRole, string $jobDept, int $permission, ?Client $client = null): User
    {
        $user = $this->userFactory->create($email, $firstName, $lastName, $client);
        $user->job_role = $jobRole;
        $user->job_dept = $jobDept;
        $user->permission = $permission;
        if ($password !== null) {
            $this->setPassword($user, $password);
        }
        $this->generateToken($user);

        if ($client instanceof Client) {
            $user->client()->associate($client);
        }

        $user->save();

        return $user;
    }

    /**
     * @param User $user
     *
     * @throws
     */
    public function setConfirm(User $user): void
    {
        $user->magic_token = null;
        $user->setEmailVerifyAt(new DateTime('now'));
    }

    /**
     * Generate and set access token
     *
     * @param User $user
     */
    public function generateToken(User $user): void
    {
        $user->token = Str::random(self::TOKEN_LENGTH);
    }

    /**
     * @return User
     */
    public static function getModel(): User
    {
        return new self::$model();
    }

    /**
     * @param User $user
     */
    public function generateMagicToken(User $user): void
    {
        if ($user->reset_magic_token) {
            $user->magic_token = $this->magicLinkTokenGenerator->generate();
        }
    }

    /**
     * @param User $user
     * @param Client $client
     */
    public function assignToClient(User $user, Client $client): void
    {
        $user->client()->associate($client);
        $user->save();
    }

    /**
     * @param User $user
     * @param array $data
     * @param Client|null $client
     *
     * @return User
     */
    public function update(User $user, array $data, ?Client $client = null): User
    {
        $user->fill($data);

        if (($user->permission === User::APP_USER || $user->permission === User::CLIENT_ADMIN)
            && $client instanceof Client) {
            $user->client()->associate($client);
        }

        $user->update();

        return $user;
    }

    /**
     * @param User $user
     * @param string $filePath
     */
    public function setAvatar(User $user, string $filePath): void
    {
        $user->avatar = $filePath;
        $user->save();
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws Exception
     */
    public function delete(User $user): bool
    {
        return $user->delete() ?? true;
    }

    /**
     * @param User $user
     * @param string $password
     */
    public function setPassword(User $user, string $password): void
    {
        $user->password = $this->hasher->make($password);
    }

    /**
     * @param User $user
     */
    public function updateLastTipAt(User $user): void
    {
        $user->last_tip_at = Carbon::now();
        $user->save();
    }

    /**
     * @param User $user
     */
    public function onBoarded(User $user): void
    {
        $user->onboarding_at = Carbon::now();
        $user->save();
    }

    /**
     * @param Client $client
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $phone
     * @param string $job_role
     *
     * @return User
     */
    public function createClientUser(Client $client, string $email, string $firstName, string $lastName, string $phone, string $job_role): User
    {
        $user = $this->userFactory->createClientUser($client, $email, $firstName, $lastName, $phone, $job_role);

        $user->permission = User::APP_USER;

        $user->save();

        return $user;
    }
}
