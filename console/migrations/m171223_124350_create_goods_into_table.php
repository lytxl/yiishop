<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods_into`.
 */
class m171223_124350_create_goods_into_table extends Migration
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
        $this->createTable('goods_into', [
            'goods_id' => $this->integer()->comment('商品id'),
            'content' => $this->text()->comment('商品描述'),

        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods_into');
    }
}
