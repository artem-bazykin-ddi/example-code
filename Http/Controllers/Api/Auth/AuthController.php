<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api\Auth;

use App\Exceptions\MagicTokenIsInvalid;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Auth\SetPasswordRequest;
use App\Http\Requests\Api\AuthAdmin\LoginRequest;
use App\Http\Responses\JsonExceptionResponse;
use App\Manager\AuthenticateManager;
use App\Manager\UserManager;
use App\Models\EmailVerification;
use App\Models\User;
use App\Services\Api\Auth\AuthService;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class AuthController extends ApiController
{
    /**
     * @SWG\Post(
     *     path="/auth/verify/{token}",
     *     summary="Verify user account via magic link token",
     *     tags={"Auth"},
     *     @SWG\Parameter(
     *         name="token",
     *         description="Token from user email",
     *         in="path",
     *         type="string",
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="User not found",
     *     ),
     * )
     *
     * @param string $magicLinkToken
     * @param AuthenticateManager $authenticateManger
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function verify(string $magicLinkToken, AuthenticateManager $authenticateManger): JsonResponse
    {
        try {
            $user = $authenticateManger->verifyMagicLink($magicLinkToken);
        } catch (MagicTokenIsInvalid $exception) {
            return new JsonExceptionResponse($exception);
        }

        $this->authorizeForUser($user,'appAuth', User::class);

        return new JsonResponse([
            'message' => 'Success',
            'token'   => $user->token,
            'is_onboarded' => $user->onboarding_at !== null,
        ]);
    }

    /**
     *
     * @SWG\Post(
     *     path="/auth/login",
     *     summary="Authenticate into Admin CMS",
     *     tags={"Auth"},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Admin Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="email",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="password",
     *                 type="string"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/AuthUser"
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param LoginRequest $loginRequest
     * @param AuthService $authService
     * @return JsonResponse
     */
    public function loginAdmin(LoginRequest $loginRequest, AuthService $authService): JsonResponse
    {
        $response = $authService->authenticateAdmin($loginRequest->validated());

        return response()->json($response->getData(), $response->getStatus());
    }

    /**
     *
     * @SWG\Post(
     *     path="/auth/set-password/{token}",
     *     summary="Authenticate into Admin CMS",
     *     tags={"Auth"},
     *     @SWG\Parameter(
     *         name="token",
     *         type="string",
     *         in="path"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Admin Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="password",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="password_confirmation",
     *                 type="string"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/AuthUser"
     *              )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param SetPasswordRequest $request
     * @param UserManager $userManager
     * @param string $token
     *
     * @return JsonResponse
     */
    public function setPassword(SetPasswordRequest $request, UserManager $userManager, string $token): JsonResponse
    {
        $emailVerification = EmailVerification::where('token', $token)
            ->where('active', true)
            ->where('expired_at', '>', Carbon::now())
            ->first();

        if (!$emailVerification instanceof EmailVerification) {
            return new JsonResponse(['error' => 'Verification link is invalid']);
        }

        $user = $emailVerification->user;

        $userManager->setPassword($user, $request->get('password'));

        $user->save();

        return new JsonResponse(['message' => 'Password successful set.']);
    }
}
