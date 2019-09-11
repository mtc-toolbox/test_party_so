<?php

use yii\db\Migration;

/**
 * Class m190911_144322_change_stamp_types
 */
class m190911_144322_change_stamp_types extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('ui_Apple_DeletedAtId', 'Apple');
        $this->dropColumn('Apple', 'CreatedAt');
        $this->dropColumn('Apple', 'FalledAt');
        $this->dropColumn('Apple', 'DeletedAt');

        $this->addColumn('Apple', 'CreatedAt', 'INTEGER');
        $this->addCommentOnColumn('Apple', 'CreatedAt', 'Время поавления');

        $this->addColumn('Apple', 'FalledAt', 'INTEGER');
        $this->addCommentOnColumn('Apple', 'FalledAt', 'Время падения');

        $this->addColumn('Apple', 'DeletedAt', 'INTEGER');
        $this->addCommentOnColumn('Apple', 'DeletedAt', 'Время полного поедания');

        $this->createIndex('ui_Apple_DeletedAtId', 'Apple', ['DeletedAt', 'Id']);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190911_144322_change_stamp_types cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190911_144322_change_stamp_types cannot be reverted.\n";

        return false;
    }
    */
}
