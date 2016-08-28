<?php

namespace console\controllers;

use yii;
use common\models\UpdateGiftsDBLog;
use common\components\exceptions\SimpleXMLException;

class UpdateController extends \yii\console\Controller
{
    /**
     * Updates information about price and quantity of goods
     */
    public function actionStock()
    {
        $stockArr = [];
        try {
            //Закгрузка файла stock.xml
            yii::beginProfile('update_StockFilePrepare');
            $stockXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')
            );
            yii::endProfile('update_StockFilePrepare');

            if ($stockXML === false) {
                throw new SimpleXMLException('File stock.xml was not processed.');
            }

            //Формирование массива с количеством товаров и их ценами
            yii::beginProfile('update_StockFileAnalyze');
            $stockArr = $this->makeArrFromStockTree($stockXML);
            yii::endProfile('update_StockFileAnalyze');

            $updateProductResult = 0;
            $updateSlaveProductResult = 0;

            if (count($stockArr) > 0) {
                $productResults = Yii::$app->db->createCommand('
                    SELECT [[id]], [[code]] FROM {{%product_tmp}}
                ')->queryAll();
                foreach ($productResults as $row) {
                    if (isset($stockArr[$row['id']])) {
                        $updateProductResult += (int)Yii::$app->db->createCommand()->update(
                            '{{%product_tmp}}',
                            [
                                'amount' => (int)$stockArr[$row['id']]['amount'],
                                'free' => (int)$stockArr[$row['id']]['free'],
                                'inwayamount' => (int)$stockArr[$row['id']]['inwayamount'],
                                'inwayfree' => (int)$stockArr[$row['id']]['inwayfree'],
                                'enduserprice' => (float)$stockArr[$row['id']]['enduserprice'],
                            ],
                            [
                                'id' => $row['id'],
                                'code' => $row['code'],
                            ]
                        )->execute();
                    }
                }

                $slaveProductResults = Yii::$app->db->createCommand('
                    SELECT [[id]], [[code]] FROM {{%slave_product_tmp}}
                ')->queryAll();
                foreach ($slaveProductResults as $row) {
                    if (isset($stockArr[$row['id']])) {
                        $updateSlaveProductResult += (int)Yii::$app->db->createCommand()->update(
                            '{{%slave_product_tmp}}',
                            [
                                'amount' => (int)$stockArr[$row['id']]['amount'],
                                'free' => (int)$stockArr[$row['id']]['free'],
                                'inwayamount' => (int)$stockArr[$row['id']]['inwayamount'],
                                'inwayfree' => (int)$stockArr[$row['id']]['inwayfree'],
                                'enduserprice' => (float)$stockArr[$row['id']]['enduserprice'],
                            ],
                            [
                                'id' => $row['id'],
                                'code' => $row['code'],
                            ]
                        )->execute();
                    }
                }

                Yii::$app->updateGiftsDBLogger->info(UpdateGiftsDBLog::ACTION_UPDATE, UpdateGiftsDBLog::TYPE_PRODUCT, 'Обновлены цены и/или остатки у ' . $updateProductResult . ' товаров во временной таблице.');
                Yii::$app->updateGiftsDBLogger->info(UpdateGiftsDBLog::ACTION_UPDATE, UpdateGiftsDBLog::TYPE_SLAVE_PRODUCT, 'Обновлены остатки у ' . $updateSlaveProductResult . ' подчиненых товаров во временной таблице.');

//                Yii::$app->db->createCommand('CALL gifts_update_stock()')->execute();
                $updateProductResult = (int)Yii::$app->db->createCommand('
                    UPDATE dpd_product as p, dpd_product_tmp as pt
                    SET
                        p.amount = pt.amount,
                        p.free = pt.free,
                        p.inwayamount = pt.inwayamount,
                        p.inwayfree = pt.inwayfree,
                        p.enduserprice = pt.enduserprice
                    WHERE
                        p.id = pt.id and p.code = pt.code
                ')->execute();

                $updateSlaveProductResult = (int)Yii::$app->db->createCommand('
                    UPDATE dpd_slave_product as sp, dpd_slave_product_tmp as spt
                    SET
                        sp.amount = spt.amount,
                        sp.free = spt.free,
                        sp.inwayamount = spt.inwayamount,
                        sp.inwayfree = spt.inwayfree,
                        sp.enduserprice = spt.enduserprice
                    WHERE
                        sp.id = spt.id and sp.code = spt.code
                ')->execute();

                Yii::$app->updateGiftsDBLogger->info(UpdateGiftsDBLog::ACTION_UPDATE, UpdateGiftsDBLog::TYPE_PRODUCT, 'Обновлены цены и/или остатки у ' . $updateProductResult . ' товаров.');
                Yii::$app->updateGiftsDBLogger->info(UpdateGiftsDBLog::ACTION_UPDATE, UpdateGiftsDBLog::TYPE_SLAVE_PRODUCT, 'Обновлены остатки у ' . $updateSlaveProductResult . ' подчиненых товаров.');
            }
        }
        catch (SimpleXMLException $xmlE) {
            yii::endProfile('update_StockFilePrepare');
            yii::endProfile('update_StockFileAnalyze');
            Yii::$app->updateGiftsDBLogger->error(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_STOCK,
                'Ошибка во время парсирования (разбора) файла stock.xml'
            );
            echo $xmlE->getMessage() . "\n";
        }
        catch (\Exception $e) {
            yii::endProfile('update_StockFilePrepare');
            yii::endProfile('update_StockFileAnalyze');
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_STOCK, $e->getMessage());
            echo $e->getMessage() . "\n";
        }
    }

    /**
     * Adds information about new categories in DB of goods
     */
    public function actionCategories()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_CATEGORY,
                'Начало процесса добавления новых категорий в БД.'
            );
            $newCategories = Yii::$app->db->createCommand('SELECT id FROM dpd_catalogue_tmp ct WHERE ct.id NOT IN (SELECT c.id FROM dpd_catalogue c WHERE ct.id = c.id)')->queryAll();
            $result = 0;
            if (count($newCategories) > 0) {
                $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_catalogue SELECT * FROM dpd_catalogue_tmp ct')->execute();
                if ($result > 0) {
                    foreach ($newCategories as $item) {
                        Yii::$app->updateGiftsDBLogger->success(
                            UpdateGiftsDBLog::ACTION_INSERT,
                            UpdateGiftsDBLog::TYPE_CATEGORY,
                            'Добавлена новая категория',
                            $item['id']
                        );
                    }
                }
            }

            if (count($newCategories) == 0 || $result == 0) {
                Yii::$app->updateGiftsDBLogger->info(
                    UpdateGiftsDBLog::ACTION_INSERT,
                    UpdateGiftsDBLog::TYPE_CATEGORY,
                    'Появилось ' . count($newCategories) . ' новых категорий. В БД добавлено ' . $result . ' категорий.'
                );
            }
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_CATEGORY,
                'Окончание процесса добавления новых категорий в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_CATEGORY, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_CATEGORY,
                'Окончание процесса добавления новых категорий в БД.'
            );
        }
    }

    /**
     * Adds information about new types of filters in DB of goods
     */
    public function actionFilterTypes()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER_TYPE,
                'Начало процесса добавления новых типов фильтров в БД.'
            );

            $newFilterTypes = Yii::$app->db->createCommand('SELECT id FROM dpd_filter_type_tmp ftt WHERE ftt.id NOT IN (SELECT ft.id FROM dpd_filter_type ft WHERE ftt.id = ft.id)')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_filter_type SELECT * FROM dpd_filter_type_tmp ftt')->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER_TYPE,
                'Появилось ' . count($newFilterTypes) . ' новых типов фильтров. В БД добавлено ' . $result . ' типов фильтров.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER_TYPE,
                'Окончание процесса добавления новых типов фильтров в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_FILTER_TYPE, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER_TYPE,
                'Окончание процесса добавления новых типов фильтров в БД.'
            );
        }
    }

    /**
     * Adds information about new filters in DB of goods
     */
    public function actionFilters()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER,
                'Начало процесса добавления новых фильтров в БД.'
            );

            $newFilters = Yii::$app->db->createCommand('SELECT ft.id, ft.type_id FROM dpd_filter_tmp ft WHERE (ft.id, ft.type_id) NOT IN (SELECT f.id, f.type_id FROM dpd_filter f WHERE ft.id = f.id AND ft.type_id = f.type_id)')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_filter SELECT * FROM dpd_filter_tmp')->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER,
                'Появилось ' . count($newFilters) . ' новых фильтров. В БД добавлено ' . $result . ' фильтров.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER,
                'Окончание процесса добавления новых фильтров в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_FILTER, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_FILTER,
                'Окончание процесса добавления новых фильтров в БД.'
            );
        }
    }

    /**
     * Adds information about new methods of print in DB of goods
     */
    public function actionPrints()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRINT,
                'Начало процесса добавления новых методов нанесения в БД.'
            );

            $newPrints = Yii::$app->db->createCommand('SELECT name FROM dpd_print_tmp pt WHERE pt.name NOT IN (SELECT p.name FROM dpd_print p WHERE pt.name = p.name)')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_print SELECT * FROM dpd_print_tmp')->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRINT,
                'Появилось ' . count($newPrints) . ' новых методов нанесения. В БД добавлено ' . $result . ' методов нанесения.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRINT,
                'Окончание процесса добавления новых методов нанесения в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_PRINT, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRINT,
                'Окончание процесса добавления новых методов нанесения в БД.'
            );
        }
    }

    /**
     * Adds information about new goods in DB
     */
    public function actionProducts()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT,
                'Начало процесса добавления новых товаров в БД.'
            );

            $newProducts = Yii::$app->db->createCommand('SELECT id FROM dpd_product_tmp pt WHERE pt.id NOT IN (SELECT p.id FROM dpd_product p WHERE pt.id = p.id)')->queryAll();
            $result = 0;
            if (count($newProducts) > 0) {
                $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_product SELECT * FROM dpd_product_tmp')->execute();
                if ($result > 0) {
                    foreach ($newProducts as $item) {
                        Yii::$app->updateGiftsDBLogger->success(
                            UpdateGiftsDBLog::ACTION_INSERT,
                            UpdateGiftsDBLog::TYPE_PRODUCT,
                            'Добавлен новый товар',
                            $item['id']
                        );
                    }
                }
            }

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT,
                'Появилось ' . count($newProducts) . ' новых товаров. В БД добавлено ' . $result . ' товаров.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT,
                'Окончание процесса добавления новых товаров в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_PRODUCT, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT,
                'Окончание процесса добавления новых товаров в БД.'
            );
        };
    }

    public function actionCategoryProductRel()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_CATEGORY_PRODUCT_REL,
                'Начало процесса добавления новых связей между категориями и товарами в БД.'
            );

            $newCategoryProductRel = Yii::$app->db->createCommand('SELECT * FROM dpd_catalogue_product_tmp cpt WHERE (cpt.catalogue_id, cpt.product_id) NOT IN (SELECT cp.catalogue_id, cp.product_id FROM dpd_catalogue_product cp WHERE cpt.catalogue_id = cp.catalogue_id AND cpt.product_id = cp.product_id)')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_catalogue_product
                                                        SELECT *
                                                        FROM dpd_catalogue_product_tmp cpt
                                                        WHERE
                                                            (cpt.catalogue_id, cpt.product_id) NOT IN (
                                                                SELECT cp.catalogue_id, cp.product_id
                                                                FROM dpd_catalogue_product cp
                                                                WHERE cpt.catalogue_id = cp.catalogue_id AND cpt.product_id = cp.product_id
                                                            )'
            )->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_CATEGORY_PRODUCT_REL,
                'Окончание процесса добавления новых связей между категориями и товарами в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_CATEGORY_PRODUCT_REL, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_CATEGORY_PRODUCT_REL,
                'Окончание процесса добавления новых связей между категориями и товарами в БД.'
            );
        }
    }

    /**
     * Adds information about new "slave goods" in DB
     */
    public function actionSlaveProducts()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_SLAVE_PRODUCT,
                'Начало процесса добавления новых "подчиненных" товаров в БД.'
            );

            $newSlaveProducts = Yii::$app->db->createCommand('SELECT spt.id FROM dpd_slave_product_tmp spt WHERE spt.id NOT IN (SELECT sp.id FROM dpd_slave_product sp WHERE spt.id = sp.id)')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_slave_product SELECT * FROM dpd_slave_product_tmp')->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_SLAVE_PRODUCT,
                'Появилось ' . count($newSlaveProducts) . ' новых "подчиненных" товаров. В БД добавлено ' . $result . ' "подчиненных" товаров.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_SLAVE_PRODUCT,
                'Окончание процесса добавления новых "подчиненных" товаров в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_SLAVE_PRODUCT, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_SLAVE_PRODUCT,
                'Окончание процесса добавления новых "подчиненных" товаров в БД.'
            );
        }
    }

    /**
     * Adds information about new attachments (files or images) of goods in DB
     */
    public function actionProductAttachments()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_ATTACH,
                'Начало процесса добавления информации о доп. файлах товаров в БД.'
            );

            $newsAttachments = Yii::$app->db->createCommand('SELECT * FROM dpd_product_attachment_tmp pat WHERE (pat.product_id, pat.file, pat.image) NOT IN (SELECT pa.product_id, pa.file, pa.image FROM dpd_product_attachment pa WHERE pat.product_id = pa.product_id AND (pat.file = pa.file OR pat.image = pa.image))')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_product_attachment
                                                        SELECT *
                                                        FROM dpd_product_attachment_tmp pat
                                                        WHERE
                                                            (pat.product_id, pat.file, pat.image) NOT IN (
                                                                SELECT pa.product_id, pa.file, pa.image
                                                                FROM dpd_product_attachment pa
                                                                WHERE pat.product_id = pa.product_id AND (pat.file = pa.file OR pat.image = pa.image)
                                                            )
            ')->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_ATTACH,
                'Появилось ' . count($newsAttachments) . ' новых доп. файлов товаров. В БД добавлена информаци о ' . $result . ' доп. файлов.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_ATTACH,
                'Окончание процесса добавления информации о доп. файлах товаров в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_PRODUCT_ATTACH, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_ATTACH,
                'Окончание процесса добавления информации о доп. файлах товаров в БД.'
            );
        }
    }

    public function actionPrintProductRel()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_PRINT_REL,
                'Начало процесса добавления новых связей между товарами и методами нанесения в БД.'
            );

            $newPrintProductRel = Yii::$app->db->createCommand('SELECT * FROM dpd_product_print_tmp ppt WHERE (ppt.product_id, ppt.print_id) NOT IN (SELECT pp.product_id, pp.print_id FROM dpd_product_print pp WHERE ppt.product_id = pp.product_id AND ppt.print_id = pp.print_id)')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_product_print
                                                        SELECT *
                                                        FROM dpd_product_print_tmp ppt
                                                        WHERE
                                                            (ppt.product_id, ppt.print_id) NOT IN (
                                                                SELECT pp.product_id, pp.print_id
                                                                FROM dpd_product_print pp
                                                                WHERE ppt.product_id = pp.product_id AND ppt.print_id = pp.print_id
                                                            )'
            )->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_PRINT_REL,
                'Появилось ' . count($newPrintProductRel) . ' новых связей между товарами и методами нанесения. В БД добавлено ' . $result . ' связей.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_PRINT_REL,
                'Окончание процесса добавления новых связей между товарами и методами нанесения в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_PRODUCT_PRINT_REL, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_PRINT_REL,
                'Окончание процесса добавления новых связей между товарами и методами нанесения в БД.'
            );
        }
    }

    public function actionProductFilterRel()
    {
        try {
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_FILTER_REL,
                'Начало процесса добавления новых связей между товарами и фильтрами в БД.'
            );

            $newProductFilterRel = Yii::$app->db->createCommand('SELECT * FROM dpd_product_filter_tmp pft WHERE (pft.product_id, pft.filter_id, pft.type_id) NOT IN (SELECT pf.product_id, pf.filter_id, pf.type_id FROM dpd_product_filter pf WHERE pft.product_id = pf.product_id AND pft.filter_id = pf.filter_id AND pft.type_id = pf.type_id)')->queryAll();
            $result = Yii::$app->db->createCommand('INSERT IGNORE INTO dpd_product_filter
                                                        SELECT *
                                                        FROM dpd_product_filter_tmp pft
                                                        WHERE
                                                            (pft.product_id, pft.filter_id, pft.type_id) NOT IN (
                                                                SELECT pf.product_id, pf.filter_id, pf.type_id
                                                                FROM dpd_product_filter pf
                                                                WHERE pft.product_id = pf.product_id AND pft.filter_id = pf.filter_id AND pft.type_id = pf.type_id)'
            )->execute();

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_FILTER_REL,
                'Появилось ' . count($newProductFilterRel) . ' новых связей между товарами и фильтрами. В БД добавлено ' . $result . ' связей.'
            );

            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_FILTER_REL,
                'Окончание процесса добавления новых связей между товарами и фильтрами в БД.'
            );
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_INSERT, UpdateGiftsDBLog::TYPE_PRODUCT_FILTER_REL, $e->getMessage());
            Yii::$app->updateGiftsDBLogger->info(
                UpdateGiftsDBLog::ACTION_INSERT,
                UpdateGiftsDBLog::TYPE_PRODUCT_FILTER_REL,
                'Окончание процесса добавления новых связей между товарами и фильтрами в БД.'
            );
        }
    }

    protected function makeArrFromStockTree(\SimpleXMLElement $tree)
    {
        $arr = [];
        foreach ($tree->stock as $stock) {
            $productId = (int)$stock->product_id;
            $arr[$productId] = [
                'product_id' => $productId,
                'code' => (string)$stock->code,
                'amount' => (int)$stock->amount,
                'free' => (int)$stock->free,
                'inwayamount' => (int)$stock->inwayamount,
                'inwayfree' => (int)$stock->inwayfree,
                'enduserprice' => (float)$stock->enduserprice,
            ];
        }

        return $arr;
    }
}
