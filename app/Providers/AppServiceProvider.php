<?php

namespace App\Providers;

use App\Repositories\Auth\UserRepository;
use App\Repositories\Category\CategoryRepository;
use App\Repositories\Counter\CounterRepository;
use App\Repositories\Order\OrderRepository;
use App\Repositories\Product\ProductRepository;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Repositories\Auth\IUserRepository', function ($app) {
            return new UserRepository();
        });

        $this->app->bind('App\Repositories\Counter\ICounterRepository', function ($app) {
            return new CounterRepository();
        });

        $this->app->bind('App\Repositories\Product\IProductRepository', function ($app) {
            return new ProductRepository();
        });

        $this->app->bind('App\Repositories\Category\ICategoryRepository', function ($app) {
            return new CategoryRepository();
        });

        $this->app->bind('App\Repositories\Order\IOrderRepository', function ($app) {
            return new OrderRepository();
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
