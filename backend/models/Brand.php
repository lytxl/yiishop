<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Brand extends ActiveRecord {
    public $img;
        public function     rules()
        {
            return [
              [['name','intro','img','sort','status'],'required']
            ];
        }

    public function attributeLabels()
        {
            return [
              'name'=>'商品名',
              'intro'=>'简介',
              'img'=>'商品LOGO',
              'sort'=>'排序',
              'status'=>'状态',
            ];
        }
}
