<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Cart extends ActiveRecord{
    public function rules()
    {
        return [
            [['goods_id','amount','member_id'],'required']
        ];
    }
}