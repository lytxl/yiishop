<?php
namespace backend\controllers;

use backend\models\GoodsCategory;
use creocoder\nestedsets\NestedSetsBehavior;
use yii\web\Controller;
use yii\web\Request;

class GoodsCategoryController extends Controller
{
    //首页
    public function actionIndex()
    {
        $result = GoodsCategory::find()->all();

        return $this->render('index', ['result' => $result]);
    }

    //添加
    public function actionAdd()
    {
        $countries = new GoodsCategory();
        $request = new Request();
        if ($request->isPost) {
            $countries->load($request->post());
            if ($countries->validate()) {
                if ($countries->parent_id) {
                    $parent = GoodsCategory::findOne(['id' => $countries->parent_id]);
                    $countries->appendTo($parent);
                } else {
                    $countries->makeRoot();
                }
                \Yii::$app->session->setFlash('success', '添加成功');
                return $this->redirect(['goods-category/index']);
            }
        } else {
            return $this->render('add', ['countries' => $countries]);
        }
    }

    //修改
    public function actionEdit($id)
    {
        $countries = GoodsCategory::find()->where(['id' => $id])->one();
        $request = new Request();
        if ($request->isPost) {
            $countries->load($request->post());
            if ($countries->validate()) {
                if ($countries->parent_id) {
                    $parent = GoodsCategory::findOne(['id' => $countries->parent_id]);
                    $countries->appendTo($parent);
                } else {
                    $countries->parent_id = 0;
                    $countries->makeRoot();
                }
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['goods-category/index']);
            }
        }
        else {
            return $this->render('add', ['countries' => $countries]);
        }
    }
    //删除
    public function actionDelete($id){
        if(GoodsCategory::findOne(['parent_id'=>$id])){
            //存在子分类就不删除
            echo json_encode(false);
        }else{
            //不存在就删除
           $r= GoodsCategory::deleteAll(['id'=>$id]);
            echo json_encode($r);
        }
    }}
