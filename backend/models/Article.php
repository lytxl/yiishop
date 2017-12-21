<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Article extends ActiveRecord{
    public function     rules()
    {
        return [
            [['name','intro','sort','status','create_date','article_category_id'],'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name'=>'文章名',
            'intro'=>'简介',
            'article_category_id'=>'分类',
            'create_date'=>'创建时间',
            'sort'=>'排序',
            'status'=>'状态',
        ];
    }
}