<?php

use yii\db\Migration;

/**
 * Handles the creation of table `text`.
 */
class m130524_201800_create_text_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%text}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string()->notNull(),
            'value' => $this->text()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%text}}');
    }
}
