<?php
declare(strict_types=1);

namespace App\Providers;

use App\Services\Azure\AzureSender\RequestSender;
use App\Services\Azure\ConnectionStringParser\ConnectionStringParser;
use App\Services\Azure\NotificationHubService\NotificationHubService;
use App\Services\Azure\SASTokenGenerator\SasTokenGenerator;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AzureServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(SasTokenGenerator::class, static function (Application $application) {
            return new SasTokenGenerator($application->get(ConnectionStringParser::class), (string) config('pushnotification.connection-string'));
        });

        $this->app->bind(NotificationHubService::class, static function (Application $application) {
            return new NotificationHubService(new RequestSender($application->get(SasTokenGenerator::class), (string) config('pushnotification.notification-hub')));
        });
    }
}
