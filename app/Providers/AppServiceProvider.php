<?php

namespace App\Providers;

use App\Repositories\CategoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\UserRepository;
use App\Repositories\CounterRepository;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\IUserRepository', function ($app) {
            return new UserRepository();
        });

        $this->app->bind('App\Repositories\ICounterRepository', function ($app) {
            return new CounterRepository();
        });

        $this->app->bind('App\Repositories\IProductRepository', function ($app) {
            return new ProductRepository();
        });

        $this->app->bind('App\Repositories\ICategoryRepository', function ($app) {
            return new CategoryRepository();
        });

    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
