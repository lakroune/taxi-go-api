<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Laravel\Reverb\Events\ConnectionClosed;
use App\Listeners\HandleDriverDisconnect;
class AppServiceProvider extends ServiceProvider
{
    protected $listen = [
        'Laravel\Reverb\Events\ConnectionClosed' => [
            'App\Listeners\HandleDriverDisconnect',
        ],
    ];
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url') . "/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }
}
