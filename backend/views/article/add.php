<?php
$form=\yii\widgets\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'intro')->textInput();
echo $form->field($model,'article_category_id')->dropDownList($val);
echo $form->field($model,'sort')->textInput();
echo $form->field($model,'status')->radioList([0=>'隐藏',1=>'正常']);
echo $form->field($model,'create_date')->textInput(['type'=>'date']);
echo $form->field($content,'content')->widget(\common\widgets\ueditor\Ueditor::className());
echo '<button class="btn btn-primary" type="submit">提交</button>';
\yii\widgets\ActiveForm::end();
