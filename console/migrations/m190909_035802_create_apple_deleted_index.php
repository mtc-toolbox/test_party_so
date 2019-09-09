<?php

use yii\db\Migration;

/**
 * Class m190909_035802_create_apple_deleted_index
 */
class m190909_035802_create_apple_deleted_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('ui_Apple_DeletedAtId', 'Apple', ['DeletedAt', 'Id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190909_035802_create_apple_deleted_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190909_035802_create_apple_deleted_index cannot be reverted.\n";

        return false;
    }
    */
}
