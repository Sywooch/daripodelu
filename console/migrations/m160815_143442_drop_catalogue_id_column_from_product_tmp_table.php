<?php

use yii\db\Migration;

/**
 * Handles dropping catalogue_id_column from table `product_tmp`.
 */
class m160815_143442_drop_catalogue_id_column_from_product_tmp_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->dropForeignKey('dpd_product_tmp_fk0', '{{%product_tmp}}');

        $this->dropColumn('{{%product_tmp}}', 'catalogue_id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->addColumn(
            '{{%product_tmp}}',
            'catalogue_id',
            $this->integer()->notNull()->comment('ID категории')
        );

        $this->addForeignKey(
            'dpd_product_tmp_fk0',
            '{{%product_tmp}}',
            'catalogue_id',
            '{{%catalogue_tmp}}',
            'id',
            'RESTRICT',
            'RESTRICT'
        );
    }
}
