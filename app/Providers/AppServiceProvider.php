<?php

namespace Columbo\Providers;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if (Config::get('app.env') == "production") {
            $this->app->bind('path.public', function() {
                return base_path() . '/../public_html';
            });
        }
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        JsonResource::withoutWrapping();

        Schema::defaultStringLength(191);

        Blade::directive('clean', function ($string) {
            return "<?php echo (Purifier::clean(nl2br($string))); ?>";
        });

        $this->app->singleton(\Parsedown::class);

        Route::bind('user', function ($value) {
            return \Columbo\User::where('username', $value)->firstOrFail();
        });
    }
}
