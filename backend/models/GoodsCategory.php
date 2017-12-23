<?php
namespace backend\models;

use yii\db\ActiveRecord;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\helpers\Json;

class GoodsCategory extends ActiveRecord
{
    public function rules()
    {
        return [
            [['name', 'parent_id', 'intro'], 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => '名称',
            'parent_id' => '上级分类id',
            'intro' => '简介'
        ];
    }

    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
                'treeAttribute' => 'tree',//打开支持多颗树
                'leftAttribute' => 'lft',
                'rightAttribute' => 'rgt',
                'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new GoodsQuery(get_called_class());
    }

    //获取节点信息
    public static function getNodes()
    {
        $nodes = self::find()->select(['id', 'parent_id', 'name'])->asArray()->all();
        array_unshift($nodes, ['id' => 0, 'parent_id' => 0, 'name' => '【顶级分类】']);
        return Json::encode($nodes);
    }



}
