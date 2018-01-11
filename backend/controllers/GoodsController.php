<?php
namespace backend\controllers;
use backend\filters\RbacFilters;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsGallery;
use backend\models\GoodsInto;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use yii\data\Pagination;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
class GoodsController extends Controller{
    public $enableCsrfValidation=false;
    //首页显示
    public function actionIndex(){
        $name=\Yii::$app->request->get('name');
        $sn=\Yii::$app->request->get('sn');
        $minPrice=\Yii::$app->request->get('minPrice');
        $maxPrice=\Yii::$app->request->get('maxPrice');
        $query=Goods::find();
        if($name){
            $query->where(['like','name',$name]);
        }
        if ($sn){
            $query->andWhere(['like','sn',$sn]);
        }
        if($minPrice){
            $query->andWhere(['>','shop_price',$minPrice]);
        }
        if($maxPrice){
            $query->andWhere(['<','shop_price',$maxPrice]);
        }
        $pager=new Pagination([
            'totalCount'=>$query->count(),
            'defaultPageSize'=>3
        ]);
        $form = $query->limit($pager->limit)->offset($pager->offset)->all();
        $barend=Brand::find()->all();
        $goods_category=GoodsCategory::find()->all();
        $v=[];
        foreach($barend as $bar){
            $v[$bar->id]=$bar->name;
        }
        $f=[];
        foreach($goods_category as $goods){
            $f[$goods->id]=$goods->name;
        }
        return $this->render('index',['form'=>$form,'v'=>$v,'f'=>$f
            ,'pager'=>$pager]);
    }
    //添加
    public function actionAdd(){
        //商品表
        $model=new Goods();
        $request=new Request();
        //详情表
        $content=new GoodsInto();
        if ($request->isPost){
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate() && $content->validate()){
                $time=date('Y-m-d',time());
                $count=GoodsDayCount::find()->where(['day'=>$time])->one();
                //判断是添加还是修改
                if($count){
                    $count->count+=1;
                }else{
                    $count=new GoodsDayCount();
                    $count->day=$time;
                    $count->count=1;
                }
                $count->save();
                //获取时间
                $model->create_time=time();
                $model->sn=date('Ymd').str_pad($count->count,4,0,0);
                $model->save();
                $content->goods_id=$model->id;
                $content->save();
                \Yii::$app->session->setFlash('success','添加成功');
                return $this->redirect(['goods/index']);
            }
        }else{
            $barend=Brand::find()->all();
            $v=[];
            foreach($barend as $bar){
                $v[$bar->id]=$bar->name;
            }
            return $this->render('add',['model'=>$model,'v'=>$v,'content'=>$content]);
        }
    }
    //修改
    public function actionEdit($id){
        //商品
        $model=Goods::findOne(['id'=>$id]);
        //详情
        $content=GoodsInto::findOne(['goods_id'=>$id]);
        $request=new Request();
        if($request->isPost){
            $model->load($request->post());
            $content->load($request->post());
            if($model->validate() && $content->validate()){
                $rows=$model->save();
                $content->save();
                //==============页面静态化==============
                if($rows){
                    //根据id获取到商品的信息
                    $goods=Goods::find()->where(['id'=>$id])->one();//基本信息
                    $form=GoodsInto::find()->where(['goods_id'=>$id])->one();//根据获得的商品id等到详情
                    //根据图片获取到商品id
                    $img=GoodsGallery::find()->where(['goods_id'=>$id])->all();
                    //第一张图片
                    $img_one=$img[0]->path;
                   $contents= $this->renderPartial('part',['goods'=>$goods,'form'=>$form,'imgs'=>$img,'img_one'=>$img_one]);
                   file_put_contents(\Yii::getAlias('@frontend/web').'/goods_'.$id.'.html',$contents);
                }
                \Yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['goods/index']);
            }
        }else{
            $barend=Brand::find()->all();
            $v=[];
            foreach($barend as $bar){
                $v[$bar->id]=$bar->name;
            }
            return $this->render('add',['model'=>$model,'v'=>$v,'content'=>$content]);
        }
    }
    //相册
    public function actionGallery($id){
        $model=GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('gallery',['model'=>$model,'id'=>$id]);
    }
    //保存图片
    public function actionGalleryAdd(){
        $re=\Yii::$app->request;
        if($re->isPost){
            $gallery=new GoodsGallery();
            $gallery->goods_id=$re->post('id');
            $gallery->path=$re->post('resu');
            $rows= $gallery->save();
            $id= \Yii::$app->db->getLastInsertID();
            //==============页面静态化==============
                if($rows){
                    //根据id获取到商品的信息
                    $goods=Goods::find()->where(['id'=>$re->post('id')])->one();//基本信息
                    $form=GoodsInto::find()->where(['goods_id'=>$re->post('id')])->one();//根据获得的商品id等到详情
                    //根据图片获取到商品id
                    $img=GoodsGallery::find()->where(['goods_id'=>$re->post('id')])->all();
                    //第一张图片
                    $img_one=$img[0]->path;
                    $contents= $this->renderPartial('part',['goods'=>$goods,'form'=>$form,'imgs'=>$img,'img_one'=>$img_one]);
                    file_put_contents(\Yii::getAlias('@frontend/web').'/goods_'.$re->post('id').'.html',$contents);
                }
            return Json::encode(['id'=>$id]);
        }
    }
    //相册删除
    public function actionGalleryDelete($id){
        $re=GoodsGallery::deleteAll(['id'=>$id]);
        echo json_encode($re);
    }
    //预览
    public function actionPreview($id){
        $coutent=GoodsInto::find()->where(['goods_id'=>$id])->one();
        $gallery=GoodsGallery::find()->where(['goods_id'=>$id])->all();
        return $this->render('preview',['content'=>$coutent,'gallery'=>$gallery]);
    }
    //处理图片
    public function actionUploader(){
        //实例化图片对象
        $img=UploadedFile::getInstanceByName('file');
        $file='/upload/goods/'.uniqid().'.'.$img->extension;
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
    //富文本编辑器
    public function actions()
    {
        return [
            'ueditor'=>[
                'class' => 'common\widgets\ueditor\UeditorAction',
                'config'=>[
                    //上传图片配置
                    'imageUrlPrefix' => "http://admin.yiishop.com", /* 图片访问路径前缀 */
                    /* 'imagePathFormat' => "/image/{yyyy}{mm}{dd}/{time}{rand:6}", /* 上传保存路径,可以自定义保存路径和文件名格式 */
                ]
            ]
        ];
    }
    //删除
    public function actionDelete($id){
        $result= Goods::deleteAll(['id'=>$id]);
        GoodsInto::deleteAll(['goods_id'=>$id]);
        echo json_encode($result);
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