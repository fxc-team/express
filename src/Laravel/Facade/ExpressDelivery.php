<?php

namespace Masonx\ExpressDelivery\Laravel\Facade;

use Illuminate\Support\Facades\Facade;

/**
 * Class Mobile
 *
 * @method static createOrderNotify() //新下单服务接口
 *
 * @package app\facade
 * @see \Masonx\ExpressDelivery\Contracts\ExpressDelivery::class
 */
class ExpressDelivery extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'masonx.expressDelivery';
    }
}