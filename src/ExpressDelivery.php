<?php
namespace Masonx\ExpressDelivery;

use GuzzleHttp\Client;
use Masonx\ExpressDelivery\Exceptions\ExpressDeliveryException;

class ExpressDelivery
{
    public $appkey = 'c2e1c8ca3f572054b5c969376d337c45';
    public $sign   = 'UAMZ';
    public $companyCode = 'EWBSZSCMSHDZSWYXGS';

    /**
     * 渠道
     *
     * @var string
     */
    protected $channel = '';

    /**
     * 接口地址
     *
     * @var string
     */
    private $apiUrl = 'http://dpsanbox.deppon.com/sandbox-web/standard-order';

    /**
     * 接口名称
     *
     * @var string
     */
    protected $method = '';

    /**
     * 接口请求数据
     *
     * @var array
     */
    private $parameter = [];

    public function __construct()
    {
        header('Content-Type:application/x-www-form-urlencoded;charset=utf-8');
    }

    /**
     * 魔术方法
     *
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        throw new ExpressDeliveryException('方法不存在');
    }

    /**
     * 刷新api接口
     */
    protected function refreshApiUrl(){
        $this->apiUrl = 'http://dpsanbox.deppon.com/sandbox-web/dop-standard-ewborder';
    }

    /**
     * 获取签名
     *
     * @param string $data
     * @param string $timestamp
     * @return string
     */
    public function getSign(string $jsonParams,string $timestamp)
    {
        $plainText   = $jsonParams.$this->appkey.$timestamp;
        return $sign = base64_encode(md5($plainText));
    }

