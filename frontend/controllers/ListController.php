<?php
namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsInto;
use frontend\models\Cart;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;

class ListController extends Controller{
    public $enableCsrfValidation=false;
    //goods
    public function actionIndex($id){
        //根据id判断是第二级分类还是三级
        $cate=GoodsCategory::find()->where(['id'=>$id])->one();
        if($cate->depth==2) {//三级分类的时候
            $ids=[$id];
        }
        else{
            //根据id获取到一级或者二级分类下面的全部分类
            $categorys=$cate->children()->select('id')->andWhere(['depth'=>2])->asArray()->all();
            $ids=ArrayHelper::map($categorys,'id','id');
        }
        //分页
        $query=Goods::find();
        $pager=new Pagination(
            [
                'totalCount'=>$query->count(),
                'defaultPageSize'=>4
            ]
        );
        //根据id获取到商品的信息
        $goods = $query->limit($pager->limit)->offset($pager->offset)->where(['in','goods_category_id',$ids])->all();//基本信息
        return $this->render('index',['goods'=>$goods,'pager'=>$pager]);
    }
    //goods->particulars
    public function actionPart($id){
            //根据id获取到商品的信息
            $goods=Goods::find()->where(['id'=>$id])->one();//基本信息
        //给上平的浏览次加一
            $goods->view_times=$goods->view_times+1;
            $goods->save();
//            Goods::updateAllCounters(['view_times'=>1],['id'=>$id]);
            $form=GoodsInto::find()->where(['goods_id'=>$id])->one();//根据获得的商品id等到详情
            //根据图片获取到商品id
            $img=GoodsGallery::find()->where(['goods_id'=>$id])->all();
            //第一张图片
            $img_one=$img[0]->path;
        return $this->render('part',['goods'=>$goods,'form'=>$form,'imgs'=>$img,'img_one'=>$img_one]);
    }
    //购物商品添加到cookie里面
    public function actionAddToCart($goods_id,$amount){
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            //未登录就将是数据存在cookie里面
            //判断购物车里面是否已经有看改该商品如果没有就添加,有就数量追加
            $cookies=\Yii::$app->request->cookies;
            if($cookies->has('cart')){
                $value=$cookies->getValue('cart');
                //var_dump($value);die;
                $cart=unserialize($value);
            }else{
                $cart=[];
            }

            //判断购物中是否存在好商品,存在就数量累加,不存在添加
            if(array_key_exists($goods_id,$cart)){
                $cart[$goods_id]+=$amount;
            }else{
                $cart[$goods_id]=$amount;
            }
            $cookies=\Yii::$app->response->cookies;
            $cookie=new Cookie();
            $cookie->name='cart';
            $cookie->value=serialize($cart);
            $cookies->add($cookie);
        }else{
            $form=Cart::find()->where(['goods_id'=>$goods_id])->one();
            //判断购物车里面时候有这个商品有就追加数量,没有就添加商品
            if($form){
                $count=$form->amount+$amount;
            }else{
                $count=$amount;
            }
            $cart_form=new Cart();
            $cart_form->goods_id=$goods_id;
            $cart_form->amount=$count;
            $cart_form->member_id=\Yii::$app->user->identity->getId();
            $cart_form->save();
        }
        return $this->redirect(['list/cart']);
    }
    //展示购物车的页面
    public function actionCart(){
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            $cookies=\Yii::$app->request->cookies;
            //没有登录就读取cookie里面的商品信息
            $ids='';
            $cart=[];
            if ($cookies->has('cart')){
                $value=$cookies->getValue('cart');
                $cart=unserialize($value);
                //取出存在cookie里面的键
                $ids=array_keys($cart);
            }
//            if(!$cart){
//                return $this->renderPartial('no');
//            }
        }
        else {
            //根据当前登录的用户的id获取数据
            $id = \Yii::$app->user->identity->getId();
            $carts=Cart::find()->where(['member_id'=>$id])->all();
            $ids=[];
            foreach ($carts as  $ca){
                $ids[]=$ca->goods_id;
                $cart[$ca->goods_id]=$ca->amount;
            }
            if(!$carts){
                return $this->renderPartial('no');
            }
            }
        //根据cookie里面的id等到商品的所有有信息
        $models=Goods::find()->where(['in','id',$ids])->all();
        return $this->renderPartial('cart',['models'=>$models,'cart'=>$cart]);
    }
    //商品删除
    public function actionCartDelete($id)
    {
        if (\Yii::$app->user->isGuest) {
            $cookies = \Yii::$app->request->cookies;
            $value = $cookies->getValue('cart');
            $cart = unserialize($value);
            $goods_id = array_keys($cart);
            foreach ($cart as $key => $c) {
                if ($key == $id) {
                    unset($cart[$id]);
                }
            }
            $cookies = \Yii::$app->response->cookies;
            $cookies->remove('cart');
            $cart1 = new Cookie();
            $cart1->name = 'cart';
            $cart1->value = serialize($cart);
            $cookies->add($cart1);
            return json_encode('true');
        }
        else {
            $cart = Cart::find()->where(['goods_id' => $id])->one();
            $v = $cart->delete();
            return json_encode($v);

        }
    }
    //商品数量修改
    public function actionCartAmount(){
        $goods_id=\Yii::$app->request->post('goods_id');
        $amount=\Yii::$app->request->post('amount');
//        var_dump($amount);
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            //未登录
            $cookies=\Yii::$app->request->cookies;
            if($cookies->has('cart')){
                $value=$cookies->getValue('cart');
                //var_dump($value);die;
                $cart=unserialize($value);
            }else{
                $cart=[];
            }
            $cart[$goods_id]=$amount;
            $cookies=\Yii::$app->response->cookies;
            $cookie=new Cookie();
            $cookie->name='cart';
            $cookie->value=serialize($cart);
            $cookies->add($cookie);
        }
        else{
            //登录了
            //得到用户的id
            $id=\Yii::$app->user->identity->getId();
            //根据商品的goods_id 添加信息
            $goods=Cart::find()->where(['goods_id'=>$goods_id])->andWhere(['member_id'=>$id])->one();
            var_dump($goods->amount);
            $goods->amount=$amount;
            $goods->save();
            var_dump($goods->amount);
        }
    }
}
