<?php
$form=\yii\widgets\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status')->radioList([-1=>'删除',0=>'隐藏',1=>'正常']);
echo '<button class="btn btn-primary" type="submit">提交</button>';
\yii\widgets\ActiveForm::end();
