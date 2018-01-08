<?php

use yii\db\Migration;

/**
 * Handles the creation of table `article_detail`.
 */
class m171220_115352_create_article_detail_table extends Migration
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
        $this->createTable('article_detail', [
            'article_id' => $this->primaryKey()->comment('文章id'),
            'content'=>$this->text()->notNull()->comment('简介')
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('article_detail');
    }
}
