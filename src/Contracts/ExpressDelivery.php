<?php

namespace Masonx\ExpressDelivery\Contracts;

use Masonx\ExpressDelivery\Exceptions\MobileException;

interface ExpressDelivery
{

    /**
     * 手机话费充值
     *
     * @param string $phone 要充值的手机号码
     * @param string $money 充值金额，支持20、30、50、100、200、300、500元的面额。
     * @param string $orderId 用户自定义的订单号，不可重复，长度不超过32位，可为英语或数字。如不输入，则系统生成默认值
     * @param string $callback 回调地址，若提交该参数，请指定协议名，如http
     * @return array
     */
    public function mobileTopUp(string $phone, string $money, string $orderId = '', string $callback = ''): array;

    /**
     * 价格查询
     *
     * @param string $province 要充值的省区（大陆地区），
     * @param string $money 要查询的面值（'20','30','50','100','200','300','500'）
     * @param string $carrier 要查询的运营商（移动、电信、联通）
     * @return array
     */
    public function inquiryPrice(string $province, string $money, string $carrier = ''): array;


    /**
     * 订单详情
     *
     * @param string $orderId 订单id
     * @return array
     * @throws MobileException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orderDetails(string $orderId): array;
}