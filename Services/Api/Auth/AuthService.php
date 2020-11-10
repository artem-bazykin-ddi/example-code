<?php
declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Factory\UserFactory;
use App\Http\DataResponse;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Mail\AuthEmail;
use App\Manager\UserManager;
use App\Models\User;
use App\Transformers\Auth\AuthUserTransformer;
use Exception;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class AuthService
{
    /**
     * @var Mail
     */
    private $mailService;

    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var Hasher
     */
    private $hasher;

    /**
     * AuthService constructor.
     *
     * @param Mail $mailService
     * @param UserFactory $userFactory
     * @param UserManager $userManager
     * @param Hasher $hasher
     */
    public function __construct(
        Mail $mailService,
        UserFactory $userFactory,
        UserManager $userManager,
        Hasher $hasher
    ) {
        $this->mailService = $mailService;
        $this->userFactory = $userFactory;
        $this->userManager = $userManager;
        $this->hasher = $hasher;
    }

    /**
     * @param LoginRequest $request
     *
     * @return DataResponse
     */
    public function login(LoginRequest $request): DataResponse
    {
        $user = $this->userManager::getModel()::findByEmail($request->get('email'));

        if ($user === null) {
            $user = $this->userFactory->createAppUserWithMagicLinkToken($request->validated()['email']);
            $user->save();

            $this->mailService::to($user->email)->send(new AuthEmail($user));

            return new DataResponse(Response::HTTP_OK, ['message' => 'Success operation']);
        }

        $this->mailService::to($user->email)->send(new AuthEmail($user));

        return new DataResponse(Response::HTTP_OK, ['message' => 'Success operation']);
    }

    /**
     * @param string $magicLinkToken
     *
     * @return DataResponse
     *
     * @throws Exception
     */
    public function check(string $magicLinkToken): DataResponse
    {
        $user = $this->userManager::getModel()::getByMagicToken($magicLinkToken);

        if (!$user instanceof User) {
            return new DataResponse(Response::HTTP_BAD_REQUEST, ['message' => 'User not found']);
        }

        $this->userManager->setConfirm($user);

        return new DataResponse(Response::HTTP_OK, [
            'message' => 'Account successfully confirmed',
            'token'   => $user->token,
            'is_onboarded' => $user->onboarding_at !== null,
        ]);
    }

    /**
     * @param array $data
     * @return DataResponse
     */
    public function authenticateAdmin(array $data): DataResponse
    {
        $user = User::query()->findByAdminRoles()
            ->getByEmail($data['email'])->first();

        if (!$user instanceof User || ($user && !$this->hasher->check($data['password'], $user->password))) {
            return new DataResponse(Response::HTTP_BAD_REQUEST, [
                'message' => 'Email or password is wrong'
            ]);
        }

        return new DataResponse(Response::HTTP_OK, fractal($user, new AuthUserTransformer())->toArray());
    }
}
