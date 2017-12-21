<?php
namespace backend\controllers;

use backend\models\Brand;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
// 引入鉴权类
use Qiniu\Auth;
// 引入上传类
use Qiniu\Storage\UploadManager;
//文章
class BrandController extends Controller{
    public $enableCsrfValidation=false;
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
            if($model->validate()){
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
            if($model->validate()){
                $model->save();
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['brand/index']);
            }
        }
        else{
            return $this->render('add',['model'=>$model]);
        }
    }
    //删除
    public function actionDelete($id){
        $model=Brand::findOne(['id'=>$id]);
        $model->status=-1;
       $data= $model->save(false);
        echo json_encode($data);
    }
    //处理图片
    public function actionUploader(){
        //实例化图片对象
        $img=UploadedFile::getInstanceByName('file');
        $file='/upload/brand/'.uniqid().'.'.$img->extension;
        //如果图片上传成功就保存
        if($img->saveAs(\Yii::getAlias('@webroot').$file)){
            //七牛-------------------------
            $accessKey ="ONQWIImIY4gjsrb530540cD7sFXR3fK4t6hPlHke";
            $secretKey = "_JiNy97QmSxfAE-jOpnEQtipQq-Q_xHF5vd9ZQMH";
            $bucket = "yiishop";
            $domain='p1aw9ovl0.bkt.clouddn.com';
            // 构建鉴权对象
            $auth = new Auth($accessKey, $secretKey);
            // 生成上传 Token
            $token = $auth->uploadToken($bucket);
            // 要上传文件的本地路径
            $filePath = \Yii::getAlias('@webroot').$file;
            // 上传到七牛后保存的文件名
            $key = $file;
            // 初始化 UploadManager 对象并进行文件的上传。
            $uploadMgr = new UploadManager();
            // 调用 UploadManager 的 putFile 方法进行文件的上传。
            list($ret, $err) = $uploadMgr->putFile($token, $key, $filePath);
            //echo "\n====> putFile result: \n";
            if ($err !== null) {
                //失败
                var_dump($err);
            } else {
                //成功
                $url="http://{$domain}/{$key}";
                echo Json::encode(['url'=>$url]);
            }
            //七牛-------------------------
        }else{
            echo json_encode(false);
        }
    }
}

