<?php
declare(strict_types=1);

namespace App\Providers;

use App\Http\Responses\PaginatedListResponse;
use App\Http\Responses\ListResponseInterface;
use App\Http\Responses\ListResponse;
use App\Models\User;
use Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class ResponseServiceProvider extends ServiceProvider
{
    protected const MOBILE_APPLICATION = 'app';
    protected const ADMIN_APPLICATION = 'admin';

    /**
     * @var array
     */
    protected $associations = [
        ListResponseInterface::class => [
            self::MOBILE_APPLICATION => ListResponse::class,
            self::ADMIN_APPLICATION => PaginatedListResponse::class,
        ]
    ];

    protected $pathAssociations = [
        ListResponseInterface::class => [
            'api.activity.me' => PaginatedListResponse::class,
            'api.activity.company' => PaginatedListResponse::class,
            'api.activity.journal' => PaginatedListResponse::class,
        ]
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
            $this->app->bind($interface, function () use ($interface, $realisation) {

                if (array_key_exists(Route::currentRouteName(), $this->pathAssociations[$interface])) {
                    $class = $this->pathAssociations[$interface][Route::currentRouteName()];

                    return $this->app->get($class);
                }
                $user = Auth::user();
                if ($user && array_key_exists($user->permission, $this->userAssociations)) {
                    return new $this->associations[$interface][$this->userAssociations[$user->permission]];
                }

                return new $this->associations[$interface][self::ADMIN_APPLICATION];
            });
        }
    }
}
