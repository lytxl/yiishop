<?php
namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\Menu;
use yii\web\Controller;
use yii\web\Request;

class MenuController extends Controller{
    //首页
    public function actionIndex(){
        $form=Menu::find()->orderBy('id')->all();
        return $this->render('index',['form'=>$form]);
    }
    //菜单添加
    public function actionAdd(){
        $model=new Menu();
        $request=new Request();
        //获取路由
        $auth=\Yii::$app->authManager;
        $result=$auth->getPermissions();
        $permission=[];
        foreach($result as $r){
            $permission[' ']='=请选择路由=';
            $permission[$r->name]=$r->description;
        }
        //获取菜单
        $menu=Menu::find()->where(['=','f_id','0'])->asArray()->all();//用数组的方式查出来下面要用数组的方式取值
        array_unshift($menu,['id'=>0,'name'=>'顶级分类','f_id'=>0]);
        $val=[];
        foreach($menu as $m){
            $val[$m['id']]=$m['name'];
        }
            if($request->isPost){
                $model->load($request->post());
                if($model->validate()){
                    $model->save();
                    \Yii::$app->session->setFlash('success','添加成功');
                    return $this->redirect(['menu/index']);
                }
            }
        return $this->render('add',['model'=>$model,'permission'=>$permission,'val'=>$val]);
    }
    //菜单修改
    public function actionEdit($id){
        $model=Menu::find()->where(['id'=>$id])->one();
        $request=new Request();
        //获取路由
        $auth=\Yii::$app->authManager;
        $result=$auth->getPermissions();
        $permission=[];
        foreach($result as $r){
            $permission[$r->name]=$r->description;
        }
        //获取菜单
        $menu=Menu::find()->where(['=','f_id','0'])->asArray()->all();//用数组的方式查出来下面要用数组的方式取值
        array_unshift($menu,['id'=>0,'name'=>'顶级分类','f_id'=>0]);
        $val=[];
        foreach($menu as $m){
            $val[$m['id']]=$m['name'];
        }
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['menu/index']);
            }
        }
        return $this->render('add',['model'=>$model,'permission'=>$permission,'val'=>$val]);
    }
    //删除菜单
    public function actionDelete($id){
        $menu=Menu::findOne(['id'=>$id]);
        $menu->delete();
        echo json_encode($menu);
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
