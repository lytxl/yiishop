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
        $result = GoodsCategory::find()->orderBy('tree,lft')->all();
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
        $parent_id=$countries->parent_id;
        $request = new Request();
        if ($request->isPost) {
            $countries->load($request->post());
            if ($countries->validate()) {
                if ($countries->parent_id) {
                    //存在子就追加
                    $parent = GoodsCategory::findOne(['id' => $countries->parent_id]);
                    $countries->appendTo($parent);
                } else {
                    if($parent_id==0){
                        //在根节点修改为跟节点时使用makeRoot就会报错
                        $countries->save();
                    }else{
                        $countries->makeRoot();
                    }
                }
                \Yii::$app->session->setFlash('success', '修改成功');
                return $this->redirect(['goods-category/index']);
            }
        }
        return $this->render('add', ['countries' => $countries]);

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
