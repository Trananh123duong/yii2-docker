<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%project}}`.
 */
class m230915_062721_create_project_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%project}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
            'projectManagerId' => $this->integer(),
            'description' => $this->text(),
            'createDate' => $this->dateTime(),
            'updateDate' => $this->dateTime(),
        ]);

        $this->addForeignKey(
            'fk-project-manager',
            '{{%project}}',
            'projectManagerId',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-project-manager', '{{%project}}');
        $this->dropTable('{{%project}}');
    }
}
