<?php
namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsGallery;
use backend\models\GoodsInto;
use frontend\models\Cart;
use frontend\models\Member;
use frontend\models\Order;
use frontend\models\OrderGoods;
use frontend\models\Site;
use frontend\models\SiteDeta;
use yii\data\Pagination;
use yii\db\Exception;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\Cookie;
use yii\web\Request;

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
        return  $this->render('index',['goods'=>$goods,'pager'=>$pager]);
    }
    //goods->particulars
    public function actionPart($id){
            //根据id获取到商品的信息
            $goods=Goods::find()->where(['id'=>$id])->one();//基本信息
        //给商品的浏览次数加一
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
                echo '你的购物车还没有商品!';die;
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
        }
    }
    //确定订单信息
    public function actionClose(){
        //判断用符是否登录
        if(\Yii::$app->user->isGuest){
            //未登录
            return $this->renderPartial('no');
        }else{
            //根据第获取到id等到商品的数据
            $id=$_GET['id'];
            $cart=Cart::find()->where(['member_id'=>$id])->all();
            $ids=[];
            foreach($cart as $ca){
                $ids[]=$ca->goods_id;
                $amount[$ca->goods_id]=$ca->amount;
            }
            //获取商品的所有信息
            $goods=Goods::find()->where(['in','id',$ids])->all();
            //得到收货人的信息
            $site=Site::find()->where(['member_id'=>$id])->all();
            //根据收货人的信息等到详细地址
            $site_id=[];
            foreach($site as $s){
                $site_id[]=$s->id;
            }
            $site_deta=SiteDeta::find()->where(['in','site_id',$site_id])->all();
            $deta=[];
            foreach ($site_deta as $s){
             $deta[$s->site_id]=$s->detailed;
            }
            //得到总商品和总金额
            $money='';
            $sum='';
            foreach($goods as $good){
                $money+=$good->shop_price*$amount[$good->id];
                $sum+=$amount[$good->id];
            }
            return $this->renderPartial('order',['goods'=>$goods,'amount'=>$amount,'site'=>$site,'deta'=>$deta,'money'=>$money,'sum'=>$sum]);
        }
    }
    //提交订单
    public function actionOrder(){
        $request=\Yii::$app->request;
        if($request->isPost){
            $deli_id=$request->post('delivery_id');
            $pay_id=$request->post('pay_id');
            $order=new Order();
            $order->load($request->post(),'');
            //判断是否有收获地址
            if(!$order->address_id){
                echo '请你却认收货地址,如果没有请添加';
                die;
            }
            //根据提交过来的地址id得到详细的地址
            $address=Site::find()->where(['id'=>$order->address_id])->one();
            //根据地址id获取地址详细
            $site_deta=SiteDeta::find()->where(['site_id'=>$address->id])->one();
            $order->name=$address->username;
            $order->province=$address->cmbProvince;
            $order->city=$address->cmbCity;
            $order->area=$address->cmbArea;
            $order->tel=$address->cel;
            $order->address=$site_deta->detailed;
            //派送方式
            $order->delivery=$deli_id;
            $order->delivery_name=Order::$deliveries[$deli_id][0];
            $order->delivery_price=Order::$deliveries[$deli_id][1];
            //支付方式
            $order->payment_id=$pay_id;
            $order->payment_name=Order::$deal[$pay_id][0];
            //状态
            $order->status=1;
            //创建时间
            $order->create_time=time();
            //登录人的id
            $member_id=\Yii::$app->user->identity->getId();
            $order->member_id=$member_id;
          //  开启事务
            $tran=\Yii::$app->db->beginTransaction();
            try {
                //保存订单信息
                if ($order->validate()) {
                    $order->save(false);
                }
                $carts = Cart::find()->where(['member_id' => $member_id])->all();
                //用个变量保存订单金额
                $total_order=0;
                foreach ($carts as $c) {
                    $order_goods = new OrderGoods();
                    $order_goods->order_id = $order->id;
                    $goods = Goods::find()->where(['id' => $c->goods_id])->one();
                    $order_goods->goods_id = $goods->id;
                    //在保存订单信息前判断库存是否足够
                    if ($goods->stock >= $c->amount) {
                        $order_goods->goods_name = $goods->name;
                        $order_goods->logo = $goods->logo;
                        $order_goods->price = $goods->shop_price;
                        $order_goods->amount = $c->amount;
                        $order_goods->total = $goods->shop_price * $c->amount;
                        $goods->stock -= $c->amount;
                        $total_order +=$goods->shop_price * $c->amount;
                        //清空购物车
                        $goods->save(false);
                        $order_goods->save(false);
                        $c->delete();
                    } else {
                        //库存不足 抛出异常
                        throw new Exception('商品的数量不足,请修改购物车');
                    }

                }
                //订单金额
                $order->total=$total_order+Order::$deliveries[$deli_id][1];
                $order->save(false);
            //添加事务
                //根据登录的id获取到拥护email
                $id=\Yii::$app->user->identity->getId();
                $member=Member::find()->where(['id'=>$id])->one();
                $email=$member->email;
                //发送邮件;
                ListController::actionEmail($email);
                $tran->commit();

            }
            catch(Exception $e){
                    //回滚
                $tran->rollBack();
                echo '商品的库存不足,请修改购物车';
                die;
            }
            }
            return $this->renderPartial('over');
        }
    //订单详情
    public function actionOrderSelect(){
        //判断用户是否登录
        if(\Yii::$app->user->isGuest){
            return $this->renderPartial('no');
        }
        else{
        //根据当期登录用户获取到订单详情
            $id=\Yii::$app->user->identity->getId();
            $orders=Order::find()->where(['member_id'=>$id])->all();
            $ids=[];
            foreach($orders as $order){
                $ids[]=$order->id;
            }
            return $this->render('order-select',['orders'=>$orders]);
        }
    }
    /**
     * 商品的搜索
     */
    public function actionSearch(){
        $name=$_GET['goods_name'];
            //根据商品名模糊得到商品的信息
            //分页
            $query=Goods::find();
            $pager=new Pagination(
                [
                    'totalCount'=>$query->count(),
                    'defaultPageSize'=>4
                ]
            );
            //根据id获取到商品的信息
            $goods = $query->limit($pager->limit)->offset($pager->offset)->where(['like','name',$name])->all();//基本信息
        return $this->render('index',['goods'=>$goods,'pager'=>$pager]);}
        /**
         * 发送邮箱
         */
    public static function actionEmail($email){
        \Yii::$app->mailer->compose()
             ->setFrom('18381616032@163.com')
             ->setTo("$email")
            ->setSubject('京西商城购物订单通知')
            ->setHtmlBody('<span style="color:red">尊敬的用户你好:</span>你在京西商城的购物订单 已经下单成功,感谢你的惠顾!祝你生活愉快!')
             ->send();
    }
}
