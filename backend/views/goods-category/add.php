<?php
/**
 * @var $this \yii\web\View
 */
$form=\yii\widgets\ActiveForm::begin();
echo $form->field($countries,'name')->textInput();
echo $form->field($countries,'parent_id')->hiddenInput();
echo '<div id="mydiv">
    
</div>';
//======================zTree=====================
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',[
    'depends'=>\yii\web\JqueryAsset::className()
]);
$nodes=\backend\models\GoodsCategory::getNodes();
if ($countries->id){
    $id=$countries->id;
}else{
    $id=0;
}
$url=\yii\helpers\Url::to(['goods-category/edit']);
$js=<<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode) {
		          //获取的值复制给$('#goodscategory-parent_id')
		          $("#goodscategory-parent_id").val(treeNode.id);
		        }
	}
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};
       
            zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
            //展示所有点
            zTreeObj.expandAll(true);
            //回显
            var node=zTreeObj.getNodeByParam('id',$id,null);//根据id获取节点
            zTreeObj.selectNode(node);
JS;
$this->registerJs($js);
echo <<<HTML
<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>
HTML;



//======================zTree=====================
echo $form->field($countries,'intro')->textarea();
echo '<button type="submit" class="btn btn-primary">提交</button>';
\yii\widgets\ActiveForm::end();
