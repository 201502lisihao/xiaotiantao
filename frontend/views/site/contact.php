<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\ContactForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\captcha\Captcha;

$this->title = '打赏本站';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-contact">
    <div class="row">
        <div class="col-sm-9">
            <h1 style="text-align: center;">打赏小田新闻网：)</h1>

            <p>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;小田新闻网一直是免费的，并且将一直免费下去，直到这个新闻网站没有存在意义为止。目前没有收费服务的计划，如果您觉得这个网站对你有帮助，请赞助我们。我们承诺，所有赞助资金将仅用于采购服务器以及运维工具，谢谢你的赞助！
            </p>

            <?= '<img src="' . Yii::$app->params['webImages']['PayImg'] . '" class="site-contact-PayImg">'; ?>

            <p style="text-align: center;">
                赞助费用在 50 元及以上，我们会将您的姓名、个人网址等展示到赞助者列表中。
            </p>
        </div>
        <div class="col-sm-3">
            <div class="panel-title box-title">
                <h4 style="text-align: center;">赞助者列表</h4>
            </div>
            <!-- 赞助者列表 -->
            <div class="panel-body">
                <p style="text-align: center;">田迪亚&nbsp;&nbsp;&nbsp;<a href="#">985901085@qq.com</a></p>
            </div>
        </div>
    </div>

</div>
