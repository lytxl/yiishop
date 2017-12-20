<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>品牌名</th>
        <th>简介</th>
        <th>商品LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($form as $r):?>
        <tr>
            <td><?=$r->id?></td>
            <td><?=$r->name?></td>
            <td><?=$r->intro?></td>
            <td><img src="<?=$r->logo?>" width="50px"></td>
            <td><?=$r->sort?></td>
            <td>
                <?=$r->status==-1?'删除':''?>
                <?=$r->status==0?'隐藏':''?>
                <?=$r->status==1?'正常':''?>
            </td>
            <td>
            <?= \yii\helpers\Html::button('删除',['class'=>'btn btn-primary','id'=>$r->id])?>
            <?= \yii\helpers\Html::a('修改',['brand/edit','id'=>$r->id],['class'=>'btn btn-info'])?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="7" style="text-align: center">
            <?=\yii\helpers\Html::a('添加',['brand/add'],['class'=>'btn btn-info'])?>
        </td>
    </tr>
</table>
<?php
/**
 * @var $this yii\web\View
 */
$url=\yii\helpers\Url::to(['brand/delete']);
$js=<<<JS
    $('tr').on('click','.btn-primary',function() {
      var id = $(this).attr('id');
      $.getJSON("$url?id="+id,function(data) {
        if(data){
            alert('删出成功')
        }else{
            alert('删出失败')
}      })
    });
JS;
$this->registerJs($js);
?>
