<?php
declare(strict_types=1);

namespace App\Services\Api;

use App\Factory\InviteEmailFactory;
use App\Http\DataResponse;
use App\Http\Requests\Api\InviteEmail\CreateMobileRequest;
use App\Mail\AuthEmail;
use App\Manager\InviteEmailManager;
use App\Manager\UserManager;
use App\Models\Client;
use App\Models\InviteEmail;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class InviteEmailService
{
    private $mailer;
    /**
     * @var InviteEmailFactory
     */
    private $inviteEmailFactory;
    /**
     * @var InviteEmailManager
     */
    private $inviteEmailManager;
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * InviteEmailService constructor.
     *
     * @param InviteEmailFactory $inviteEmailFactory
     * @param InviteEmailManager $inviteEmailManager
     * @param UserManager $userManager
     * @param Mail $mail
     */
    public function __construct(InviteEmailFactory $inviteEmailFactory, InviteEmailManager $inviteEmailManager, UserManager $userManager, Mail $mail)
    {
        $this->mailer = $mail;
        $this->inviteEmailFactory = $inviteEmailFactory;
        $this->inviteEmailManager = $inviteEmailManager;
        $this->userManager = $userManager;
    }

    /**
     * @param CreateMobileRequest $request
     *
     * @return DataResponse
     */
    public function inviteEmail(CreateMobileRequest $request): DataResponse
    {
        $this->inviteEmailFactory->create($request->validated()['email']);

        return new DataResponse(Response::HTTP_OK, [
            'message' => 'success operation'
        ]);
    }

    /**
     * @param string $email
     *
     * @return DataResponse
     */
    public function sendInvite(string $email): DataResponse
    {
        //only for QA from stores

        if ($email && substr($email, 0, 2) === '$$') {
            $email = substr($email, 2);
            $user = $this->userManager->create($email, '', '', '', '', '', User::APP_USER, Client::find(Client::DEFAULT_CLIENT_ID));
            $user->save();
        }

        $user = User::findByEmail($email);

        if ($user instanceof User) {
            $this->userManager->generateMagicToken($user);
            $user->save();
            $this->mailer::to($user->email)->send(new AuthEmail($user));

            return new DataResponse(Response::HTTP_OK, ['message' => 'Success operation']);
        }

        $inviteEmail = InviteEmail::findByEmail($email);

        if ($inviteEmail instanceof InviteEmail) {
            if (!$inviteEmail->token) {
                $this->inviteEmailManager->generateMagicToken($inviteEmail);
            }
        } else {
            $inviteEmail = $this->inviteEmailFactory->create($email);
        }

        $inviteEmail->save();

        return new DataResponse(Response::HTTP_BAD_REQUEST, ['message' => 'User not found']);
    }

    /**
     * @param $email
     *
     * @return DataResponse
     */
    public function getInvite($email): DataResponse
    {
        $user = $this->userManager::getModel()::findByEmail($email);

        if ($user instanceof User) {
            $this->userManager->generateMagicToken($user);
            $user->save();

            return new DataResponse(Response::HTTP_OK, ['message' => 'Success operation', 'token' => $user->getMagicToken()]);
        }

        $inviteEmail = InviteEmail::findByEmail($email);

        if ($inviteEmail instanceof InviteEmail) {
            if (!$inviteEmail->token) {
                $this->inviteEmailManager->generateMagicToken($inviteEmail);
            }
        } else {
            $inviteEmail = $this->inviteEmailFactory->create($email);
        }

        $inviteEmail->save();

        return new DataResponse(Response::HTTP_BAD_REQUEST, ['message' => 'User not found']);
    }
}
