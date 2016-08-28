<?php

use yii\db\Migration;

/**
 * Handles the creation for table `catalogue_product`.
 */
class m160814_110029_create_catalogue_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%catalogue_product}}', [
            'catalogue_id' => $this->integer()->notNull()->comment('ID категории'),
            'product_id' => $this->integer()->notNull()->comment('ID товара'),
            'user_row' => $this->integer(3)->unsigned()->defaultValue(0)->notNull()->comment('Создан пользователем'),
        ], 'ENGINE = InnoDB');

        $this->createIndex(
            'idx-catalogue_product-cp',
            '{{%catalogue_product}}',
            ['catalogue_id', 'product_id'],
            true
        );

        $this->createIndex(
            'idx-catalogue_product-catalogue',
            '{{%catalogue_product}}',
            'catalogue_id'
        );

        $this->createIndex(
            'idx-catalogue_product-product',
            '{{%catalogue_product}}',
            'product_id'
        );

        $this->addForeignKey(
            'fk-catalogue_product-catalogue',
            '{{%catalogue_product}}',
            'catalogue_id',
            '{{%catalogue}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-catalogue_product-product',
            '{{%catalogue_product}}',
            'product_id',
            '{{%product}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropIndex(
            'idx-catalogue_product-cp',
            '{{%catalogue_product}}'
        );

        $this->dropIndex(
            'idx-catalogue_product-catalogue',
            '{{%catalogue_product}}'
        );

        $this->dropIndex(
            'idx-catalogue_product-product',
            '{{%catalogue_product}}'
        );

        $this->dropForeignKey(
            'fk-catalogue_product-catalogue',
            '{{%catalogue_product}}'
        );

        $this->dropForeignKey(
            'fk-catalogue_product-product',
            '{{%catalogue_product}}'
        );

        $this->dropTable('{{%catalogue_product}}');
    }
}
