<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>名称</th>
        <th>操作</th>
    </tr>
    <?php foreach($result as $r):?>
        <tr style="text-align: center">
            <td><?=$r->id?></td>
            <td><?=str_repeat('ㅡㅡ',$r->depth).$r->name?></td>
            <td>
                <?=\yii\helpers\Html::a('修改',['goods-category/edit','id'=>$r->id],['class'=>'btn btn-primary'])?>　　
                <?=\yii\helpers\Html::button('删除',['class'=>'btn btn-info','id'=>$r->id])?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="5" style="text-align: center ">
            <?=\yii\helpers\Html::a('添加',['goods-category/add'],['class'=>'btn btn-primary'])?>
        </td>
    </tr>
</table>
<?php
/**
 * @var $this \yii\web\View
 */
$url=\yii\helpers\Url::to(['goods-category/delete']);
    $js=<<<JS
        $('tr').on('click','.btn-info',function() {
         var id=$(this).attr('id');
          var result=confirm('是否删除');
          var del=$(this);
          if(result){
          $.getJSON("$url?id="+id,function(data) {
        if(data){
            del.closest('tr').remove();
        }else{
            alert('删出失败下面存在子分类')
}      })
          }
        })
JS;
$this->registerJs($js);
?>
