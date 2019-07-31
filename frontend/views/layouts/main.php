<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
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
        'brandLabel' => '<img src="' . Yii::$app->params['layout']['logo'] . '" class="layout-logo">',
        //'brandLabel' => '<i class="fa fa-calculator" aria-hidden="true"></i>&nbsp;&nbsp;' . Yii::t('common','Qianzhuli'),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    //获取用户登录状态，判断用户是否登录，如果登录，显示我要投稿按钮
    if (Yii::$app->user->isGuest) {
        $menuItems = [
            ['label' => '首页', 'url' => ['/site/index']],
            ['label' => '新闻', 'url' => ['/posts/news']],
            ['label' => '国内', 'url' => ['/posts/china']],
            ['label' => '国际', 'url' => ['/posts/international']],
            ['label' => '体育', 'url' => ['/posts/sports']],
            ['label' => '娱乐', 'url' => ['/posts/fun']],
            ['label' => '打赏我们', 'url' => ['/site/contact']],
        ];
        $menuItems[] = ['label' => '注册', 'url' => ['/site/signup']];
        $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
    } else {
        $menuItems = [
            ['label' => '首页', 'url' => ['/site/index']],
            ['label' => '新闻', 'url' => ['/posts/news']],
            ['label' => '国内', 'url' => ['/posts/china']],
            ['label' => '国际', 'url' => ['/posts/international']],
            ['label' => '体育', 'url' => ['/posts/sports']],
            ['label' => '娱乐', 'url' => ['/posts/fun']],
            ['label' => '我要投稿', 'url' => ['/posts/create']],
            ['label' => '打赏本站', 'url' => ['/site/contact']],
        ];
        $menuItems[] = [
            'label' => '<img src = "' . Yii::$app->params['portrait']['small'] . '" alt ="'. Yii::$app->user->identity->username . '">&nbsp;&nbsp;' . Yii::$app->user->identity->username,
            'linkOptions' => ['class' => 'portrait'],
            'items' => [
                [
                    'label' => '<i class="fa fa-cube" aria-hidden="true"></i>&nbsp;&nbsp;我的投稿',
                    'url' => ['/posts/mine'],
                ],
                [
                    'label' => '<i class="fa fa-sign-out" aria-hidden="true"></i>&nbsp;&nbsp;退出登录',
                    'url' => ['/site/logout'],
                    'linkOptions' => ['data-method' => 'post'],
                ],
            ],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-right'],
        //代码过滤，不加下面这行的话会在前端显示头像的这个<img>标签代码
        'encodeLabels' => false,
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
