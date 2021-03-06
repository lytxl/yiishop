<?php
namespace backend\models;

use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface {
    public $password;
    public $wornpwd;
    public $jurisdiction;
    const SCENARIO_EDIT_USER ='edit.user';
    public function rules()
    {
        return [
            [['username','email','password_hash','password','status'],'required'],
            ['email','email'],
            ['password', 'compare', 'compareAttribute'=>'password_hash'],
            [['wornpwd','jurisdiction'],'default','value'=>null]
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'email'=>'邮箱',
            'password_hash'=>'密码',
            'wornpwd'=>'旧密码',
            'password'=>'确认密码',
            'status'=>'状态',
            'jurisdiction'=>'角色'
        ];
    }
    //验证旧密码
    public function verifypwd(){
        $admin=User::findOne(['username'=>$this->username]);
        if($admin){
            if(\Yii::$app->security->validatePassword($this->wornpwd,$admin->password_hash)){
                return true;
            }else{
                return false;
            }
        };
        return false;
    }
 public function getMenus(){
     $menuItems=[];
     $menus=\backend\models\Menu::find()->where(['f_id'=>0])->all();
     //遍历第一级菜单
     foreach($menus as $menu){
             $m=[];
             //第二级菜单
         $r=Menu::find()->where(['f_id'=>$menu->id])->all();
             foreach($r as $me){
                 if(\Yii::$app->user->can($me->route)){
                     $m[]=['label'=>$me->name,'url'=>[$me->route]];
                 }
             }
             if($m){
                 $menuItems[]=['label'=>$menu->name,'items'=>$m];
             }
     }
     return $menuItems;
 }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey()===$authKey;
    }
}