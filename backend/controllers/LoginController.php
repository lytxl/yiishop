<?php
namespace backend\controllers;

use backend\models\LoginForm;
use yii\captcha\CaptchaAction;
use yii\web\Request;

class LoginController extends \yii\web\Controller {
    //登录
    public function actionIndex(){
        $model=new LoginForm();
        $request=new Request();
        //后端验证
        if($request->isPost){
            $model->load($request->post());
            if($model->login()){
                \Yii::$app->session->setFlash('success','登录成功');
                    return $this->redirect(['user/index']);
            }
        }
        return $this->render('login',['model'=>$model]);
    }
    //验证码
    public function actions()
    {
        return [
            'captcha'=>[
                'class'=>CaptchaAction::className(),
                'height'=>50,
                'minLength'=>4,
                'maxLength'=>4,
                'padding'=>4
            ]
        ];
    }
}
