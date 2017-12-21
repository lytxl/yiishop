<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>文章名</th>
        <th>简介</th>
        <th>分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($forms as $r):?>
        <tr>
            <td><?=$r->id?></td>
            <td><?=$r->name?></td>
            <td><?=$r->intro?></td>
            <td><?=$val[$r->article_category_id]?></td>
            <td><?=$r->sort?></td>
            <td><?=$r->status==1?'正常':'隐藏'?></td>
            <td><?=$r->create_date?></td>
            <td>
                <?= \yii\helpers\Html::button('删除',['class'=>'btn btn-primary','id'=>$r->id])?>
                <?= \yii\helpers\Html::a('修改',['article/edit','id'=>$r->id],['class'=>'btn btn-info'])?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="8" style="text-align: center">
            <?=\yii\helpers\Html::a('添加',['article/add'],['class'=>'btn btn-primary'])?>
        </td>
    </tr>
</table>
<?php
/**
 * @var $this yii\web\View
 */
$url=\yii\helpers\Url::to(['article/delete']);
$js=<<<JS
    $('tr').on('click','.btn-primary',function() {
       var id=$(this).attr('id');
     $(this).closest('tr').remove();
      $.getJSON("$url?id="+id,function(date) {
          if(data){
                alert("删除成功")
            }else{
                alert("删除失败")
            }          
      })
    })
JS;
$this->registerJs($js);
?>

