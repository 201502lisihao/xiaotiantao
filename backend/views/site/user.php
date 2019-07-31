<?php

use yii\helpers\Url;

$this->title = '用户管理';
?>
<div class="row table-responsive">
    <div class="col-sm-12">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>id</th>
                    <th>用户名</th>
                    <th>邮箱</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $key => $value): ?>
                    <tr>
                        <td><?= $value['id'] ?></td>
                        <td><?= $value['username'] ?></td>
                        <td><?= $value['email'] ?></td>
                        <td><a href="<?=Url::to(['site/deluser','id' => $value['id']])?>">删除</a></td>
                    </tr>
                <?php endforeach;?> 
            </tbody>
        </table>
    </div>
</div>