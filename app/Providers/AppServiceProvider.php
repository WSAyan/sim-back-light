<?php

namespace App\Providers;

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
        $this->app->bind('App\Repositories\Auth\IUserRepository', 'App\Repositories\Auth\UserRepository');

        $this->app->bind('App\Repositories\Counter\ICounterRepository', 'App\Repositories\Counter\CounterRepository');

        $this->app->bind('App\Repositories\Brand\IBrandRepository', 'App\Repositories\Brand\BrandRepository');

        $this->app->bind('App\Repositories\Product\IProductRepository', 'App\Repositories\Product\ProductRepository');

        $this->app->bind('App\Repositories\Image\IImageRepository', 'App\Repositories\Image\ImageRepository');

        $this->app->bind('App\Repositories\Category\ICategoryRepository', 'App\Repositories\Category\CategoryRepository');

        $this->app->bind('App\Repositories\Order\IOrderRepository', 'App\Repositories\Order\OrderRepository');

        $this->app->bind('App\Repositories\Home\IHomeRepository', 'App\Repositories\Home\HomeRepository');

        $this->app->bind('App\Repositories\ProductOption\IProductOptionRepository', 'App\Repositories\ProductOption\ProductOptionRepository');

        $this->app->bind('App\Repositories\Collection\ICollectionRepository', 'App\Repositories\Collection\CollectionRepository');
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
