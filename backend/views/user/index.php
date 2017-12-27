<table class="table table-bordered">
    <tr>
        <th>ID</th>
        <th>用户名</th>
        <th>邮箱</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>修改时间</th>
        <th>登录时间</th>
        <th>登录IP</th>
        <th>操作</th>
    </tr>
    <?php foreach($form as $f):?>
        <tr style="text-align: center">
            <td><?=$f->id?></td>
            <td><?=$f->username?></td>
            <td><?=$f->email?></td>
            <td><?=$f->status==1?'可用':'禁用'?></td>
            <td><?=date('Y-m-d H:i:s',$f->created_at)?></td>
            <td><?=date('Y-m-d H:i:s',$f->updated_at)=='1970-01-01 08:00:00'?'':date('Y-m-d H:i:s',$f->updated_at)?></td>
            <td>
                <?=date('Y-m-d H:i:s',$f->last_login_time)=='1970-01-01 08:00:00'?'':date('Y-m-d H:i:s',$f->last_login_time)?>
               </td>
            <td><?=$f->last_login_ip?></td>
            <td>
                <?=\yii\helpers\Html::button('删除',['class'=>'btn btn-info','id'=>$f->id])?>

                <?=\yii\helpers\Html::a('修改',['user/edit','id'=>$f->id],['class'=>'btn btn-primary'])?>
            </td>
        </tr>
    <?php endforeach;?>
    <tr>
        <td colspan="9" style="text-align: center">
            <?=\yii\helpers\Html::a('添加',['user/add'],['class'=>'btn btn-primary'])?>
        </td>
    </tr>
</table>
<?php
$url=\yii\helpers\Url::to(['user/delete']);
/**
 * @var $this \yii\web\View
 */
$js = <<<JS
        $('table').on('click','.btn-info',function() {
          var id=$(this).attr('id');
          var n=$(this);
          if(confirm('是否删除?')){
              $.getJSON("$url?id="+id,function(data) {
                if(data){
                    n.closest('tr').remove();
                }else{
                    alert('删除失败');
                }
              })
          }
        })
JS;
$this->registerJs($js);

