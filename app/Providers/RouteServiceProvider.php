<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });


        $this->routes(function () {
            Route::get('/', function (Request $request, ResponseFactory $response) {
                $data = [
                    'APP_NAME' => config('app.name'),
                    'RECCOMENDED_API_VERSION' => 'v1'
                ];

                return $request->expectsJson()
                    ? $response->json($data)
                    : $data;
            });

            Route::middleware('api')
                ->prefix('v1')
                ->group(base_path("routes/v1.php"));
        });
    }
}
