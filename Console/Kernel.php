<?php
declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\ChangeUserPasswordCommand;
use App\Console\Commands\Debug\IsSecureCommand;
use App\Console\Commands\EventsScheduledCommand;
use App\Console\Commands\MigrateFilesystemCommand;
use App\Console\Commands\MigrationDBCommand;
use App\Console\Commands\Send\SendPushNotificationCommand;
use App\Console\Commands\CreateActivity;
use App\Console\Commands\Debug\DebugDevicesCommand;
use App\Console\Commands\Debug\DebugSASTokenCommand;
use App\Console\Commands\Debug\DebugScheduledEvents;
use App\Console\Commands\RegisterDevicesCommand;
use App\Console\Commands\Send\SendRandomTipsCommand;
use App\Console\Commands\ShowFilesystemCommand;
use App\Console\Commands\TestPush;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // DELETED
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SendPushNotificationCommand::class)->everyMinute()->sendOutputTo(storage_path('logs/push/events.log'));
        $schedule->command(SendRandomTipsCommand::class)->everyMinute()->sendOutputTo(storage_path('logs/push/tips.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