    /**
     * 【新】下单服务接口
     *
     * @param string $logisticID 渠道单号： 由第三方接入商产生的订单号（生成规则为sign+数字，sign值由双方约定）
     * @param string $companyCode 第三方接入商的公司编码：渠道来源
     * @param string $orderType 下单模式：1、 散客模式（单量较小，平台类，异地调货，退换货等发货地址不固定-需要通知快递员或者司机上门取件打单;整车订单也选此模式）； 2、 大客户模式（仓库发货，固定点出货，单量较大客户自行打印标签，快递员直接盲扫走货）3、同步筛单下单（只支持大客户模式快递下单）
     * @param string $transportType 运输方式/产品类型：（具体传值请与月结合同签订约定的为准，否则可能影响计费） 快递运输方式 : RCP：大件快递360； NZBRH：重包入户； ZBTH：重包特惠； WXJTH：微小件特惠； JJDJ：经济大件； PACKAGE：标准快递； DEAP：特准快件；EPEP ：电商尊享；CITYPIECE：同城当日达 零担运输方式： JZKY：精准空运（仅散客模式支持该运输方式）; JZQY_LONG：精准汽运； JZKH：精准卡航； AGENT_VEHICLE：汽运偏线； DTD：精准大票-经济件； YTY：精准大票-标准件； PCP：精准包裹; 整车运输方式 1.精准整车 JZZC 2.整车配送 ZCPS 3.精准专车 JZZHC 4.精准拼车JZPC
     * @param string $customerCode 客户编码/月结账号：德邦一线营业部给到客户的月结客户编码 ，是一串数字，由营业部给出。沙箱环境，下子母件订单必须传值 219401 或者219402
     * @param array $sender 发货人信息：（是否必须）
     * companyName 发货人公司： 否
     * businessNetworkNo  发货部门编码：否 德邦营业部门编码，若没有同步德邦营业部门数据，可以不填
     * name 发货人名称：是
     * mobile 发货人手机：是
     * phone 发货人电话：否
     * province 发货人省份：是
     * city 发货人城市：是
     * county 发货人区县：是
     * town 发货人乡镇街道：否
     * address 发货人详细地址：是
     * @param array $receiver 收货人信息:（是否必须）
     * toNetworkNo 到达部门编码:否 德邦营业部门编码，若没有同步德邦营业部门数据，可以不填
     * name 收货人名称：是
     * phone 收货人电话：否
     * mobile 收货人手机：是
     * province 收货人省份：是
     * city 收货人城市：是
     * county 收货人区县：是
     * town 收货人镇街道：否
     * address 收货人详细地址：是
     * companyName 收货人公司：否
     * @param array $packageInfo 包裹信息：（是否必须）
     * cargoName 货物名称：是
     * totalNumber 总件数（包裹数）：是
     * totalWeight 总重量：是 单位kg
     * totalVolume 总体积:否 单位m3
     * packageService 包装：否 包装（直接用中文） ： 纸、纤、木箱、木架、托膜、托木（大客户模式下运输方式为零担时必填）
     * deliveryType 送货方式：是 1、自提； 2、送货进仓； 3、送货（不含上楼）； 4、送货上楼； 5、大件上楼
     * gmtCommit 订单提交时间：是 2012-11-27 18:44:19 系统当前时间
     * payType 支付方式：是 0:发货人付款（现付） 1:收货人付款（到付） 2：发货人付款（月结） （电子运单客户不支持寄付）
     * @param string $gmtCommit 2012-11-27 18:44:19 系统当前时间
     * @param string $payType 0:发货人付款（现付） 1:收货人付款（到付） 2：发货人付款（月结） （电子运单客户不支持寄付）
     * @param string $custOrderNo 客户订单号/商户订单号：客户的订单号
     * @param string $mailNo 运单号：预埋单号时传运单号，不传时会返回运单号给客户
     * @param string $needTraceInfo 是否需要订阅轨迹：1：是（为是时要对接轨迹推送接口） 2：否 默认否
     * @param array $addServices 增值服务：否
     * insuranceValue 保价金额：否 可为空 如为空，则FOSS开单时默认为0
     * codType 代收货款类型：否 1：即日退 3：三日退 代收货款金额不为0时，此项客户必填，代收货款金额为0或为空，则代收类型默认为无
     * reciveLoanAccount 代收货款客户账号：否 代收货款金额不为0时，此项客户必填
     * accountName 代收货款客户开户名：否 代收货款金额不为0时，此项客户必填
     * codValue 代收货款金额：否 可为空 如为空，则FOSS开单时默认为0
     * backSignBill 签收回单 是 0:无需返单 1：签收单原件返回 2:客户签收单传真返回 3:运单签收联原件返回 4: 运单到达联传真返回
     * @param string $smsNotify 短信通知:否 Y：需要 N: 不需要
     * @param string $sendStartTime 上门接货开始时间：否 方便上门接货的时间范围
     * @param string $sendEndTime 上门接货结束时间：否 方便上门接货的时间范围
     * @param string $originalWaybillNumber 原运单号：否 异地调货退货场景可能用到
     * @param string $remark 备注：否 注意事项（备注）
     * @param string $isOut 是否外发：否 Y：需要 N: 不需要（仅适用于大客户模式下运输方式为零担时，此字段必填；不适用于快递）
     * @param string $passwordSigning 是否口令签收：否 仅适用于快递，Y：需要 N: 不需要；若为Y，必须收货人提供验证码给快递员才能签收，该服务是有偿的，具体费用请让我司收货营业部联系张宁（491407），请慎重使用！
     * @param string $Isdispatched 是否可派送: 否 Y：是 N:否
     * @param string $Ispresaleorder 是否预售单: 否 Y：是N: 否
     * @param string $isPickupSelf 是否合伙人自提:否 Y：是N: 否（只适用于同步筛单下单模式）
     * @param array $orderExtendFields 扩展字段：否 如需传值货物唯一码，key值（变量名）必须为custewb_number，value值为货物唯一码，以逗号分隔，且唯一码数量与件数一致，每个唯一码长度50
     * value 订单扩展属性值：否
     * key 订单扩展属性键：否
     *
     * 接口成功例子
     * {
    "result":"true",
    "reason":"成功",
    "mailNo":"6229932829",
    "logisticID":"UAMZ33343111111123",
    "resultCode":"1000",
    "uniquerRequestNumber":"38110626673804992"
    }
     * 接口失败例子
     * {
    "result":"false",
    "reason":"渠道单号或运单号重复！",
    "resultCode":"2006",
    "uniquerRequestNumber":"38111022395295356"
    }
     *
     * @return array
     */
    public function createOrderNotify(
        string $logisticID
        ,string $companyCode
        ,string $orderType
        ,string $transportType
        ,string $customerCode
        ,array $sender
        ,array $receiver
        ,array $packageInfo
        ,string $gmtCommit
        ,string $payType
        ,string $custOrderNo = ''
        ,string $mailNo = ''
        ,string $needTraceInfo = ''
        ,array $addServices = []
        ,string $smsNotify = ''
        ,string $sendStartTime = ''
        ,string $sendEndTime = ''
        ,string $originalWaybillNumber = ''
        ,string $remark = ''
        ,string $isOut = ''
        ,string $passwordSigning = ''
        ,string $isdispatched = ''
        ,string $ispresaleorder = ''
        ,string $isPickupSelf = ''
        ,array $orderExtendFields = []
    ): array {
        $this->refreshApiUrl();
        //必须参数
        $this->parameter = [
            'logisticID'=>$logisticID,
            'companyCode'=>$companyCode,
            'orderType'=>$orderType,
            'transportType'=>$transportType,
            'customerCode'=>$customerCode,
            'sender'=>$sender,
            'receiver'=>$receiver,
            'packageInfo'=>$packageInfo,
            'gmtCommit'=>$gmtCommit,
            'payType'=>$payType,
        ];
        //不必须参数
        if(!empty($custOrderNo)){
            $this->parameter['custOrderNo'] = $custOrderNo;
        }
        if(!empty($mailNo)){
            $this->parameter['mailNo'] = $mailNo;
        }
        if(!empty($needTraceInfo)){
            $this->parameter['needTraceInfo'] = $needTraceInfo;
        }
        if(!empty($addServices)){
            $this->parameter['addServices'] = $addServices;
        }
        if(!empty($smsNotify)){
            $this->parameter['smsNotify'] = $smsNotify;
        }
        if(!empty($sendStartTime)){
            $this->parameter['sendStartTime'] = $sendStartTime;
        }
        if(!empty($sendEndTime)){
            $this->parameter['sendEndTime'] = $sendEndTime;
        }
        if(!empty($originalWaybillNumber)){
            $this->parameter['originalWaybillNumber'] = $originalWaybillNumber;
        }
        if(!empty($remark)){
            $this->parameter['remark'] = $remark;
        }
        if(!empty($isOut)){
            $this->parameter['isOut'] = $isOut;
        }
        if(!empty($passwordSigning)){
            $this->parameter['passwordSigning'] = $passwordSigning;
        }
        if(!empty($isdispatched)){
            $this->parameter['isdispatched'] = $isdispatched;
        }
        if(!empty($ispresaleorder)){
            $this->parameter['ispresaleorder'] = $ispresaleorder;
        }
        if(!empty($isPickupSelf)){
            $this->parameter['isPickupSelf'] = $isPickupSelf;
        }
        if(!empty($orderExtendFields)){
            $this->parameter['orderExtendFields'] = $orderExtendFields;
        }
        $this->method = '/createOrderNotify.action';
        return $this->request();
    }

