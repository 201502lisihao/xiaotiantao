<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = Yii::t('common','Preview');
$this->params['breadcrumbs'][] = ['label' => Yii::t('common','Information'),'url' => ['posts/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('common','Create'),'url' => ['posts/create']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel-title box-title">
			<span><?= '为《' . $title . '》这篇资讯选择一个预览图吧！' ?></span>
		</div>
		<div class="panel-body">
			<?php $form = ActiveForm::begin(["options" => ["enctype" => "multipart/form-data"]]) ?>
			
			<?= $form->field($model, "file")->fileInput() ?>
			<div>请上传2M以下的图片</div>
			<div>&nbsp;</div>

			<div class="form-group">
				<?= Html::submitButton('提交',['class' => 'btn btn-success']) ?>
			</div>

			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>