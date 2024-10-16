<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%PostCategory}}`.
 */
class m241015_112848_create_PostCategory_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%PostCategory}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
        ]);

        $this->batchInsert('PostCategory', ['name'], [
            ['A'],
            ['B'],
            ['C'],
            ['D'],
            ['BE'],
            ['CE'],
            ['Tm'],
            ['Tb'],
            ['M'],
            ['A1'],
            ['B1'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%PostCategory}}');
    }
}
