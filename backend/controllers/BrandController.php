<?php
namespace backend\controllers;

use backend\models\Brand;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends Controller{
    //首页
    public function actionIndex(){
        //获取数据
       $form=Brand::find()->where(['>=','status',0])->all();
        return $this->render('index',['form'=>$form]);
    }
    //添加
    public function actionAdd(){
        $model=new Brand();
        $request=new Request();

        if($request->isPost){
            $model->load($request->post());
        //处理图片
            $model->img=UploadedFile::getInstance($model,'img');
            if($model->validate()){
                //处理图片路径
                $file='/upload/'.uniqid().'.'.$model->img->extension;
                //如果图片上传成功就保存
                if($model->img->saveAs(\Yii::getAlias('@webroot'.$file))){
                    $model->logo=$file;
                }
                //保存
                $model->save(false);
                //提示
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转
                return $this->redirect(['brand/index']);
            }
        }
        else{
            return $this->render('add',['model'=>$model]);

        }
    }
    //修改
    public function actionEdit($id){
        $model=Brand::findOne(['id'=>$id]);
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            //处理图片
            $model->img=UploadedFile::getInstance($model,'img');
            if($model->validate()){
                //处理图片名
                $file='/upload/brand/'.uniqid().'.'.$model->img->extension;
                //判断图片是否保存到本地成功
                if($model->img->saveAs(\Yii::getAlias('@webroot').$file)){
                   $model->logo=$file;
                }
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['brand/index']);
            }
        }
        else{
            return $this->render('add',['model'=>$model]);
        }
    }
    //删出
    public function actionDelete($id){
        $model=Brand::findOne(['id'=>$id]);
        $model->status=-1;
       $data= $model->save(false);
        echo json_encode($data);
    }}
