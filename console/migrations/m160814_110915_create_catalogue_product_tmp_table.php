<?php

use yii\db\Migration;

/**
 * Handles the creation for table `catalogue_product_tmp`.
 */
class m160814_110915_create_catalogue_product_tmp_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%catalogue_product_tmp}}', [
            'catalogue_id' => $this->integer()->notNull()->comment('ID категории'),
            'product_id' => $this->integer()->notNull()->comment('ID товара'),
            'user_row' => $this->integer(3)->unsigned()->defaultValue(0)->notNull()->comment('Создан пользователем'),
        ], 'ENGINE = InnoDB');

        $this->createIndex(
            'idx-catalogue_product_tmp-cp',
            '{{%catalogue_product_tmp}}',
            ['catalogue_id', 'product_id'],
            true
        );

        $this->createIndex(
            'idx-catalogue_product_tmp-catalogue',
            '{{%catalogue_product_tmp}}',
            'catalogue_id'
        );

        $this->createIndex(
            'idx-catalogue_product_tmp-product',
            '{{%catalogue_product_tmp}}',
            'product_id'
        );

        $this->addForeignKey(
            'fk-catalogue_product_tmp-catalogue',
            '{{%catalogue_product_tmp}}',
            'catalogue_id',
            '{{%catalogue_tmp}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );

        $this->addForeignKey(
            'fk-catalogue_product_tmp-product',
            '{{%catalogue_product_tmp}}',
            'product_id',
            '{{%product_tmp}}',
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
            'idx-catalogue_product_tmp-cp',
            '{{%catalogue_product_tmp}}'
        );

        $this->dropIndex(
            'idx-catalogue_product_tmp-catalogue',
            '{{%catalogue_product_tmp}}'
        );

        $this->dropIndex(
            'idx-catalogue_product_tmp-product',
            '{{%catalogue_product_tmp}}'
        );

        $this->dropForeignKey(
            'fk-catalogue_product_tmp-catalogue',
            '{{%catalogue_product_tmp}}'
        );

        $this->dropForeignKey(
            'fk-catalogue_product_tmp-product',
            '{{%catalogue_product_tmp}}'
        );

        $this->dropTable('{{%catalogue_product_tmp}}');
    }
}