    /**
     * 修改订单
     *
     * @param string $logisticCompanyID 物流公司ID 为DEPPON
     * @param string $logisticID 渠道单号 由第三方接入商产生的订单号
     * @param string $orderSource 订单来源 与companyCode保持一致
     * @param string $customerCode 客户编码 与德邦crm中的客户编码保持一致
     * @param string $gmtCommit 订单提交时间 2012-11-27 18:44:19
     * @param string $cargoName 货物名称
     * @param string $payType 支付方式 0:发货人付款（现付）; 1:收货人付款（到付）; 2：发货人付款（月结）
     * @param string $transportType 运输方式 HK_JZKY:精准空运 QC_JZKH:精准卡航 QC_JZQYC:精准汽运（长） QC_JZQYD:精准汽运（短） QC_JZCY:精准城运; PACKAGE： 标准快递; RCP :360特惠件; EPEP:电商尊享;
     * @param string $vistReceive 上门接货 是:Y 否：N
     * @param string $serviceType 服务类型 1:零担在线定单 2．快递在线下单
     * @param string $businessNetworkNo 发货部门编码 德邦营业部门编码，若没有同步德邦营业部门数据，可以不填
     * @param string $toNetworkNo 到达部门编码 德邦营业部门编码，若没有同步德邦营业部门数据，可以不填
     * @param array $sender 发货人信息
     * @param array $receiver 收货人信息
     * @param string $special 特殊商品性质 普通[0]、易碎[1]、液态[2]、化学品[3]、白色粉末状[4]、香烟[5]
     * @param string $totalNumber 总件数 电子面单默认为1，可不填
     * @param string $totalWeight 总重量 单位kg
     * @param string $totalVolume 总体积 单位m3
     * @param string $codType 代收货款类型 3：三日退 1：即日退
     * @param string $codValue 代收货款
     * @param string $sendStartTime 上门接货开始时间
     * @param string $sendEndTime 上门接货结束时间
     * @param string $deliveryType 送货方式 0:自提 1:送货（不含上楼） 2:机场自提 3:送货上楼
     * @param string $backSignBill 签收回单 0:无需返单 1:客户签收单原件返回 2:客户签收单传真返回 4: 运单到达联传真返回
     * @param string $packageService 包装 包装（直接用中文） ： 纸、纤、木箱、木架、托膜、托木
     * @param string $smsNotify 短信通知 Y：需要 N: 不需要
     * @param string $remark 备注 注意事项（备注）
     * @param string $insuranceValue 保价金额 单位：元
     * @param string $mailNo 运单号 orderType 为 0时，运单号mailNo必填
     * @param string $orderType 订单类型 0：电子运单，2：散客电子运单
     * @return array
     *
     * 返回成功例子
     * {"logisticID":"UAMZ33343111213345","result":"true","reason":"成功(沙箱环境仅供参考)","resultCode":"1000","uniquerRequestNumber":"38545342800362280"}
     */
    public function updateOrder(
        string $logisticCompanyID
        ,string $logisticID
        ,string $orderSource
        ,string $customerCode
        ,string $gmtCommit
        ,string $cargoName
        ,string $payType
        ,string $transportType
        ,string $vistReceive
        ,string $serviceType = ''
        ,string $businessNetworkNo = ''
        ,string $toNetworkNo = ''
        ,array $sender = []
        ,array $receiver = []
        ,string $special = ''
        ,string $totalNumber = ''
        ,string $totalWeight = ''
        ,string $totalVolume = ''
        ,string $codType = ''
        ,string $codValue = ''
        ,string $sendStartTime = ''
        ,string $sendEndTime = ''
        ,string $deliveryType = ''
        ,string $backSignBill = ''
        ,string $packageService = ''
        ,string $smsNotify = ''
        ,string $remark = ''
        ,string $insuranceValue = ''
        ,string $mailNo = ''
        ,string $orderType = ''
    ): array{
        //必须参数
        $this->parameter = [
            'logisticCompanyID'=>$logisticCompanyID,
            'logisticID'=>$logisticID,
            'orderSource'=>$orderSource,
            'customerCode'=>$customerCode,
            'gmtCommit'=>$gmtCommit,
            'cargoName'=>$cargoName,
            'payType'=>$payType,
            'transportType'=>$transportType,
            'vistReceive'=>$vistReceive,
        ];

        //不必须参数
        if(!empty($serviceType)){
            $this->parameter['serviceType'] = $serviceType;
        }
        if(!empty($businessNetworkNo)){
            $this->parameter['businessNetworkNo'] = $businessNetworkNo;
        }
        if(!empty($toNetworkNo)){
            $this->parameter['toNetworkNo'] = $toNetworkNo;
        }
        if(!empty($sender)){
            $this->parameter['sender'] = $sender;
        }
        if(!empty($receiver)){
            $this->parameter['receiver'] = $receiver;
        }
        if(!empty($special)){
            $this->parameter['special'] = $special;
        }
        if(!empty($totalNumber)){
            $this->parameter['totalNumber'] = $totalNumber;
        }
        if(!empty($totalWeight)){
            $this->parameter['totalWeight'] = $totalWeight;
        }
        if(!empty($totalVolume)){
            $this->parameter['totalVolume'] = $totalVolume;
        }
        if(!empty($codType)){
            $this->parameter['codType'] = $codType;
        }
        if(!empty($codValue)){
            $this->parameter['codValue'] = $codValue;
        }
        if(!empty($sendStartTime)){
            $this->parameter['sendStartTime'] = $sendStartTime;
        }
        if(!empty($sendEndTime)){
            $this->parameter['sendEndTime'] = $sendEndTime;
        }
        if(!empty($deliveryType)){
            $this->parameter['deliveryType'] = $deliveryType;
        }
        if(!empty($backSignBill)){
            $this->parameter['backSignBill'] = $backSignBill;
        }
        if(!empty($packageService)){
            $this->parameter['packageService'] = $packageService;
        }
        if(!empty($smsNotify)){
            $this->parameter['smsNotify'] = $smsNotify;
        }
        if(!empty($remark)){
            $this->parameter['remark'] = $remark;
        }
        if(!empty($insuranceValue)){
            $this->parameter['insuranceValue'] = $insuranceValue;
        }
        if(!empty($mailNo)){
            $this->parameter['mailNo'] = $mailNo;
        }
        if(!empty($orderType)){
            $this->parameter['orderType'] = $orderType;
        }

        $this->method = '/updateOrder.action';
        return $this->request();
    }

