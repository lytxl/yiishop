<?php

use yii\db\Migration;

/**
 * Handles the creation of table `brand`.
 */
class m171220_062227_create_brand_table extends Migration
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
        $this->createTable('brand', [
            'id' => $this->primaryKey()->notNull(),
            'name'=>$this->string(25)->notNull()->comment('品牌名'),
            'intro'=>$this->text()->notNull()->comment('简介'),
            'logo'=>$this->char(20)->notNull()->comment('LOGO图片'),
            'sort'=>$this->integer()->notNull()->comment('排序'),
            'status'=>$this->integer()->notNull()->comment('状态')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('brand');
    }
}
