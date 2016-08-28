<?php

use yii\db\Migration;

/**
 * Handles dropping catalogue_id_column from table `product`.
 */
class m160817_140441_drop_catalogue_id_column_from_product_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropForeignKey('dpd_product_fk0', '{{%product}}');

        $this->dropColumn('{{%product}}', 'catalogue_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn(
            '{{%product}}',
            'catalogue_id',
            $this->integer()->notNull()->comment('ID категории')
        );

        $this->addForeignKey(
            'dpd_product_fk0',
            '{{%product}}',
            'catalogue_id',
            '{{%catalogue}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }
}
