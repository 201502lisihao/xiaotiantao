<?php
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = '管理后台-小田新闻网';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Just清单管理后台</h1>

        <p class="lead">http://www.qianzhuli,top</p>

        <span><a class="btn btn-lg btn-success" href="<?=Url::to(['site/user'])?>">用&nbsp;户&nbsp;管&nbsp;理</a></span>
        <!--
        <span>&nbsp;&nbsp;&nbsp;&nbsp;</span>
        <span><a class="btn btn-lg btn-success" href="<?=Url::to(['site/news'])?>">新&nbsp;闻&nbsp;管&nbsp;理</a></span>
        -->
    </div>
</div>
