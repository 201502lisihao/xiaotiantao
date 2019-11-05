<?php

use yii\helpers\Url;

$this->title = '群众意见';
?>
<div class="row table-responsive">
    <div class="col-sm-12">
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>编号</th>
                <th>微信头像</th>
                <th>微信昵称</th>
                <th>意见</th>
                <th>联系方式</th>
                <th>处理状态</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach($data as $key => $value): ?>
                <tr>
                    <td><?= $value['id'] ?></td>
                    <td><img style="width:70px;height:70px;" src="<?= $value['headimg'] ?>" alt="<?=$value['nickname']?>"></td>
                    <td><?= base64_decode($value['nickname']) ?></td>
                    <td><?= $value['suggest'] ?></td>
                    <td><?= $value['contact'] ?></td>
                    <td><?php
                        if($value['status'] == 1){
                            echo '已处理';
                        }else{
                            echo '待处理';
                        }
                        ?></td>
                    <td><a href="<?=Url::to(['site/dealsuggest','id' => $value['id']])?>">处理</a></td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    </div>
</div>
