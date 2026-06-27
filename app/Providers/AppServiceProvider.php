<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;

class AppServiceProvider extends ServiceProvider
{
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
        Blade::directive('money', function ($expression) {
            return "<?php echo format_money($expression); ?>";
        });

        Event::listen(Login::class, function (Login $event) {
            /** @var \App\Models\User $user */
            $user = $event->user;
            $user->update([
                'last_login_at' => now(),
            ]);
        });
    }
}
