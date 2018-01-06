<?php
namespace backend\filters;

use yii\base\ActionFilter;
use yii\web\HttpException;

class RbacFilters extends ActionFilter{
    //操作之前
    public function beforeAction($action)
    {
         if(!\Yii::$app->user->can($action->uniqueId)){
             //如果没有登录就让用户去登录
             if(\Yii::$app->user->isGuest){
                 return $action->controller->redirect(\Yii::$app->user->loginUrl);
             }
            //没有权限
             throw new HttpException(403,'对不起,你没有该权限!');
         }
         return true;
    }
}
