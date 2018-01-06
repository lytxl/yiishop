<?php
namespace frontend\models;

use yii\base\Model;

class LoginForm extends Model{
    public $username;
    public $password_hash;
    public function rules()
    {
        return [
            [['username','password_hash'],'required'],
        ];
    }
    public function member(){
        $admin= Member::find()->where(['username'=>$this->username])->one();
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
            }else{

               return false;
            }
        }else{
            //用户名错误
            return false;
        }
    }
}
