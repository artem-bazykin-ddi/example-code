<?php
declare(strict_types=1);

namespace App\Providers;

use App\Tools\RouterPath;
use Illuminate\Support\ServiceProvider;

class RoutePathServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind('routerpath', static function () {
            return new RouterPath();
        });

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(): void
    {
        //
    }
}
