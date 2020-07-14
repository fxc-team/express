<?php
/**
 * 配置文件
 *
 */

return [
    /*
      * 默认配置，将会合并到各模块中
      */
    'default' => 'deppon',

    /*
     * 日志配置
     * type 框架类型 Laravel
     * level: 日志级别，可选为：debug/info/notice/warning/error/critical/alert/emergency
     * file：日志文件位置(绝对路径!!!)，要求可写权限
     */
    'log' => [
        'type' => 'Laravel',
        'level' => env('LARAVEL_LOG_LEVEL', 'debug'),
        'file' => env('LARAVEL_LOG_FILE', ''),
    ],


    //第三方配置
    'connect' => [
        'deppon' => [ //易源
            // 请求方式
            //appkey
            'appkey' => env('DEPPON_APPKEY', ''),

            // api_id
            'sign'  => env('DEPPON_SIGN', ''),

            // companyCode
            'companyCode' => env('DEPPON_COMPANY_CODE', ''),
        ],
    ]

];
