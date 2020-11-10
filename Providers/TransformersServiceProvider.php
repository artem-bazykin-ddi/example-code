<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Transformers\Activity\ActivityTransformerInterface;
use App\Transformers\UserFeedback\UserFeedbackTransformer;
use App\Transformers\UserFeedback\UserFeedbackTransformerInterface;
use App\Transformers\JournalActivity\JournalActivityTransformer;
use App\Transformers\JournalActivity\JournalActivityTransformerInterface;
use App\Transformers\User\UserTransformer;
use App\Transformers\User\UserTransformerInterface;
use Auth;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TransformersServiceProvider extends ServiceProvider
{
    protected const MOBILE_APPLICATION = 'app';
    protected const ADMIN_APPLICATION = 'admin';

    /**
     * @var array
     */
    protected $associations = [
        UserTransformerInterface::class => [
            self::MOBILE_APPLICATION => UserTransformer::class,
            self::ADMIN_APPLICATION => UserTransformer::class

        ],
        ActivityTransformerInterface::class => [
            self::MOBILE_APPLICATION => ActivityTransformerInterface::class,
            self::ADMIN_APPLICATION => ActivityTransformerInterface::class
        ],
        UserFeedbackTransformerInterface::class => [
            self::MOBILE_APPLICATION => UserFeedbackTransformer::class,
            self::ADMIN_APPLICATION => UserFeedbackTransformer::class
        ],
    ];

    /**
     * @var array
     */
    protected $userAssociations = [
        User::APP_USER => self::MOBILE_APPLICATION,
        User::ADMIN => self::ADMIN_APPLICATION,
    ];

    public function register(): void
    {
        foreach ($this->associations as $interface => $realisation) {
            $this->app->bind($interface, function () use ($interface) {
                $user = Auth::user();
                if ($user && array_key_exists($user->permission, $this->userAssociations)) {
                    return $this->app->get($this->associations[$interface][$this->userAssociations[$user->permission]]);
                }

                return $this->app->get($this->associations[$interface][$this->userAssociations[User::APP_USER]]);
            });
        }
    }
}
