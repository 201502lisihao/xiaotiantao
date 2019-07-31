<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use xj\ueditor\Ueditor;
use pudinglabs\tagsinput\TagsinputWidget;

//echo '<h1>这是我要投稿的前端代码</h1>';

$this->title = '我要投稿';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<div class="col-sm-12">
		<div class="panel-title box-title">
			<span>创建资讯</span>
		</div>
		<div class="panel-body">
			<?php $form = ActiveForm::begin(["options" => ["enctype" => "multipart/form-data"]]) ?>

			<?= $form->field($model, 'title')->textinput(['maxlength' => true]) ?>
			<?= $form->field($model, 'cat_id')->dropDownlist($cats) ?>
			<?= $form->field($model, 'content')->widget(\crazydb\ueditor\UEditor::className()) ?>
			<div class="form-group">
				<?= Html::submitButton('提交',['class' => 'btn btn-success']) ?>
			</div>

			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>