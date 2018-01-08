<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site`.
 */
class m180103_024102_create_site_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
        }
        $this->createTable('site', [
            'id' => $this->primaryKey(),
            'username'=>$this->string(15)->notNull()->comment('收货人姓名'),
            'profile'=>$this->string(100)->notNull()->comment('收货地址'),
            'cel'=>$this->integer(11)->comment('手机号')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('site');
    }
}
