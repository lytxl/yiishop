<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Article_category extends ActiveRecord{
   public function rules()
   {
       return [
       [['name','intro','sort','status'],'required']
   ];
   }

    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'intro'=>'简介',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }
}
