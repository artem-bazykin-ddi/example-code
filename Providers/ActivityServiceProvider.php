<?php
declare(strict_types=1);

namespace App\Providers;

use App\Transformers\Activity\ActivityCompanyTransformer;
use App\Transformers\Activity\ActivityMeTransformer;
use App\Transformers\Activity\ActivityTransformerInterface;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class ActivityServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $associations = [
        ActivityTransformerInterface::class => [
            'api.activity.me' => ActivityMeTransformer::class,
            'api.activity.company' => ActivityCompanyTransformer::class,
        ]
    ];

    public function register(): void
    {
        foreach ($this->associations as $interface => $value) {
            $this->app->bind($interface, function () use ($value, $interface) {
                if (array_key_exists(Route::currentRouteName(), $value)) {
                    $class = $value[Route::currentRouteName()];

                    return $this->app->get($class);
                }

                return $this->app->get($this->associations[$interface][array_key_first($value)]);
            });
        }
    }
}
