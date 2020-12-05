<?php

namespace App\Providers;

use App\Resolvers\SocialUserResolver;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\Carbon;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //config(['app.locale' => 'id']);
        //Carbon::setLocale('id');
        //Carbon::setLocale('id');
        //date_default_timezone_set('Asia/Jakarta');
        Schema::defaultStringLength(191);
    }

    public $bindings = [
        SocialUserResolverInterface::class => SocialUserResolver::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
