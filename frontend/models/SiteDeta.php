<?php
namespace frontend\models;

use yii\db\ActiveRecord;

class SiteDeta extends ActiveRecord {
    public function rules()
    {
        return [
            ['detailed','required']
        ];
    }
}
