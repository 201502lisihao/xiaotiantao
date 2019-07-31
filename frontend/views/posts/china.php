<?php

use frontend\widgets\posts\ChinaPostsWidgets;
use yii\base\Widget;

$this->title = '国内';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<div class="col-sm-12">
		<!-- 引用的文章列表组件 目录在frontend/widgets/posts -->
		<?= ChinaPostsWidgets::widget(); ?>
	</div>
</div>
