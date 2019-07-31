<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;

$this->title = $post['title'];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<div class="col-sm-12">
		<div class="page-title">
			<h1><?= $post['title'] ?></h1>
			<span>作者：<?= $post['user_name']; ?>&nbsp;&nbsp;</span>
			<span>时间：<?= date('Y-m-d',$post['created_at']); ?>&nbsp;&nbsp;</span>
			<span>浏览数：<?= isset($post['extends']['browser'])?$post['extends']['browser']:0 ?>次</span>
			<span>评论数：<?= isset($post['extends']['comment'])?$post['extends']['comment']:0 ?>次</span>
		</div>
		<div class="page-content">
			<?= $post['content']; ?>
		</div>
		<!--写评论区-->
		<div class="post-view-writeComments">
			<?php if(Yii::$app->user->isGuest){ ?>
				<div class="col-sm-12" style="height: 70px;background-color: #e0dddd; text-align: center;">
					<h3>游客登录后可评论</h3>
				</div>
			<?php }else{?>
				<?php $form = ActiveForm::begin(["options" => ["enctype" => "multipart/form-data"]]) ?>
				<?= $form->field($model, 'content')->textarea(['rows' => '5']) ?>
				<div class="form-group">
					<?= Html::submitButton('提交',['class' => 'btn btn-success']) ?>
				</div>
				<?php ActiveForm::end() ?>
			<?php } ?>
		</div>
		<!--展示评论区-->
		<div class="allComments" style="padding-bottom: 5px;">全部评论:</div>
		<div class="post-view-comments">
			<table class="table table-striped">
				<?php foreach ($comments as $comment):?>
					<tr>
						<td>
							<span style="font-size: 18px;"><?= '<img src="' . Yii::$app->params['portrait']['small'] . '" style="width:30px;">' ?>&nbsp;&nbsp;<?= $comment['user'].':' ?></span>
							<span style="float: right; font-size: 14px; color: gray;"><?= date('Y/m/d H:i:s', $comment['create_at']); ?></span>
						</td>
					</tr>
					<tr>
						<td style="line-height: 300%; padding-left: 10px;"><?= $comment['content'] ?></td>
					</tr>
				<?php endforeach; ?>
			</table>
		</div>

		<!-- 底部banner -->
		<div>
        	<?= '<img src="' . Yii::$app->params['webImages']['SiteTitle1'] . '" class="site-index-jumbotron-img">'; ?>
    	</div>
	</div>
</div>