    /**
     * 订单查询
     *
     * @param string $logisticCompanyID 物流公司ID 默认值 "DEPPON"
     * @param string $logisticID 渠道单号 若订单是快递电子面单，且下单时客户编码在我司有子母件权限，渠道单号需要拼接运单号查询，例如：渠道单号_运单号，否则传下单时的渠道单号即可
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * 返回成功例子
     * {"result":"true","reason":"","resultCode":"1000","responseParam":{"cargoName":"酒鬼酒","codPrice":0,"codType":"0","codValue":0,"insurancePrice":0,"insuranceValue":0,"logisticCompanyID":"DEPPON","logisticID":"UAMZ33343111213345","mailNo":"6294143306","receiver":{"address":"徐泾镇学山文化创意谷A1-801","city":"广州市","country":"白云区","mobile":"13687646578","name":"李泽呢","originalAddress":"","postCode":"","province":"广州","street":""},"sender":{"address":"徐泾镇学山文化创意谷A1-801","city":"广州市","country":"白云区","mobile":"13687646578","name":"李泽呢","originalAddress":"","postCode":"","province":"广州","street":""},"smsNotify":"YES","statusType":"GOT","totalNumber":2,"totalVolume":0.01,"totalWeight":1,"transportType":"PACKAGE","vistReceive":"YES","waitNotifySend":"NO"},"uniquerRequestNumber":"38546580996061888"}
     */
    public function queryOrder(string $logisticCompanyID,string $logisticID):array {
        //必须参数
        $this->parameter = [
            'logisticCompanyID'=>$logisticCompanyID,
            'logisticID'=>$logisticID
        ];
        $this->method = '/queryOrder.action';
        return $this->request();
    }

    /**
     * 撤消订单
     *
     * @param string $logisticCompanyID 物流公司ID 固定值 "DEPPON"
     * @param string $logisticID 渠道单号 第三方接入平台调用我司下单接口所传的渠道单号，对于有子母件权限的快递大客户电子面单该字段传值还需要拼接运单号，格式如 渠道单号_运单号 注：渠道单号和运单号不可同时为空
     * @param string $mailNo 运单号 下单时或下单后我司系统生成的运单号 注：渠道单号和运单号不可同时为空
     * @param string $cancelTime 撤销时间 格式为 yyyy-MM-dd HH:mm:ss 如 2012-11-27 18:44:19
     * @param string $remark 撤销备注 用户撤销订单原因
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * 返回成功例子
     * {"logisticID":"UAMZ33343111213345","result":"true","reason":"成功(沙箱环境仅供参考)","resultCode":"1000","uniquerRequestNumber":"38546615797393118"}
     */
    public function cancelOrder(string $logisticCompanyID
        ,string $logisticID
        ,string $mailNo
        ,string $cancelTime
        ,string $remark):array {
        //必须参数
        $this->parameter = [
            'logisticCompanyID'=>$logisticCompanyID,
            'logisticID'=>$logisticID,
            'mailNo'=>$mailNo,
            'cancelTime'=>$cancelTime,
            'remark'=>$remark
        ];
        $this->method = '/cancelOrder.action';
        return $this->request();
    }

