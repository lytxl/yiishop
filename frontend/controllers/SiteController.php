<?php
namespace frontend\controllers;

use backend\models\Goods;
use backend\models\GoodsCategory;
use frontend\components\Sms;
use frontend\models\Cart;
use frontend\models\Member;
use frontend\models\Site;
use frontend\models\SiteDeta;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use frontend\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\web\Request;
use frontend\models\SignatureHelper;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public $enableCsrfValidation=false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'minLength'=>4,
                'maxLength'=>4,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * 用户注册
     */
    public function actionRegister(){
        $model=new Member();
        $requesrt=Yii::$app->request;
        if($requesrt->post()){
            $model->load($requesrt->post(),'');
            if ($model->validate()){
                $model->password_hash=Yii::$app->security->generatePasswordHash($model->password_hash);//密码转化为哈希值
                $model->created_at=time();
                $model->save(false);
                return $this->redirect(['site/member-login']);
            }
            else{
                var_dump($model->getErrors());die;
            }
        }
        return $this->render('register');
    }
    /**用户名重复验证*/
    public function actionI($username){
        $result=Member::find()->where(['username'=>$username])->one();
        if($result){
            echo 'false';
        }else{
            echo 'true';
        }
    }
    /**
     * 用户登录
     */
    public function actionMemberLogin(){
        $model=new LoginForm();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post(),'');
            if($model->member()){
                //获取当期登录用户的id
                $id = \Yii::$app->user->identity->getId();
                //登录过后将cookie里面的商品追加到商品表里面
                $cookies = \Yii::$app->request->cookies;
                if ($cookies->has('cart')) {
                    $value = $cookies->getValue('cart');
                    $cart = unserialize($value);//反序列化cookie里面的值
                    $goods_id = array_keys($cart);
                    foreach ($goods_id as $cart_id) {
                        $form = Cart::find()->where(['goods_id' => $cart_id, 'member_id' => $id])->one();
                        if ($form) {
                            $form->amount += $cart["$cart_id"];
                            $form->save(false);
                        } else {
                            $form = new Cart();
                            $form->goods_id = $cart_id;
                            $form->amount = $cart[$cart_id];
                            $form->member_id = $id;
                            $form->save(false);
                        }
                    }
                \Yii::$app->response->cookies->remove('cart');
                } else {
                    //根据当前登录用户的id获取所有购物车的信息
                    $ids = Cart::find()->where(['member_id' => $id])->all();

                }
               return $this->redirect('http://www.yiishop.com');
           }
           else{
             echo '登录失败'; die;
           }
        }
        return $this->render('login');
    }
    /**
     * 用户注销
     */
    public function actionMemberCancel(){
        Yii::$app->user->logout();
        return $this->redirect('http://www.yiishop.com');
    }
    /**
     * 添加收货地址
     */
    public function actionSite(){
        //得到当前用户的id
        $id=Yii::$app->user->identity->getId();
        $model=new Site();
        $site=new SiteDeta();
        $request=new Request();
        if($request->isPost){
            $model->load($request->post(),'');
            $site->load($request->post(),'');
            if($site->validate() && $model->validate()){
                $model->member_id=$id;
                if($model->status){
                    $all=Site::find()->where(['member_id'=>$id])->all();
                    //先把所有地址的状态改为0
                    foreach($all as $a){
                        $a->status=0;
                        $a->save();
                    }
                }
                $model->save();
                $site->site_id=$model->id;
                $site->detailed;
                $site->save();
                return $this->redirect(['site/site-index']);
            }
        }
        return $this->render('site');
    }
    /**
     * 收货地址首页
     * @return string
     *
     */
    public function actionSiteIndex($id){
        //从session里面获取到改用户的id
            $form=Site::find()->where(['member_id'=>$id])->all();
            return $this->render('site-index',['form'=>$form]);
            }
    /**
     * 修改地址
     * @param $id
     * @return string
     */
    public function actionSiteEdit($id){
        //得到当前登录用户的id
        $m_id=Yii::$app->user->identity->getId();
        //得到用户的信息
        $form=Site::find()->where(['id'=>$id])->one();
        $detailed=SiteDeta::find()->where(['site_id'=>$id])->one();
        $request=new Request();
        if($request->isPost){
            $form->load($request->post(),'');
            $detailed->load($request->post(),'');
            if($form->validate() && $detailed->validate()){
                if($form->status){
                    $all=Site::find()->where(['member_id'=>$m_id])->all();
                    //先把所有地址的状态改为0
                    foreach($all as $a){
                        $a->status=0;
                        $a->save();
                    }
                }
                $form->save();
                $detailed->save();
                return $this->redirect(['site/site-index']);
            }
        }
        return $this->render('site-edit',['form'=>$form,'detail'=>$detailed]);
    }
    /**
     * 删除地址
     */
    public function actionSiteDelete($id){
        $form=Site::find()->where(['id'=>$id])->one();
        $detailed=SiteDeta::find()->where(['site_id'=>$id])->one();
       $result= $form->delete();
        $detailed->delete();
        echo json_encode($result);
    }
    /**
     * 设置默认地址
     */
    public function actionStatus($id){
        //得到当前登录用户的id
        $m_id=Yii::$app->user->identity->getId();
        $all=Site::find()->where(['member_id'=>$m_id])->all();
        //先把所有地址的状态改为0
        foreach($all as $a){
            $a->status=0;
            $a->save();
        }
        $form=Site::find()->where(['id'=>$id])->one();
        $form->status=1;
        $form->save();
        return $this->redirect(['site/site-index']);
    }
    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    //短信验证码
    public function actionSms($tel){
        if(preg_match("/^1[34578]\d{9}$/", $tel)){
            $code=rand(100000,999999);
            $result= Yii::$app->sms->send($tel,['code'=>$code]);
            if($result->Code=='OK'){
                //将验证码存到cookie里面
                $redis=new \Redis();
                $redis->connect('127.0.0.1');
                $redis->set('code'.$tel,$code,24*3600);
                return 'true';
            }else{
                return '短信发送失败';
            }
        }
        else{
        return '手机号格式错误';
        }

       /* $params = array ();

        // *** 需用户填写部分 ***

        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = "LTAIQFuGuxvKzLzl";
        $accessKeySecret = "37kLVTSBgBMM9BNlmIrA2rcxz5WXDu";

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = "18381616032";

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = "陌上花健身会所";

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = "SMS_120125270";

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = Array (
            "code" => rand(100000,999999),
//            "product" => "阿里通信"
        );

        // fixme 可选: 设置发送短信流水号
//        $params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
//        $params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if(!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"], JSON_UNESCAPED_UNICODE);
        }

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求
        $helper = new SignatureHelper();

        // 此处可能会抛出异常，注意catch
        $content = $helper->request(
            $accessKeyId,
            $accessKeySecret,
            "dysmsapi.aliyuncs.com",
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action" => "SendSms",
                "Version" => "2017-05-25",
            ))
        );*/
//        var_dump($content);
    }
    //验证验证码
    public function actionVerify($captcha,$tel){
        $redis=new \Redis();
        $redis->connect('127.0.0.1');
        $v=$redis->get("code".$tel);
        if ($v){
            if($v==$captcha){
                return 'true';
            }else{

                return 'felse';
            }
        }else{
            return 'felse';
        }
    }
}
