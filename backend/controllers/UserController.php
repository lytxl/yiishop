<?php
namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\User;
use yii\captcha\CaptchaAction;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Request;

class UserController extends Controller{
    //首页
    public function actionIndex(){
        $form=User::find()->all();
        return $this->render('index',['form'=>$form]);
    }
    //用户名添加
    public function actionAdd(){
        $model=new User();
        $request=new Request();
        //获取所有权限
        $auth=\Yii::$app->authManager;
        $result=$auth->getRoles();
        $roles=[];
        foreach ($result as $r){
            $roles[$r->name]=$r->description;
        }
        //后端验证
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->created_at=time();
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->auth_key=uniqid();
                $jurisdiction=$model->jurisdiction;
                $model->save(false);
                $id = $model->id;
                if($jurisdiction){
                    foreach ($jurisdiction as $ju){
                        $name=$auth->getRole($ju);
                        $auth->assign($name,$id);
                }
                }
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
                die;
            }
        }
        return $this->render('add',['model'=>$model,'roles'=>$roles]);
    }
    //修改个人
    public function actionEditOne(){
       $id=\Yii::$app->user->id;
        $model=User::find()->where(['id'=>$id])->one();
        $request=new Request();
        //后端验证
        if ($request->isPost){
                $model->load($request->post());
                if($model->verifypwd()){
                    $model->updated_at=time();
                    $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);
                    $model->auth_key=uniqid();
                    $model->save(false);
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['user/index']);
                }else{
                    $model->addError("wornpwd",'旧密码不一致');
                }
        }
        $model->password_hash='';
        return $this->render('editone',['model'=>$model]);
    }
    //高级管理员修改
    public function actionEdit($id){
        $model=User::find()->where(['id'=>$id])->one();
        //获取所有权限
        $auth=\Yii::$app->authManager;
        $result=$auth->getRoles();
        $roles=[];
        foreach ($result as $r){
            $roles[$r->name]=$r->description;
        }
        //根据id获取到角色
        $model->jurisdiction=[];
        foreach($auth->getRolesByUser($id) as $r){
                $model->jurisdiction[$r->description]=$r->name;
        };
        $request=new Request();
        //后端验证
        if ($request->isPost){
            //取消所有的角色关联
            $auth->revokeAll($id);
                $model->load($request->post());
                if($model->validate()){
                    $model->updated_at=time();
                    $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);
                    $model->auth_key=uniqid();
                    $roles=$model->jurisdiction;
                    $model->save(false);
                    if($roles){
                        foreach ($roles as $ju){
                            $name=$auth->getRole($ju);
                            $auth->assign($name,$id);
                        }
                    }
                    \Yii::$app->session->setFlash('success','修改成功');
                    return $this->redirect(['user/index']);
                }else{
                    $model->addError("wornpwd",'旧密码不一致');
                }
        }
        $model->password_hash='';
        return $this->render('add',['model'=>$model,'roles'=>$roles]);
    }
    //删除
    public function actionDelete($id){
        $dele=User::findOne(['id'=>$id]);
        $auth=\Yii::$app->authManager;
        $auth->revokeAll($id);
        $dele->delete();
        echo json_encode($dele);
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
