<?php

use yii\db\Migration;

/**
 * Class m230921_063615_add_password_reset_token_to_user
 */
class m230921_063615_add_password_reset_token_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'password_reset_token', $this->string()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'password_reset_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230921_063615_add_password_reset_token_to_user cannot be reverted.\n";

        return false;
    }
    */
}
