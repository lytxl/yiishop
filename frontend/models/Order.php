<?php
namespace  frontend\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord{
    //快递放式
    public static $deliveries=[
        1=>['顺丰快递',25,'速度非常快,服务好,价格贵'],
        2=>['圆通快递',10,'速度快,服务一般,价格便宜'],
        3=>['中通快递',10,'速度一般,服务一般,价格便宜']
    ];
    public static $deal=[
        1=>['货到付款','送货上门后再收款，支持现金、POS机刷卡、支票支付'],
        2=>['在线支付','即时到帐，支持绝大数银行借记卡及部分银行信用卡'],
        3=>['邮局汇款','通过快钱平台收款 汇款后1-3个工作日到账']
    ];
    public $address_id;
    public $money;
    public $pay;
    public function rules()
    {
        return [
            [[
                'name','province','city','area','address','tel','delivery','delivery_name','delivery_price','total'
            ],'required'],
            [['address_id','money','pay'],'default','value'=>null]
        ];
    }
}
