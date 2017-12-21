<?php
$form=\yii\widgets\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'logo')->hiddenInput();
/**
 * @var $this yii\web\View
 */
$this->registerJsFile('@web/webuploader/webuploader.js',['depends'=>\yii\web\YiiAsset::className()]);
$this->registerCssFile('@web/webuploader/webuploader.css');
echo <<<html
    <div id="uploader-demo">
    <!--用来存放item-->
    <div id="fileList" class="uploader-list"></div>
    <img id="img" src="$model->logo" width="85">
    <div id="filePicker">选择图片</div>
</div>
html;
$url=\yii\helpers\Url::to(['brand/uploader']);
$js=<<<JS
    // 初始化Web Uploader
var uploader = WebUploader.create({

    // 选完文件后，是否自动上传。
    auto: true,

    // swf文件路径
    //  swf:  '/web/webup/Uploader.swf',

    // 文件接收服务端。
    server: '{$url}',

    // 选择文件的按钮。可选。
    // 内部根据当前运行是创建，可能是input元素，也可能是flash.
    pick: '#filePicker',

    // 只允许选择图片文件。
    accept: {
        title: 'Images',
        extensions: 'gif,jpg,jpeg,bmp,png',
        mimeTypes: 'image/gif,image/jpeg,image/png,image/jpg,image/bmp'
    }
});
uploader.on( 'uploadSuccess', function( file,response) {
    console.debug(response['url']);
    $('#img').attr('src',response.url);
    $('#brand-logo').val(response.url);
});
JS;
$this->registerJs($js);
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status')->radioList([0=>'隐藏',1=>'正常']);
echo '<button class="btn btn-primary" type="submit">提交</button>';
\yii\widgets\ActiveForm::end();
