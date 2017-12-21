<?php
namespace backend\controllers;

use yii\web\Controller;
use backend\models\article_category;
use yii\web\Request;
//分类
class Article_categoryController extends Controller{
    //首页
    public function actionIndex(){
            //获取数据
          $form =  Article_category::find()->where(['>=','status',0])->all();
            return $this->render('index',['form'=>$form]);
        }
        //添加
    public function actionAdd(){
            $model=new Article_category();
        $request=new Request();

        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                //保存
                $model->save(false);
                //提示
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转
                return $this->redirect(['article_category/index']);
            }
        }
        else{
            return $this->render('add',['model'=>$model]);

        }
    }
    //修改
    public function actionEdit($id){
        $model=Article_category::findOne(['id'=>$id]);
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article_category/index']);
            }
        }
        else{
            return $this->render('add',['model'=>$model]);
        }
    }
    //删除
    public function actionDelete($id){
        $model=Article_category::findOne(['id'=>$id]);
        $model->status=-1;
        $data=$model->save(false);
        echo json_encode($data);
    }
}
