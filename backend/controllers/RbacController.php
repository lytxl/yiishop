<?php
namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\RbacForm;
use backend\models\RoleForm;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\Controller;
use yii\web\Request;

class RbacController extends Controller{
    public function actionPermissionIndex(){
            $auth=\Yii::$app->authManager;
            $form=$auth->getPermissions();
            return $this->render('index',['form'=>$form]);
        }
    //权限的添加
    public function actionPermissionAdd(){
            $model=new RbacForm();
            $model->scenario=RbacForm::SCENARIO_ADD_PERMISSION;
            $request=new Request();
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $auth=\Yii::$app->authManager;
                    $permission=new Permission();
                    $permission->name=$model->name;
                    $permission->description=$model->describe;
                    $auth->add($permission);
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['rbac/permission-index']);
                }
            }
            return $this->render('add',['model'=>$model]);
        }
        //修改权限
    public function actionPermissionEdit($name){
         $auth=\Yii::$app->authManager;
       $permission= $auth->getPermission($name);
       $model=new RbacForm();//new一个实例化的对象
        $model->scenario=RbacForm::SCENARIO_EDIT_PERMISSION;
       //把数据放到模型里面
        $model->name=$permission->name;
       $model->describe=$permission->description;
       $requesrt=new Request();
       if($requesrt->isPost){
           $model->load($requesrt->post());
           //后台验证
           if($model->validate()){
                $perm=new Permission();
                $perm->name=$model->name;
                $perm->description=$model->describe;
                $auth->update($name,$perm);
               \Yii::$app->session->setFlash('success','修改成功');
               return $this->redirect(['rbac/permission-index']);
           }
       }
       return $this->render('add',['model'=>$model]);
    }
        //删除
    public function actionPermissionDelete($name){
        $auth=\Yii::$app->authManager;
        //获取到对象然后才删除
        $result=$auth->getPermission($name);
      if($auth->remove($result)){
          \Yii::$app->session->setFlash('success','路由删除成功');
          return $this->redirect(['rbac/permission-index']);
      }
    }
    //角色首页
    public function actionRoleIndex(){
        $auth=\Yii::$app->authManager;
        $form=$auth->getRoles();
        return $this->render('role-index',['form'=>$form]);
    }
    //角色添加
    public function actionRoleAdd(){
        $model=new RoleForm();
        $model->scenario=RoleForm::SCENARIO_NAME_ADD_ROLE;
        $auth=\Yii::$app->authManager;
        $result=$auth->getPermissions();
        //得到所有权限
        $jurisdiction=[];
        foreach ($result as $r){
            $jurisdiction[$r->name]=$r->description;
        }
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //把角色保存到角色和权限表里面
                $permission=new Role();
                $permission->name=$model->name;
                $permission->description=$model->description;
                $auth->add($permission);
                //保存角色名以及他的权限
                foreach ($model->permission as $p){
                   $name= $auth->getPermission($p);
                    $auth->addChild($model,$name);
                }
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['rbac/role-index']);

            }
        }
        return $this->render('role-add',['model'=>$model,'jurisdiction'=>$jurisdiction]);
    }
    //角色修改
    public function actionRoleEdit($name)
    {
        //根据name获取到他的信息

        $model = new RoleForm();
        $model->scenario = RoleForm::SCENARIO_NAME_EDIT_ROLE;
        $auth=\Yii::$app->authManager;
        $role=$auth->getRole($name);
        $model->name=$role->name;
        $model->description=$role->description;
        //得到所有权限
        $result=$auth->getPermissions();
        foreach ($result as $r){
            $jurisdiction[$r->name]=$r->description;
        }
        //等到改用户的权限
        $perm=$auth->getChildren($name);
//        $perm=$auth->getPermissionsByRole($name);或者这种
        $model->permission=[];
        foreach ($perm as $p){
            //因为页面上面的多选款的的键值对方式是$p->name 多选是以索引数组的方式不需要键
            $model->permission[]=$p->name;
        }
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            //后端验证
            if($model->validate()){

                $role->name=$model->name;
                $role->description=$model->description;
                //角色修改
                $auth->update($name,$role);
                //权限修改
                //在添加新的权限的时候要吧原来的权限删除
                $auth->removeChildren($role);
                foreach($model->permission as $p){
                    $prmission=$auth->getPermission($p);
                    $auth->addChild($role,$prmission);
                }
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['rbac/role-index']);
            }
        }
        return $this->render('role-add',['model'=>$model,'jurisdiction'=>$jurisdiction]);
    }
    //角色删除
    public function actionRoleDelete($name){
        $auth=\Yii::$app->authManager;
        $na=  $auth->getRole($name);
        if($auth->remove($na)){
            $auth->removeChildren($na);
            \Yii::$app->session->setFlash('success','角色删除成功');
            return $this->redirect(['rbac/role-index']);
        }
    }
    //权限
    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilters::className()
            ]
        ];
    }
}
