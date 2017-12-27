<?php
$form=\yii\widgets\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'wornpwd')->passwordInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'password')->passwordInput();
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\widgets\ActiveForm::end();