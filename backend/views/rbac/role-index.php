<table class="table table-responsive">
    <thead>
    <tr>
        <th>名称</th>
        <th>描述</th>
        <th>添加时间</th>
        <th>修改时间</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($form as $f):?>
        <tr>
            <td><?=$f->name?></td>
            <td><?=$f->description?></td>
            <td><?=date("Y-m-d H:i:s",$f->createdAt)?></td>
            <td><?=date("Y-m-d H:i:s",$f->updatedAt)?></td>
            <td>
                <?=\yii\helpers\Html::a('删除',['rbac/role-delete','name'=>$f->name],['class'=>'btn btn-info'])?>
                <?=\yii\helpers\Html::a('修改',['rbac/role-edit','name'=>$f->name],['class'=>'btn btn-info'])?>
            </td>
        </tr>
    <?PHP endforeach;?>
    </tbody>
</table>
