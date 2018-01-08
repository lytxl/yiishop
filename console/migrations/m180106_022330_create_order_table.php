<?php

use yii\db\Migration;

/**
 * Handles the creation of table `order`.
 */
class m180106_022330_create_order_table extends Migration
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
        $this->createTable('order', [
            'id' => $this->primaryKey(),
            'member_id'=>$this->integer(3)->notNull()->comment('用户id'),
            'name'=>$this->string(50)->notNull()->comment('收货人'),
            'province'=>$this->string(20)->notNull()->comment('省'),
            'city'=>$this->string(20)->notNull()->comment('市'),
            'area'=>$this->string(20)->notNull()->comment('县'),
            'address'=>$this->string('255')->notNull()->comment('详细地址'),
            'tel'=>$this->char(11)->notNull()->comment('电话'),
            'delivery'=>$this->integer(3)->notNull()->comment('配送方式id'),
            'delivery_name'=>$this->string(45)->notNull()->comment('配送方式名称'),
            'delivery_price'=>$this->float()->notNull()->comment('配送方式方式价格'),
            'payment_id'=>$this->integer(3)->notNull()->comment('支付方式id'),
            'payment_name'=>$this->string(30)->notNull()->comment('支付方式名称'),
            'total'=>$this->decimal()->notNull()->comment('订单金额'),
            'status'=>$this->integer()->notNull()->comment('订单状态'),
            'trade_no'=>$this->string()->notNull()->comment('第三方支付交易号'),
            'create_time'=>$this->integer()->notNull()->comment('创建时间')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('order');
    }
}
