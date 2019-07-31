<?php

$this->title = $post['title'];

?>

<div class="row">
	<div class="col-sm-12">
		<div class="page-title" style="padding-bottom: 10px;">
			<h1><?= $post['title'] ?></h1>
			<span>作者：<?= $post['user_name']; ?>&nbsp;&nbsp;</span>
			<span>时间：<?= date('Y-m-d',$post['created_at']); ?>&nbsp;&nbsp;</span>
		</div>
		<div class="page-content">
			<?= $post['content']; ?>
		</div>
	</div>
</div>