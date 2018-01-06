<div class="topnav">
    <div class="topnav_bd w1210 bc">
        <div class="topnav_left">

        </div>
        <div class="topnav_right fr">
            <ul>
                <li><?=isset(Yii::$app->user->identity->username)?(Yii::$app->user->identity->username).'[<a href="/site/member-cancel">注销</a>]':'你好，欢迎来到京西！[<a href="/site/member-login">登录</a>][<a href="/site/register">免费注册</a>]'?>
                </li>
                <li class="line">|</li>
                <li>我的订单</li>
                <li class="line">|</li>
                <li>客户服务</li>

            </ul>
        </div>
    </div>
</div>
<?=$content?>