    /**
     * 查询网点
     *
     * @param string $logisticCompanyID 物流公司编码 DEPPON
     * @param string $province
     * @param string $city
     * @param string $county
     * @param int $matchType 0 出发网点；1 自提网点；2 派送网点；3 自提+派送
     * @param string $address 详细地址 详细地址和经纬度两者只用填写一个
     * @param string $longitude 经度 详细地址和经纬度两者只用填写一个
     * @param string $latitude 纬度 详细地址和经纬度两者只用填写一个
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * 返回成功例子
     * {"result":"true","reason":"以下数据为测试数据,仅供接口联调","resultCode":"1000","responseParam":{"deptInfoList":[{"deptAddress":"【营业时间8：50-17:30，本人提货凭身份证原件取货，代提 凭提货人身份证原件与货单号】地点:广州市2020-07-13 17:08:01***街道**号,可直接地图导航我部。","deptCode":"W01011017","deptName":"广州市2020-07-13 17:08:01**营业部","deptTel":"发货咨询027-59426072\/异常处理027-59426073\/其他027-59426074","distance":"1.8005564","isDelivery":"true","isInside":"","isPickup":"true","isReceive":"true"}],"logisticCompanyID":"DEPPON"},"uniquerRequestNumber":"38545490378742285"}
     */
    public function queryDeptByAddress(string $logisticCompanyID
        ,string $province
        ,string $city
        ,string $county
        ,int $matchType
        ,string $address = ''
        ,string $longitude = ''
        ,string $latitude = ''):array {

        //必须参数
        $this->parameter = [
            'logisticCompanyID'=>$logisticCompanyID,
            'province'=>$province,
            'city'=>$city,
            'county'=>$county,
            'matchType'=>$matchType
        ];
        //不必须参数
        if(!empty($address)){
            $this->parameter['address'] = $address;
        }
        if(!empty($longitude)){
            $this->parameter['longitude'] = $longitude;
        }
        if(!empty($latitude)){
            $this->parameter['latitude'] = $latitude;
        }
        $this->method = '/queryDeptByAddress.action';
        return $this->request();
    }

    /**
     * 标准轨迹订阅
     *
     * @param string $tracking_number 运单号
     * @param string $order_number 订单号 若订单是快递电子面单，且下单时客户编码在我司有子母件权限，渠道单号需要拼接运单号查询，例如：渠道单号_运单号，否则传下单时的渠道单号即可
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * 返回成功例子
     * {"result":true,"error_message":"订阅成功","error_code":"1000","success":true,"uniquerRequestNumber":"38545635256242392"}
     */
    public function standTraceSubscribe(string $trackingNumber,string $orderNumber = ''):array {
        //必须参数
        $this->parameter = [
            'tracking_number'=>$trackingNumber
        ];
        //不必须参数
        if(!empty($orderNumber)){
            $this->parameter['order_number'] = $orderNumber;
        }
        $this->method = '/standTraceSubscribe.action';
        return $this->request();
    }

    /**
     * 外发订单路由推送至德邦
     *
     * @param string $waybillNumber 运单号
     * @param array $statusDetail
     *              operateTime 操作时间 yyyy-MM-dd HH:mm:ss
     *              locate 城市 yyyy-MM-dd HH:mm:ss
     *              operateType 操作类型 28: 跨境运输中 20：派送拉回 14：签收
     *              note 轨迹类型描述 纯中文文本
     *              signUserName 签收人 签收人姓名
     * @return array
     * @throws ExpressDeliveryException
     *
     * 返回成功例子
     * {"result":"true","reason":"成功","waybillNumber":"6293222153","resultCode":"1000","uniquerRequestNumber":"38546820123858579"}
     */
    public function overseasTraceAdd(string $waybillNumber,array $statusDetail):array {
        //必须参数
        $this->parameter = [
            'waybillNumber'=>$waybillNumber,
            'statusDetail'=>$statusDetail
        ];
        $this->method = '/overseasTraceAdd.action';
        return $this->request();
    }

