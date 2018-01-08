<?php

use yii\db\Migration;

/**
 * Handles the creation of table `menu`.
 */
class m171229_021139_create_menu_table extends Migration
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
        $this->createTable('menu', [
            'id' => $this->primaryKey(),
            'name'=>$this->char(20)->notNull()->comment('菜单名'),
            'f_id'=>$this->char(20)->notNull()->comment('上级菜单_id'),
            'route'=>$this->char(20)->notNull()->comment('路由'),
            'sort'=>$this->integer()->notNull()->comment('排序')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('menu');
    }
}
