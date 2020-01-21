<?php

use yii\helpers\Url;

$this->title = '用户管理';
?>
<div class="row table-responsive">
    <div class="col-sm-12">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>编号</th>
                    <th>微信头像</th>
                    <th>微信昵称</th>
                    <th>性别</th>
                    <th>城市</th>
                    <th>注册时间</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data as $key => $value): ?>
                    <tr>
                        <td><?= $value['id'] ?></td>
                        <td><img style="width:70px;height:70px;" src="<?= $value['headimg'] ?>" alt="<?=$value['nickname']?>"></td>
                        <td><?= base64_decode($value['nickname']) ?></td>
                        <td><?php
                            if($value['gender'] == 1){
                                echo '男生';
                            }elseif($value['gender'] == 2){
                                echo '女生';
                            }else{
                                echo '未知';
                            }
                        ?></td>
                        <td><?= $value['city'] ?></td>
                        <td><?= date('Y-m-d H:i:s',$value['add_time']) ?></td>
                    </tr>
                <?php endforeach;?> 
            </tbody>
        </table>
    </div>
</div>
