<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\Api\InviteEmailService;
use Illuminate\Support\ServiceProvider;

class InviteEmailServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(InviteEmailService::class);
    }
}
