<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord {
        public function     rules()
        {
            return [
              [['name','intro','logo','sort','status'],'required']
            ];
        }

    public function attributeLabels()
        {
            return [
              'name'=>'商品名',
              'intro'=>'简介',
              'sort'=>'排序',
              'status'=>'状态',
                'logo'=>'头像',
            ];
        }
}
