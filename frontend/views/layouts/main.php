<?php
/* @var $this \yii\web\View */

/* @var $content string */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;

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
            ];
            $menuItems[] = ['label' => '注册', 'url' => ['/site/signup']];
            $menuItems[] = ['label' => '登录', 'url' => ['/site/login']];
        } else {
            $menuItems = [
                ['label' => '首页', 'url' => ['/site/index']],
            ];
            $menuItems[] = [
                'label' => '<img src = "' . Yii::$app->params['portrait']['small'] . '" alt ="' . Yii::$app->user->identity->username . '">&nbsp;&nbsp;' . Yii::$app->user->identity->username,
                'linkOptions' => ['class' => 'portrait'],
                'items' => [
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
            <p class="pull-left"><a href="http://www.beian.miit.gov.cn">冀ICP备19000109号</a></p>

            <p class="pull-right"><?= Yii::powered() ?></p>
        </div>
    </footer>

    <?php $this->endBody() ?>
    </body>
    </html>
<?php $this->endPage() ?>
