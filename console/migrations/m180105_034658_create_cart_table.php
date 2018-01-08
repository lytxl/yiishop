<?php

use yii\db\Migration;

/**
 * Handles the creation of table `cart`.
 */
class m180105_034658_create_cart_table extends Migration
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
        $this->createTable('cart', [
            'id' => $this->primaryKey(),
            'goods_id'=>$this->string(2)->notNull()->comment('商品id'),
            'amount'=>$this->string(5)->notNull()->comment('商品的数量'),
            'member_id'=>$this->string(2)->notNull()->comment('用户的id')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('cart');
    }
}
