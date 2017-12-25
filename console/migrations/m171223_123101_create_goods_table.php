<?php

use yii\db\Migration;

/**
 * Handles the creation of table `goods`.
 */
class m171223_123101_create_goods_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('goods', [
            'id' => $this->primaryKey(),
            'name'=>$this->char(20)->notNull()->comment('商品名'),
            'sn'=>$this->char(20)->notNull()->comment('货号'),
            'logo'=>$this->char(255)->notNull()->comment('LOGO图片'),
            'goods_category_id'=>$this->integer()->notNull()->comment('商品分类'),
            'brand_id'=>$this->string(50)->notNull()->comment('品牌分类'),
            'market_price'=>$this->decimal(10,2)->notNull()->comment('市场价格'),
            'shop_price'=>$this->decimal(10,2)->notNull()->comment('商品价格'),
            'stock'=>$this->integer()->notNull()->comment('库存'),
            'is_on_sale'=>$this->integer()->notNull()->comment('是否在销'),
            'status'=>$this->integer()->notNull()->comment('状态'),
            'sort'=>$this->integer()->notNull()->comment('排序'),
            'create_time'=>$this->integer()->notNull()->comment('添加时间'),
            'view_times'=>$this->integer()->notNull()->comment('浏览次数'),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('goods');
    }
}
