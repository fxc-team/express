<?php

namespace Masonx\ExpressDelivery\Laravel;

use Illuminate\Support\ServiceProvider;
use Masonx\ExpressDelivery\ExpressDelivery;

class ExpressDeliveryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/masonxExpressDelivery.php', 'masonxExpressDelivery'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Config/masonxExpressDelivery.php' => config_path('masonxExpressDelivery.php'),
        ], 'masonxExpressDelivery');

        $this->app->bind("masonx.expressDelivery", function () {
            $connect = config('masonxExpressDelivery.connect.' . config('masonxExpressDelivery.default'));
            //参数设置
            $expressDelivery = new ExpressDelivery($connect['appkey'],$connect['sign'],$connect['companyCode']);
            return $expressDelivery;
        });
    }
}
