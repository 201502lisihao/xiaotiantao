<?php

use frontend\widgets\banner\BannerWidgets;
use frontend\widgets\posts\PostsWidgets;
use yii\helpers\Url;


/* @var $this yii\web\View */

$this->title = '小田新闻网';
?>

<div class="site-index">
    <div>
        <?= '<img src="' . Yii::$app->params['webImages']['SiteTitle1'] . '" class="site-index-jumbotron-img">'; ?>
    </div>
    <div class="body-content">
        <!-- 第一部分 12格栅系统-->
        <div class="row">
            <div class="col-sm-8">
                <!-- 图片轮播组件 frontend/widgets/bannner -->
                <?= BannerWidgets::widget() ?>
            </div>
            <div class="col-sm-4">
                <div class="body-content-right">
                        <ul>
                            <li><a href="#" style="font-weight: bold; color: #2472bc;">习近平接受七国新任驻华大使递交国书</a></li>
                            <li><a href="#">2018年三大攻坚战稳扎稳打,初战告捷战局..</a></li>
                            <li><a href="#">时评聚焦：2019年中国经济的航船将驶向..</a></li>
                            <li><a href="#" style="font-weight: bold; color: #2472bc;">中国政府制定第三份对欧盟政策文件</a></li>
                            <li><a href="#">2018年三大攻坚战稳扎稳打,初战告捷战局良好初战告捷战局良好初战告捷战局良好..</a></li>
                            <li><a href="#">2018年三大攻坚战稳扎稳打,初战告捷战局良好初战告捷战局良好初战告捷战局良好..</a></li>
                        </ul>
                        <img src="<?= Yii::$app->params['webImages']['Gaige'] ?>" style="width:100%;height:auto; margin-top: 0px;">
                </div>
            </div>
        </div>
        <!-- 第二部分 滚动新闻 -->
        <div class="row" style="border: 1px solid #2472bc; border-radius: 4px; width: 100%;margin-left: 0px;margin-top: 17px;">
            <div class="col-sm-2" style="border-right: 1px solid #2472bc;">
                <span style="font-size: 17px;font-weight: bold;line-height: 45px; letter-spacing: 2px;">&nbsp;&nbsp;&nbsp;实时动态:</span>
            </div>
            <div class="col-sm-10 site-index-gundong" style="font-size: 16px; color: gray; line-height: 45px;">
                <marquee behavior="scroll" direction="left" scrollamount="5" onmouseover=this.stop() onmouseout=this.start() style="height: 30px;">
                    <a href="#">1、习近平接受七国新任驻华大使递交国书</a>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="#">2、2018年三大攻坚战稳扎稳打,初战告捷战局良好初战告捷战局良好初战告捷战局良好..</a>
                </marquee>
            </div>
            
        </div>
        <!-- 第三部分 分类新闻 -->
        <div class="row" style="margin-top: 20px;">
            <div class="col-sm-12" style="text-align: center;">
                <ul id="myTab" class="nav nav-tabs">
                    <li class="active" style="width: 25%"><a href="#news" data-toggle="tab">热点</a></li>
                    <li style="width: 25%"><a href="#posts" data-toggle="tab">原创</a></li>
                    <li style="width: 25%"><a href="#platform" data-toggle="tab">娱乐</a></li>
                    <li style="width: 25%"><a href="#platform2" data-toggle="tab">体育</a></li>
                </ul>
                <div id="myTabContent" class="tab-content">
                    <!--热点-->
                    <div class="tab-pane fade in active" id="news" style="text-align: left; padding-top: 10px;">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data1'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data1'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-6">
                                <p style="text-align: center; font-size: 18px; color: red;">2018年三大攻坚战稳扎稳打,初战告捷战局</p>
                                <?= '<img src="' . Yii::$app->params['webImages']['Bahe'] . '" style="width:100%;">'; ?>
                            </div>
                        </div>
                    </div>
                    <!--原创-->
                    <div class="tab-pane fade" id="posts" style="text-align: left; padding-top: 10px;">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data2'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data2'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-6">
                                <p style="text-align: center; font-size: 18px; color: red;">时评聚焦：2019年中国经济的航船将驶向何方</p>
                                <?= '<img src="' . Yii::$app->params['webImages']['Lunchuan'] . '" style="width:100%;">'; ?>
                            </div>
                        </div>
                    </div>
                    <!--热点-->
                    <div class="tab-pane fade" id="platform" style="text-align: left; padding-top: 10px;">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data1'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data1'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-6">
                                <p style="text-align: center; font-size: 18px; color: red;">2018年三大攻坚战稳扎稳打,初战告捷战局</p>
                                <?= '<img src="' . Yii::$app->params['webImages']['Bahe'] . '" style="width:100%;">'; ?>
                            </div>
                        </div>
                    </div>
                    <!--原创-->
                    <div class="tab-pane fade" id="platform2" style="text-align: left; padding-top: 10px;">
                        <div class="row">
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data2'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-3">
                                <?php if(!empty($data['data1'])){ ?>
                                    <?php foreach ($data['data2'] as $p) { ?>
                                        <p><i class="fa fa-hacker-news" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;<a href="<?=Url::to(['posts/view','id'=>$p['id']])?>"><?= $p['title']?></a></p>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="col-sm-6">
                                <p style="text-align: center; font-size: 18px; color: red;">时评聚焦：2019年中国经济的航船将驶向何方</p>
                                <?= '<img src="' . Yii::$app->params['webImages']['Lunchuan'] . '" style="width:100%;">'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 第四部分 新闻报刊-->
        <div class="row" style=" padding-top: -5px; border-bottom: 1px solid blue;">
            <div class="col-sm-12">
                <div style="float: left;"><h3>报刊栏</h3></div>
                <div style="float: right; margin-top: 20px; font-size: 16px; color: gray;">以下为友情链接，本站不对其内容负责&nbsp;</div>
            </div>
        </div>
        <div class="row" style="border-bottom: 1px solid blue; padding-bottom: 2px;padding-top: 8px; background-image: url();">
                    <div class="col-sm-2 animated_div">
                        <p style="text-align: center; color: red;font-size: 16px; line-height: 24px; font-weight: bold;">人民日报</p>
                        <?= '<a href= "#"><img src="' . Yii::$app->params['webImages']['Renminribao'] . '" style="width:100%;"></a>'?>
                    </div>
                    <div class="col-sm-2 animated_div">
                        <p style="text-align: center; color: red;font-size: 16px; line-height: 24px;font-weight: bold;">河北青年报</p>
                        <?= '<a href= "#"><img src="' . Yii::$app->params['webImages']['Hebeiqingnianbao'] . '" style="width:100%;"></a>'?>
                    </div>
                    <div class="col-sm-2 animated_div">
                        <p style="text-align: center; color: red;font-size: 16px; line-height: 24px;font-weight: bold;">北京日报</p>
                        <?= '<a href= "#"><img src="' . Yii::$app->params['webImages']['Beijingribao'] . '" style="width:100%;"></a>'?>
                    </div>
                    <div class="col-sm-2 animated_div">
                        <p style="text-align: center; color: red;font-size: 16px; line-height: 24px;font-weight: bold;">上海日报</p>
                        <?= '<a href= "#"><img src="' . Yii::$app->params['webImages']['Shanghairibao'] . '" style="width:100%;"></a>'?>
                    </div>
                    <div class="col-sm-2 animated_div">
                        <p style="text-align: center; color: red;font-size: 16px; line-height: 24px;font-weight: bold;">解放军报</p>
                        <?= '<a href= "#"><img src="' . Yii::$app->params['webImages']['Jiefangjunbao'] . '" style="width:100%;"></a>'?>
                    </div>
                    <div class="col-sm-2 animated_div">
                        <p style="text-align: center; color: red;font-size: 16px; line-height: 24px;font-weight: bold;">纽约时报</p>
                        <?= '<a href= "#"><img src="' . Yii::$app->params['webImages']['Niuyueshibao'] . '" style="width:100%;"></a>'?>
                    </div>
        </div>
        <!--第五部分 底部信息-->
        <div class="row">
            <div class="col-sm-12" style="text-align: center; font-size: 12px; color: gray;">
                <p>&nbsp;</p>
                <p>本网站所刊载信息，仅供学习使用，如涉及侵权，请联系站长删除。 刊用本网站稿件，务经书面授权。</p>
                <p>未经授权禁止转载、摘编、复制及建立镜像，违者将依法追究法律责任。</p>
                <p>[网上传播视听节目许可证（0106168)] [京ICP证xxxxx号] [京公网安备 xxxxxxxxxxxxxxx号] [京ICP备xxxxxxx号] 总机：86-xxxxxxxx</p>
                <p>Copyright ©1999- 2019 xiaotiannews.com. All Rights Reserved </p>
            </div>
        </div>
    </div>
</div>




