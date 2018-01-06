<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput();
echo $form->field($model,'f_id')->dropDownList($val);
echo $form->field($model,'route')->dropDownList($permission);
echo $form->field($model,'sort')->textInput();
echo \yii\helpers\Html::button('提交',['class'=>'btn btn-primary','type'=>'submit']);
\yii\bootstrap\ActiveForm::end();
