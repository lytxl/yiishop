<?php
namespace backend\controllers;

use backend\filters\RbacFilters;
use backend\models\Article;
use backend\models\Article_category;
use backend\models\Article_detail;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller{
    //首页
    public function actionIndex(){
        //获取数据显示页面
        $forms=Article::find()->where(['>=','status',0])->all();
        $article_category=Article_category::find()->all();
        $val=[];
        foreach ($article_category as $a){
            $val[$a->id]=$a->name;
        }
        return $this->render('index',['forms'=>$forms,'val'=>$val]);
    }
    //添加
    public function actionAdd(){
        $model=new Article();
        $content=new Article_detail();
        $request = new Request();
        $article_category=Article_category::find()->all();
        $val=[];
        foreach ($article_category as $a){
            $val[$a->id]=$a->name;
        }
        if($request->isPost) {
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate()){
                $model->save(false);
                $content->article_id=$model->id;
                $content->save(false);
                //提示
                \Yii::$app->session->setFlash('success','添加成功');
                //跳转
                return $this->redirect(['article/index']);
            }
        }
        else{

            return $this->render('add',['model'=>$model,'val'=>$val,'content'=>$content]);
        }
    }
    //修改
    public function actionEdit($id){
        $model=Article::findOne(['id'=>$id]);
        $content=Article_detail::findOne(['article_id'=>$id]);
        $article_category=Article_category::find()->all();
        $val=[];
        foreach ($article_category as $a){
            $val[$a->id]=$a->name;
        }
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            $content->load($request->post());
            //后端验证
            if($model->validate()){
                $content->save(false);
                $model->save(false);
                //提示
                \Yii::$app->session->setFlash('success','修改成功');
                //跳转
                return $this->redirect(['article/index']);
            }
        }else{
            return $this->render('add',['model'=>$model,'val'=>$val,'content'=>$content]);
        }
    }
    //删除
    public function actionDelete($id){
        $model=Article::findOne(['id'=>$id]);
        $model->status=-1;
        $data= $model->save(false);
        echo json_encode($data);
    }
    //富文本编辑器
    public function actions()
    {
        return [
        'ueditor'=>[
            'class' => 'common\widgets\ueditor\UeditorAction',
            'config'=>[
                //上传图片配置
                'imageUrlPrefix' => "", /* 图片访问路径前缀 */
                'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
            ]
        ]
    ];
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
