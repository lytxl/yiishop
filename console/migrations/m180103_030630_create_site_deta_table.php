<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_deta`.
 */
class m180103_030630_create_site_deta_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('site_deta', [
            'site_id' =>$this->integer()->notNull()->comment('收货地址id'),
            'detailed'=>$this->string(150)->notNull()->comment('收货详细地址'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('site_deta');
    }
}
