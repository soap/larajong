<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Jongman\Traits\CustomDate;

class AppServiceProvider extends ServiceProvider
{
    use CustomDate;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCustomDateFunction();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
