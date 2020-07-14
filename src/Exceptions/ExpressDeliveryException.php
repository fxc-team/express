<?php

namespace Masonx\ExpressDelivery\Exceptions;

use Exception;

class ExpressDeliveryException extends Exception
{

    /**
     * MobileException constructor.
     *
     * @param string $message
     * @param $code 40000失败，20000成功，其他：三方返回的CODE码
     *
     */
    public function __construct($message = "", $code = 40000)
    {
        parent::__construct($message, $code);
    }
}
