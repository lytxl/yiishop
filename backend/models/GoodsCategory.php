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
            [['name','intro'], 'required'],
            ['parent_id','validateId'],
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

    public function validateId(){
        $parent = GoodsCategory::findOne(['id' => $this->parent_id]);
        //判断查询的结果是不是对象,如果不是对象就null 返回一个false
        if(!is_object($parent)){
            return false;
        }else{
            if($parent->isChildOf($this)){
                $this->addError('parent_id','不能修改到自己的子孙下面');
            }
        }
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
