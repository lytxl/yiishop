<?php
$form=\yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username')->textInput();
echo $form->field($model,'password_hash')->passwordInput();
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'email')->textInput();
echo $form->field($model,'status',['inline'=>1])->radioList(['0'=>'禁用','1'=>'可用']);
echo $form->field($model,'jurisdiction')->checkboxList($roles);
echo \yii\helpers\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();