<?php

use frontend\widgets\posts\NewsPostsWidgets;
use yii\base\Widget;

$this->title = '新闻';
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<div class="col-sm-12">
		<!-- 引用的文章列表组件 目录在frontend/widgets/posts -->
		<?= NewsPostsWidgets::widget(); ?>
	</div>
</div>
