<?php

use yii\db\Migration;

/**
 * Class m190907_121514_create_apple_model
 */
class m190907_121514_create_apple_model extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->getDb()->createCommand("
        CREATE TABLE `Apple`
            (
              `Id` Int NOT NULL AUTO_INCREMENT
             COMMENT 'Идентификатор записи',
              `Color` Int(1)
             COMMENT 'Цвет яблока',
              `IntegrityPercent` Decimal(6,2) DEFAULT 100.00
             COMMENT 'Процент целостности',
              `CreatedAt` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
             COMMENT 'Время появления',
              `FalledAt` TIMESTAMP
             COMMENT 'Время падения',
              `DeletedAt` TIMESTAMP
             COMMENT 'Время полного поедания',
              PRIMARY KEY (`Id`)
            )
             COMMENT = 'Яблоки';
        ")->execute();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('Apple');

        return true;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190907_121514_create_apple_model cannot be reverted.\n";

        return false;
    }
    */
}
