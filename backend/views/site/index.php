<?php

use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = '管理后台-小田新闻网';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>lisihao管理后台</h1>

        <p class="lead">http://www.qianzhuli.top</p>

        <span><a class="btn btn-lg btn-success" href="<?= Url::to(['site/just']) ?>">Just 清 单</a></span>
        <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span><a class="btn btn-lg btn-success" href="<?= Url::to(['site/suggest']) ?>">群 众 反 馈</a></span>
    </div>
</div>
