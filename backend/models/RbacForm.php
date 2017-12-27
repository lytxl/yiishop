<?php
namespace backend\models;

use yii\base\Model;

class RbacForm extends Model{
    public $name;
    public $describe;

    const SCENARIO_ADD_PERMISSION ='add_permission';
    const SCENARIO_EDIT_PERMISSION ='edit_permission';
    public function rules()
    {
        return [
            [['name','describe'],'required'],
            ['name','weiyi','on'=>self::SCENARIO_ADD_PERMISSION],
            ['name','weiyiedit','on'=>self::SCENARIO_EDIT_PERMISSION],
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'路由',
            'describe'=>'描述'
        ];
    }
    //添加权限名唯一
    public function weiyi(){
        $auth=\Yii::$app->authManager;
        //验证权限是否存在
        if($auth->getPermission($this->name)){
            $this->addError("name",'已经存在该权限');
        }
    }
    //修改权限名唯一
    public function weiyiedit(){
        $auth=\Yii::$app->authManager;
        $p=\Yii::$app->request->get('name');
        if($p !=$this->name){
            if($auth->getPermission($this->name)){
                $this->addError("name",'已经存在该权限');
            }
        }

    }
}