    /**
     * 价格时效查询
     *
     * @param string $logisticCompanyID  物流公司编码 DEPPON
     * @param string $originalProvince 发货地 省
     * @param string $originalCity  市
     * @param string $originalDistrict 街道
     * @param string $destProvince 目的地 省
     * @param string $destCity 市
     * @param string $destDistrict 街道
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * 返回成功例子{"result":"true","reason":"以下数据为测试数据,仅供接口联调","resultCode":"1000","responseParam":{"logisticCompanyID":"DEPPON","priceInfo":[{"heavyRate":2.24,"lightRate":227,"lowestPrice":34,"productCode":"QC_JZKH","productName":"精准卡航","promiseArriveTime":"第2天13:00前"},{"heavyRate":9.08,"lightRate":447,"lowestPrice":12,"productCode":"QC_JZQYC","productName":"精准汽运(长途)","promiseArriveTime":"第3天到第4天"},{"groundPrice":12,"lowerGround":0,"lowerOfStage1":1,"lowerOfStage2":5,"productCode":"PACKAGE","productName":"标准快递","promiseArriveTime":"第2天的18:00前派送","rateOfStage1":5,"rateOfStage2":5,"upperGround":1,"upperOfStage1":5,"upperOfStage2":10000},{"groundPrice":21,"lowerGround":0,"lowerOfStage1":3,"lowerOfStage2":5,"productCode":"RCP","productName":"3.60特惠件","promiseArriveTime":"第2天的18:00前派送","rateOfStage1":4,"rateOfStage2":4,"upperGround":3,"upperOfStage1":5,"upperOfStage2":10000},{"groundPrice":11,"lowerGround":0,"lowerOfStage1":1,"lowerOfStage2":5,"productCode":"EPEP","productName":"电商尊享","promiseArriveTime":"第2天的18:00前派送","rateOfStage1":4,"rateOfStage2":4,"upperGround":1,"upperOfStage1":5,"upperOfStage2":10000},{"groundPrice":17,"lowerGround":0,"lowerOfStage1":1,"lowerOfStage2":5,"productCode":"DEAP","productName":"特准快件","promiseArriveTime":"第2天的18:00前派送","rateOfStage1":7,"rateOfStage2":7,"upperGround":1,"upperOfStage1":5,"upperOfStage2":10000}]},"uniquerRequestNumber":"38546842494249046"}
     */
    public function queryPrice(string $logisticCompanyID
        ,string $originalProvince
        ,string $originalCity
        ,string $originalDistrict
        ,string $destProvince
        ,string $destCity
        ,string $destDistrict):array {
        //必须参数
        $this->parameter = [
            'logisticCompanyID'=>$logisticCompanyID,
            'originalProvince'=>$originalProvince,
            'originalCity'=>$originalCity,
            'originalDistrict'=>$originalDistrict,
            'destProvince'=>$destProvince,
            'destCity'=>$destCity,
            'destDistrict'=>$destDistrict
        ];
        $this->method = '/queryPrice.action';
        return $this->request();
    }

    /**
     * 新价格时效查询（估算价格接口）
     *
     * @param string $logisticCompanyID 物流公司ID 固定值"DEPPON"
     * @param string $originalsStreet 目标城市 省-市-区，如 “上海-上海市-长宁区”
     * @param string $originalsAddress 出发城市 省-市-区，如 “上海-上海市-长宁区”
     * @param string $sendDateTime 发出时间 yyyy-MM-dd HH:mm:ss
     * @param string $totalVolume 体积 无体积则填写0.001 /立方米
     * @param string $totalWeight 重量 /公斤
     * @return array
     * @throws ExpressDeliveryException
     *
     * 返回成功例子
     * {"result":"true","reason":"以下数据为测试数据,仅供接口联调","resultCode":"1000","responseParam":[{"arriveDate":"2020-07-14 12:12:00","days":"预计14:12:00前到达","groundPrice":12,"lowerOfStage1":3,"lowerOfStage2":30,"omsProductCode":"RCP","productName":"大件快递3.60","producteCode":"RCP","rateOfStage1":1,"totalfee":12,"upperGround":3},{"arriveDate":"2020-07-14 12:10:30","days":"预计14:10:30前到达","groundPrice":10,"label":"时间最快","lowerOfStage1":1,"lowerOfStage2":30,"omsProductCode":"DEAP","productName":"特准快件","producteCode":"DEAP","rateOfStage1":2,"totalfee":14,"upperGround":1},{"arriveDate":"2020-07-14 12:12:00","days":"预计14:12:00前到达","groundPrice":51,"lowerOfStage1":31,"lowerOfStage2":60,"omsProductCode":"ZBRH","productName":"大件快递3.60","producteCode":"ZBRH","rateOfStage1":2,"totalfee":51,"upperGround":31},{"arriveDate":"2020-07-15 12:10:00","days":"预计15:10:00前到达","heaveRate":1.01,"lightRate":212,"omsProductCode":"JZCY","productName":"精准卡航","producteCode":"FSF","totalfee":78},{"arriveDate":"2020-07-15 12:08:00","days":"预计15:8:00前到达","heaveRate":0.8,"label":"时间最快","lightRate":168,"omsProductCode":"JZQY_SHORT","productName":"精准汽运","producteCode":"SRF","totalfee":65}],"uniquerRequestNumber":"38543197150053191"}
     */
    public function queryPriceTime(
        string $logisticCompanyID,
        string $originalsStreet,
        string $originalsAddress,
        string $sendDateTime,
        string $totalVolume,
        string $totalWeight
    ):array {
        //必须参数
        $this->parameter = [
            'logisticCompanyID'=>$logisticCompanyID,
            'originalsStreet'=>$originalsStreet,
            'originalsAddress'=>$originalsAddress,
            'sendDateTime'=>$sendDateTime,
            'totalVolume'=>$totalVolume,
            'totalWeight'=>$totalWeight
        ];
        $this->method = '/queryPriceTime.action';
        return $this->request();
    }

