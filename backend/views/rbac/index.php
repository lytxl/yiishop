<?php
/**
 * @var $this \yii\web\View
 */
$this->registerCssFile('@web/tables/DataTables-1.10.16/css/jquery.dataTables.css');
$this->registerJsFile('@web/tables/DataTables-1.10.16/js/jquery.dataTables.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
$js=<<<JS
$(document).ready( function () {
    $('#table_id_example').DataTable({
    language: {
        "sProcessing": "处理中...",
        "sLengthMenu": "显示 _MENU_ 项结果",
        "sZeroRecords": "没有匹配结果",
        "sInfo": "显示第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
        "sInfoEmpty": "显示第 0 至 0 项结果，共 0 项",
        "sInfoFiltered": "(由 _MAX_ 项结果过滤)",
        "sInfoPostFix": "",
        "sSearch": "搜索:",
        "sUrl": "",
        "sEmptyTable": "表中数据为空",
        "sLoadingRecords": "载入中...",
        "sInfoThousands": ",",
        "oPaginate": {
            "sFirst": "首页",
            "sPrevious": "上页",
            "sNext": "下页",
            "sLast": "末页"
        },
        "oAria": {
            "sSortAscending": ": 以升序排列此列",
            "sSortDescending": ": 以降序排列此列"
        }
    }
    });
} );
JS;
$this->registerJs($js);
?>
<table id="table_id_example" class="display">
    <thead>
    <tr>
        <th>路由</th>
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
                <?=\yii\helpers\Html::a('删除',['rbac/permission-delete','name'=>$f->name],['class'=>'btn btn-primary'])?>
                <?=\yii\helpers\Html::a('修改',['rbac/permission-edit','name'=>$f->name],['class'=>'btn btn-primary'])?>
            </td>
        </tr>
    <?PHP endforeach;?>
    </tbody>
</table>
