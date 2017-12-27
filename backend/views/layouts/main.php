<?php

/* @var $this \yii\web\View */
/* @var $content string */

use backend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use common\widgets\Alert;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => '良品时光',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    $menuItems = [
        [
            'label'=>'管理员',
            'items'=>[
                ['label' => '管理员列表', 'url' =>['/user/index']],
            ]
        ],[
            'label'=>'商品',
            'items'=>[
                ['label' => '商品列表', 'url' =>['/goods/index']],

                ['label' => '添加商品', 'url' => ['/goods/add']],
                ['label' => '商品分类', 'url' => ['/goods-category/index']],
            ]
        ],
        [
            'label'=>'品牌',
            'items'=>[
                ['label' => '品牌列表', 'url' =>['/brand/index']],

                ['label' => '添加商品', 'url' => ['/brand/add']],
            ]
        ],[
            'label'=>'文章',
            'items'=>[
                ['label' => '文章', 'url' =>['/article/index']],

                ['label' => '添加文章', 'url' => ['/article/add']],
                ['label' => '分类列表', 'url' => ['/article_category/index']],
                ['label' => '添加分类', 'url' => ['/article_category/add']],
            ]
        ],[
            'label'=>'个人中心',
            'items'=>[
                ['label' => '修改个人信息', 'url' =>['/user/edit-one','id'=>\Yii::$app->user->id]],
            ]
        ],[
            'label'=>'管理用户和权限',
            'items'=>[
                ['label' => '权限列表', 'url' =>['/rbac/permission-index']],
                ['label' => '权限添加', 'url' =>['/rbac/permission-add']],
                ['label' => '角色列表', 'url' =>['/rbac/role-index']],
                ['label' => '角色添加', 'url' =>['/rbac/role-add']],
            ]
        ],

    ];
    if (Yii::$app->user->isGuest) {
        $menuItems[] = ['label' => '登录', 'url' => ['/login/index']];
    } else {
        $menuItems[] = '<li>'
            . Html::beginForm(['/login/index'], 'post')
            . Html::submitButton(
                '注销 (' . Yii::$app->user->identity->username . ')',
                ['class' => 'btn btn-link logout']

            )
            . Html::endForm()
            . '</li>';
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        'items' => $menuItems,
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= Alert::widget() ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; My Company <?= date('Y') ?></p>

        <p class="pull-right"><?= Yii::powered() ?></p>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