    /**
     * 海外轨迹对接
     *
     * @param string $waybillNo 运单号
     * @param string $operateType 轨迹类型 ZH_GOT xxx已收件（xxx是地名）; ZH_DEPARTURE 已分配航班，发送清关口岸 ;ZH_ARRIVAL 到达清关口岸，开始清关
     * @param string $operateTime 轨迹发生时间
     * @param string $details 轨迹描述
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * 返回成功例子
     * {"result":"true","waybillNo":"6293222153","reason":"成功","resultCode":"1000","uniquerRequestNumber":"38526812034464435"}
     */
    public function deliverAbroadTrace(string $waybillNo,string $operateType,string $operateTime,string $details):array {
        //必须参数
        $this->parameter = [
            'waybillNo'=>$waybillNo,
            'operateType'=>$operateType,
            'operateTime'=>$operateTime,
            'details'=>$details
        ];
        $this->method = '/deliverAbroadTrace.action';
        return $this->request();
    }

    /**
     * 新标准轨迹查询
     *
     * @param string $mailNo
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     *
     * 返回成功例子
     * {"result":"true","reason":"","resultCode":"1000","responseParam":{"trace_list":[{"city":"广州","description":"test","status":"20","time":"2020-07-13 11:12:06"},{"city":"**市(测试)","description":"正常签收,签收人类型:本人\/同事\/门卫 等","site":"【***】营业部","status":"SIGNED","time":"2020-07-13 22:58:37"},{"city":"**市(测试)","description":"此货已滞留，与客户预约改日派送","site":"【***】营业部","status":"ERROR","time":"2020-07-14 01:58:37"},{"city":"**市(测试)","description":"拒绝签收:原因","site":"【***】营业部","status":"FAILED","time":"2020-07-13 22:58:37"},{"city":"**市(测试)","description":"派送中,派送人:**,派送人电话:***********","site":"【***】营业部","status":"SENT_SCAN","time":"2020-07-13 19:58:37"},{"city":"**市(测试)","description":"货物已到达【***营业部】部门","site":"【***】营业部","status":"ARRIVAL","time":"2020-07-13 16:58:37"},{"city":"**市(测试)","description":"运输中,离开【广州市转运中心】,下一站【***营业部】(出发到达对应多个)","site":"****转运中心\/营业部\/枢纽中心","status":"DEPARTURE","time":"2020-07-13 13:58:37"},{"city":"**市(测试)","description":"您的订单已被收件员揽收,【广州市白云区***营业部】库存中","site":"广州市转运中心\/营业部\/枢纽中心","status":"GOT","time":"2020-07-13 11:58:37"}],"tracking_number":"6293222153"},"uniquerRequestNumber":"38525813676188993"}
     */
    public function newTraceQuery(string $mailNo):array {
        //必须参数
        $this->parameter = [
            'mailNo'=>$mailNo
        ];
        $this->method = '/newTraceQuery.action';
        return $this->request();
    }

