<?php

namespace App\Providers;

use App\Jongman\Traits\CustomDate;
use Illuminate\Support\ServiceProvider;

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
