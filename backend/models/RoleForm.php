<?php
namespace backend\models;

use yii\base\Model;

class RoleForm extends Model{
    public $name;
    public $description;
    public $permission;
    const SCENARIO_NAME_ADD_ROLE = 'name_add';
    const SCENARIO_NAME_EDIT_ROLE = 'name_edit';
    //在定义了场景后一定要有对应的方法

    public function rules()
    {
        return [
            [['name','description','permission'],'required'],
            ['name','weiyiadd','on'=>self::SCENARIO_NAME_ADD_ROLE],
            ['name','weiyiedit','on'=>self::SCENARIO_NAME_EDIT_ROLE]
        ];
    }
    public function attributeLabels()
    {
        return [
            'name'=>'角色名',
            'description'=>'描述',
            'permission'=>'权限'
        ];
    }
    public function weiyiadd(){
        $auth=\Yii::$app->authManager;
        //和数据表里面的名字进行验证
        if($auth->getRole($this->name)){
            $this->addError('name','已经存在该角色');
        }
    }
    public function weiyiedit(){
        $auth=\Yii::$app->authManager;
        $name=\Yii::$app->request->get('name');
        if($name != $this->name){
            if($auth->getRole($this->name)){
                $this->addError('name','已经存在该角色');
            }
        }
    }
}
