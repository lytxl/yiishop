<?php
namespace backend\controllers;

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
        //后端验证
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->created_at=time();
                $model->password_hash=\Yii::$app->security->generatePasswordHash($model->password_hash);
                $model->auth_key=uniqid();
                $model->save(false);
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['user/index']);
            }else{
                var_dump($model->getErrors());
                die;
            }
        }
        return $this->render('add',['model'=>$model]);
    }
    //修改个人
    public function actionEditOne($id){
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
        return $this->render('edit',['model'=>$model]);
    }
    //删除
    public function actionDelete($id){
        $dele=User::findOne(['id'=>$id]);
        $dele->delete();
        echo json_encode($dele);
    }
}