    /**
     * 快递电子面单同步筛单(B模式)
     *
     * @param string $logisticCompanyID
     * @param string $logisticID
     * @param string $orderSource
     * @param string $serviceType
     * @param string $customerCode
     * @param string $gmtCommit
     * @param string $cargoName
     * @param string $payType
     * @param string $transportType
     * @param string $deliveryType
     * @param array $sender
     * @param array $receiver
     * @param string $mailNo
     * @param string $customerID
     * @param string $toNetworkNo
     * @param string $businessNetworkNo
     * @param string $special
     * @param string $totalNumber
     * @param string $totalWeight
     * @param string $totalVolume
     * @param string $insuranceValue
     * @param string $vistReceive
     * @param string $sendStartTime
     * @param string $sendEndTime
     * @param string $codType
     * @param string $codValue
     * @param string $reciveLoanAccount
     * @param string $accountName
     * @param string $backSignBill
     * @param string $packageService
     * @param string $smsNotify
     * @param string $remark
     * @param string $isPickupSelf
     * @param string $isOut
     * @return array
     * @throws ExpressDeliveryException
     *
     * 返回成功例子
     * {"logisticID":"UAMZ333431112333461","result":"true","reason":"成功","resultCode":"1000","uniquerRequestNumber":"38546223088619524"}
     */
    public function expressSyncSieveOrder(
        string $logisticCompanyID,
        string $logisticID,
        string $orderSource,
        string $serviceType,
        string $customerCode,
        string $gmtCommit,
        string $cargoName,
        string $payType,
        string $transportType,
        string $deliveryType,
        array $sender,
        array $receiver,
        string $mailNo = '',
        string $customerID = '',
        string $toNetworkNo = '',
        string $businessNetworkNo = '',
        string $special = '',
        string $totalNumber = '',
        string $totalWeight = '',
        string $totalVolume = '',
        string $insuranceValue = '',
        string $vistReceive = '',
        string $sendStartTime = '',
        string $sendEndTime = '',
        string $codType = '',
        string $codValue = '',
        string $reciveLoanAccount = '',
        string $accountName = '',
        string $backSignBill = '',
        string $packageService = '',
        string $smsNotify = '',
        string $remark = '',
        string $isPickupSelf = '',
        string $isOut = ''
    ):array {
        //必须参数
        $this->parameter = [
            'logisticCompanyID'=>$logisticCompanyID,
            'logisticID'=>$logisticID,
            'orderSource'=>$orderSource,
            'serviceType'=>$serviceType,
            'customerCode'=>$customerCode,
            'gmtCommit'=>$gmtCommit,
            'cargoName'=>$cargoName,
            'payType'=>$payType,
            'transportType'=>$transportType,
            'deliveryType'=>$deliveryType,
            'sender'=>$sender,
            'receiver'=>$receiver
        ];

        //不必须参数
        if(!empty($mailNo)){
            $this->parameter['mailNo'] = $mailNo;
        }
        if(!empty($customerID)){
            $this->parameter['customerID'] = $customerID;
        }
        if(!empty($toNetworkNo)){
            $this->parameter['toNetworkNo'] = $toNetworkNo;
        }
        if(!empty($businessNetworkNo)){
            $this->parameter['businessNetworkNo'] = $businessNetworkNo;
        }
        if(!empty($sender)){
            $this->parameter['sender'] = $sender;
        }
        if(!empty($receiver)){
            $this->parameter['receiver'] = $receiver;
        }
        if(!empty($special)){
            $this->parameter['special'] = $special;
        }
        if(!empty($totalNumber)){
            $this->parameter['totalNumber'] = $totalNumber;
        }
        if(!empty($totalWeight)){
            $this->parameter['totalWeight'] = $totalWeight;
        }
        if(!empty($totalVolume)){
            $this->parameter['totalVolume'] = $totalVolume;
        }
        if(!empty($insuranceValue)){
            $this->parameter['insuranceValue'] = $insuranceValue;
        }
        if(!empty($vistReceive)){
            $this->parameter['vistReceive'] = $vistReceive;
        }
        if(!empty($sendStartTime)){
            $this->parameter['sendStartTime'] = $sendStartTime;
        }
        if(!empty($sendEndTime)){
            $this->parameter['sendEndTime'] = $sendEndTime;
        }
        if(!empty($codType)){
            $this->parameter['codType'] = $codType;
        }
        if(!empty($codValue)){
            $this->parameter['codValue'] = $codValue;
        }
        if(!empty($reciveLoanAccount)){
            $this->parameter['reciveLoanAccount'] = $reciveLoanAccount;
        }
        if(!empty($accountName)){
            $this->parameter['accountName'] = $accountName;
        }
        if(!empty($backSignBill)){
            $this->parameter['backSignBill'] = $backSignBill;
        }
        if(!empty($packageService)){
            $this->parameter['packageService'] = $packageService;
        }
        if(!empty($smsNotify)){
            $this->parameter['smsNotify'] = $smsNotify;
        }
        if(!empty($remark)){
            $this->parameter['remark'] = $remark;
        }
        if(!empty($isPickupSelf)){
            $this->parameter['isPickupSelf'] = $isPickupSelf;
        }
        if(!empty($isOut)){
            $this->parameter['isOut'] = $isOut;
        }

        $this->method = '/expressSyncSieveOrder.action';
        return $this->request();
    }



    /**
     * 方法请求器
     *
     * @return array
     * @throws ExpressDeliveryException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(): array
    {
        try {
            date_default_timezone_set("PRC");
            $timestamp   = $this->getMillisecond();
            $jsonParams  = json_encode($this->parameter,JSON_UNESCAPED_UNICODE);
            $digest      = $this->getSign($jsonParams,$timestamp);
            $client = new Client(['verify' => false]);


            $postData = [
                'form_params'=>[
                        'params' => $jsonParams,
                        'digest' => $digest,
                        'timestamp' => $timestamp,
                        'companyCode' => $this->companyCode
                    ]
            ];
            $response = $client->request('post', trim($this->apiUrl . $this->method),$postData); //使用json请求
            $response = json_decode($response->getBody()->getContents(), true);
            $message = '操作成功';
            $code    = isset($response['resultCode']) ? $response['resultCode'] : '';
            return $this->apiResponse($code, $message, $response);
        } catch (\Exception $e) {
            throw new ExpressDeliveryException($e->getMessage());
        }
    }

    /**
     * 获取时间戳，毫秒级
     *
     * @return int
     */
    public function getMillisecond() {
        list($msec, $sec) = explode(' ', microtime());
        return intval(((float)$msec + (float)$sec) * 1000);
    }

    /**
     * 数据返回
     *
     * @param $code
     * @param $message
     * @param array $data
     * @return array
     */
    private function apiResponse($code, $message, $data = [])
    {
        $result = [
            'responseCode' => 20000,
            'responseMessage' => 'success',
            'responseData' => [
                'code' => $code,
                'message' => $message,
                'data' => $data,
                'channel' => $this->channel,
            ]
        ];
        return $result;
    }

}
