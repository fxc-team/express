# masonx/express-delivery

话费SDK  

兼容框架：

|  名称   | 是否支持 |
|  :----:     | :----: |
|  Laravel 6.x  | :white_check_mark: |
|  swoft 2.x  | :x: |
|  TP 6.x  | :x: |


涉及第三方

|  三方名称   | 标识  |  是否启用    | 
|  :----:    | :----:  | :----:  |
| 德邦 | deppon | :white_check_mark: |

## 环境要求
PHP >= 7.1

## 安装
在composer.json添加
~~~
"repositories": {
    "masonx": {
        "type": "vcs",
        "url": "https://github.com/jian-D/express.git"
    }
}
~~~

然后执行
~~~
composer require masonx/express-delivery ^1.0
~~~


## Laravel使用
框架环境 laravel >= 6.0


##返回参数说明

>{responseCode:20000,responseMessage:"success", responseData: { code: "", message: "操作成功", data: [], channel: "piaoduoduo" }}

- 1.responseCode 状态码 20000
- 2.responseMessage 文案 
- 3.responseData 数据
- 3.1 code 业务状态码 ""
- 3.2 message 业务提示文案 
- 3.3 data 业务数据接口体
- 3.4 chanel 第三方渠道