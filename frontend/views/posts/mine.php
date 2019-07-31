<?php

use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->title = Yii::$app->user->identity->username . '的资讯';
$this->params['breadcrumbs'][] = $this->title;

?>
<!-- 这个是我的投稿页的前端页面代码 -->
<div class="row">
	<div class="col-sm-12">
		<div class="panel">
		    <div class="panel-title box-title">
		        <span><?=$data['title']?></span>
		    </div>
		    <div class="new-list">
		    <?php foreach ($data['body'] as $list):?>
		        <div class="panel-body border-bottom">      
		            <div class="row">
		                <div class="col-sm-2 label-img-size">
		                    <a href="<?=Url::to(['posts/view','id'=>$list['id']])?>" class="post-label-mine">
		                        <img src="<?= $list['label_img'] ?>" alt="<?=$list['title']?>">
		                    </a>
		                </div>
		                <div class="col-sm-10 btn-group">
		                    <h1><a href="<?=Url::to(['posts/view','id'=>$list['id']])?>"><?=$list['title']?></a></h1>
		                    <span class="post-tags">
		                        <span class="glyphicon glyphicon-user"></span><a href="<?=Url::to(['member/index','id'=>$list['user_id']])?>"><?=$list['user_name']?></a>&nbsp;
		                        <span class="glyphicon glyphicon-time"></span><?=date('Y-m-d',$list['created_at'])?>&nbsp;
		                        <span class="glyphicon glyphicon-eye-open"></span><?=isset($list['extends']['browser'])?$list['extends']['browser']:0?>&nbsp;
		                        <span class="glyphicon glyphicon-comment"></span><a href="<?=Url::to(['posts/view','id'=>$list['id']])?>"><?=isset($list['extend']['comment'])?$list['extend']['comment']:0?></a>
		                    </span>
		                    <p class="post-content"><?=$list['summary']?></p>
		                    <?php if($list['is_valid']){?>
    							<button class="btn no-radius btn-success btn-sm pull-right-mine" disabled="disabled">审 核 通 过√</button>
							<?php }else{?>
							    <button class="btn no-radius btn-default btn-sm pull-right-mine" disabled="disabled">审 核 中..</button>
							<?php }?>
		                </div>
		            </div>
		        </div>
		    <?php endforeach;?>            
		    </div>
		    <?php if($this->context->page):?>
		    <div class="page"><?=LinkPager::widget(['pagination' => $data['page']]);?></div>
		    <?php endif;?>
		</div>
	</div>
</div>

