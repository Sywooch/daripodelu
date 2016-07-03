<?php

use yii\db\Migration;

/**
 * Handles the creation for table `contacts_item`.
 */
class m160702_202500_create_contacts_item_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%contacts_item}}', [
            'id' => $this->primaryKey(),
            'type' => $this->string(40)->notNull()->comment('Тип'),
            'name' => $this->string(255)->notNull()->comment('Название'),
            'value' => $this->string(255)->defaultValue(NULL)->comment('Значение'),
            'note' => $this->string(255)->defaultValue(NULL)->comment('Примечание'),
            'weight' => $this->integer()->unsigned()->notNull()->comment('Порядок следования'),
            'status' => $this->smallInteger(1)->unsigned()->notNull()->defaultValue(1)->comment('Статус'),
        ], 'ENGINE = InnoDB');

        $this->createIndex(
            'idx-contacts_item-type',
            '{{%contacts_item}}',
            'type'
        );

        $this->createIndex(
            'idx-contacts_item-status',
            '{{%contacts_item}}',
            'status'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-contacts_item-type',
            '{{%contacts_item}}'
        );

        $this->dropIndex(
            'idx-contacts_item-status',
            '{{%contacts_item}}'
        );

        $this->dropTable('{{%contacts_item}}');
    }
}
