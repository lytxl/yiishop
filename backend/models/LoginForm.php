<?php
namespace backend\models;

use yii\base\Model;


class LoginForm extends Model{
    public $password_hash;
    public $username;
    //验证码
    public $code;
    public function rules()
    {
        return [
            [['username','password_hash'],'required'],
            ['code','captcha','captchaAction'=>'login/captcha']
        ];
    }
    public function attributeLabels()
    {
        return [
          'username'=>'用户名',
            'password_hash'=>'密码'
        ];
    }

    public function login(){
        //验证用户是否正确
        $admin=User::find()->where(['username'=>$this->username])->one();
        if($admin){
            //验证密码
            if(\Yii::$app->security->validatePassword($this->password_hash,$admin->password_hash)){
                //保存登录时间和IP
                $admin->last_login_time=time();
                $admin->last_login_ip=$_SERVER['REMOTE_ADDR'];
                $admin->save(false);
                //将用户信息保存到session里面
                \Yii::$app->user->login($admin,7*24*3600);
                return true;
            }
            else{
                //密码错误
                $this->addError('password_hash','密码出错');
            }
        }else{
            //用户名错误
            $this->addError('username','用户名出错');
        }
    }


}