<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class Site extends ActiveRecord{
    public function rules()
    {
        return [
         [['username','cmbProvince','cmbCity','cmbArea','cel'],'required'],
            ['status','default','value'=>null]
        ];
    }

}
