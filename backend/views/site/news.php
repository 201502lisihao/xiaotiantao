<?php

use yii\helpers\Url;

$this->title = '新闻管理';
?>
<div class="row table-responsive">
    <div class="col-sm-12">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>id</th>
                    <th>标题</th>
                    <th>摘要</th>
                    <th>作者</th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $key => $value): ?>
                    <tr>
                        <td><?= $value['id'] ?></td>
                        <td><?= $value['title'] ?></td>
                        <td><?= $value['summary'] ?></td>
                        <td><?= $value['user_name'] ?></td>
                        <td>
                            <?php if($value['is_valid'] == 1){ ?>
                                <span style="color: green;">已审</span>
                            <?php }else{ ?>
                                <a href="<?=Url::to(['site/checknews','id' => $value['id']])?>">审核</a>
                            <?php } ?>
                        </td>
                        <td><a href="<?=Url::to(['site/delnews','id' => $value['id']])?>">删除</a></td>
                        <td><a href="<?=Url::to(['site/looknews','id' => $value['id']])?>">查看</a></td>
                    </tr>
                <?php endforeach;?> 
            </tbody>
        </table>
    </div>
</div>