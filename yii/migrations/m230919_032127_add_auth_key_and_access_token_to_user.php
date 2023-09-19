<?php

use yii\db\Migration;

/**
 * Class m230919_032127_add_auth_key_and_access_token_to_user
 */
class m230919_032127_add_auth_key_and_access_token_to_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user}}', 'auth_key', $this->string(32));
        $this->addColumn('{{%user}}', 'access_token', $this->string()->unique());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user}}', 'auth_key');
        $this->dropColumn('{{%user}}', 'access_token');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230919_032127_add_auth_key_and_access_token_to_user cannot be reverted.\n";

        return false;
    }
    */
}
