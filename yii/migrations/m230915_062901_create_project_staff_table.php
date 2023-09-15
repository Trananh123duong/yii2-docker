<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%project_staff}}`.
 */
class m230915_062901_create_project_staff_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%project_staff}}', [
            'id' => $this->primaryKey(),
            'projectId' => $this->integer(),
            'userId' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-project-staff-project',
            '{{%project_staff}}',
            'projectId',
            '{{%project}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-project-staff-user',
            '{{%project_staff}}',
            'userId',
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
        $this->dropForeignKey('fk-project-staff-project', '{{%project_staff}}');
        $this->dropForeignKey('fk-project-staff-user', '{{%project_staff}}');
        $this->dropTable('{{%project_staff}}');
    }
}
