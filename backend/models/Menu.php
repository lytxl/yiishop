<?php
namespace backend\models;

use yii\db\ActiveRecord;

class Menu extends ActiveRecord{
    public function rules()
    {
        return [
            [['name','f_id','sort'],'required'],
            ['route','default','value'=>null]
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'菜单名称',
            'f_id'=>'上级菜单',
            'route'=>'路由',
            'sort'=>'排序'
        ];
    }

}