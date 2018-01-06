<h2>菜单管理</h2>
<table class="table table-responsive">
    <tr>
        <th>名称</th>
        <th>路由</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach($form as $f):?>
        <tr>
            <td><?=$f->name?></td>
            <td><?=$f->route?></td>
            <td><?=$f->sort?></td>
            <td>
                <?=\yii\helpers\Html::button('删除',['class'=>'btn btn-warning','id'=>$f->id])?>
                <?=\yii\helpers\Html::a('修改',['menu/edit','id'=>$f->id],['class'=>'btn btn-primary'])?>
            </td>
        </tr>
    <?php endforeach;?>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url=\yii\helpers\Url::to(['menu/delete']);
$js=<<<JS
    $('table').on('click','.btn-warning',function() {
      var id=$(this).attr('id');
      var n=$(this);
      if(confirm('是否要删除?')){
          $.getJSON("$url?id="+id,function(data) {
            if(data){
                n.closest('tr').remove()
            }else{
                alert('删除失败!');
            }
          })
      }
    })
JS;
$this->registerJs($js);
?>
