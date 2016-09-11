<?php

namespace console\controllers;

use yii;
use backend\models\Product;
use common\models\UpdateGiftsDBLog;
use rkdev\loadgifts\LoadGiftsXML;
use rkdev\xmlreader\NodeObject;
use rkdev\xmlreader\SimpleXMLReader;

class LoadController extends \yii\console\Controller
{
    /**
     * Количество записей в одном insert-запросе
     */
    private $batchSize = 100;

    /**
     * Очистка таблиц от записей
     *
     * @throws yii\db\Exception
     */
    public function actionDroptables()
    {
        try {
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml')) {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/tree.xml' . '" not found.' . "\n\r");
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/product.xml' . '" not found.' . "\n\r");
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml')) {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/filters.xml' . '" not found.' . "\n\r");
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')) {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/stock.xml' . '" not found.' . "\n\r");
            }

            yii::$app->db->createCommand()->delete('{{%product_print_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%print_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%product_filter_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%product_attachment_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%slave_product_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%catalogue_product_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%product_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%filter_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%filter_type_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%catalogue_tmp}}')->execute();
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    /**
     * Загрузка файлов со стороннего сайта
     *
     * @throws \Exception
     */
    public function actionDownloadxml()
    {
        $loadXMLObject = LoadGiftsXML::getInstance();
        $login = mb_strrpos(yii::$app->basePath, 'daripodelu/console') !== false ? null : yii::$app->config->gateLogin;
        $password = mb_strrpos(yii::$app->basePath, 'daripodelu/console') !== false ? null : yii::$app->config->gatePassword;

        try {
            $treeXML = $loadXMLObject->get(yii::$app->params['gate']['tree'], $login, $password);
            if ($treeXML === false) {
                throw new \Exception('File tree.xml was not processed.');
            }

            $treeXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml');
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        try {
            $productsXML = $loadXMLObject->get(yii::$app->params['gate']['product'], $login, $password);
            if ($treeXML === false) {
                throw new \Exception('File product.xml was not processed.');
            }

            $productsXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        try {
            $filtersXML = $loadXMLObject->get(yii::$app->params['gate']['filters'], $login, $password);
            if ($filtersXML === false) {
                throw new \Exception('File stock.xml was not processed.');
            }

            $filtersXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml');
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        try {
            $stockXML = $loadXMLObject->get(yii::$app->params['gate']['stock'], $login, $password);
            if ($stockXML === false) {
                throw new \Exception('File stock.xml was not processed.');
            }

            $stockXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml');
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function actionDownloadstock()
    {
        $loadXMLObject = LoadGiftsXML::getInstance();
        $login = mb_strrpos(yii::$app->basePath, 'daripodelu/console') !== false ? null : yii::$app->config->gateLogin;
        $password = mb_strrpos(yii::$app->basePath, 'daripodelu/console') !== false ? null : yii::$app->config->gatePassword;

        try {
            Yii::$app->updateGiftsDBLogger->info(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::TYPE_STOCK, 'Началось скачивание файла stock.xml с gifts.ru.');

            $stockXML = $loadXMLObject->get(yii::$app->params['gate']['stock'], $login, $password);
            if ($stockXML === false) {
                throw new \Exception('File stock.xml was not processed.');
            }

            if ($stockXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')) {
                Yii::$app->updateGiftsDBLogger->success(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::TYPE_STOCK, 'Файл stock.xml загружен с gifts.ru и сохранен на сервере.');
            } else {
                Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::TYPE_STOCK, 'Файл stock.xml не удалось сохранить на сервере.');
            }
        }
        catch (\Exception $e) {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::TYPE_STOCK, $e->getMessage());
            echo $e->getMessage() . "\n";
        }
    }

    /**
     * Запись категорий товаров в БД
     *
     * @throws \Exception
     */
    public function actionInsertctg()
    {
        try {
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml')) {
                throw new \Exception('File tree.xml not found.');
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    /**
     * Запись товаров в БД
     *
     * @throws \Exception
     */
    public function actionInsertprod()
    {
        try {
            //Формирование массива категорий
            yii::beginProfile('ProductsPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml')) {
                throw new \Exception('File tree.xml not found. Products were not inserted in DB.');
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found.');
            }
        }
        catch (\Exception $e) {
            yii::endProfile('ProductsPrepare');
            echo $e->getMessage() . "\n";
        }
    }

    /**
     * Запись товаров в БД
     *
     * @throws \Exception
     */
    public function actionInsertCtgProdRel()
    {
        try {
            //Формирование массива категорий
            yii::beginProfile('CtgProductsRelPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml')) {
                throw new \Exception('File tree.xml not found. Products were not inserted in DB.');
            }
            yii::endProfile('CtgProductsRelPrepare');
        }
        catch (\Exception $e) {
            yii::endProfile('CtgProductsRelPrepare');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertslaveprod()
    {
        try {
            yii::beginProfile('SlaveProductsPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The slave products were not inserted in DB.');
            }

            yii::endProfile('SlaveProductsPrepare');
        }
        catch (\Exception $e) {
            yii::endProfile('SlaveProductsPrepare');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertattach()
    {
        try {
            yii::beginProfile('ProductAttachmentsPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The product attachments were not inserted in DB.');
            }

            yii::endProfile('ProductAttachmentsPrepare');
        }
        catch (\Exception $e) {
            yii::endProfile('ProductAttachmentsPrepare');
            yii::endProfile('ProductAttachInsertIntoDB');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertprint()
    {
        try {
            yii::beginProfile('ProductPrintsPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The product prints were not inserted in DB.');
            }

            yii::endProfile('ProductPrintsPrepare');
        }
        catch (\Exception $e) {
            yii::endProfile('ProductPrintsPrepare');
            yii::endProfile('ProductPrintsInsertIntoDB');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertfilters()
    {
        //Формирование таблиц с типами фильтров и фильтрами
        try {
            yii::beginProfile('FiltersPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml')) {
                throw new \Exception('File filters.xml not found.');
            }
            yii::endProfile('FiltersPrepare');
        }
        catch (\Exception $e) {
            yii::endProfile('FiltersPrepare');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertprodfilters()
    {
        try {
            yii::beginProfile('ProductFiltersPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The filters for product were not inserted in DB.');
            }
            yii::endProfile('ProductFiltersPrepare');
        }
        catch (\Exception $e) {
            yii::endProfile('ProductFiltersPrepare');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionMakeimglist()
    {
        try {
            $imagesForDownloadArr = [];
            $results = yii::$app->db->createCommand('
                SELECT [[id]] as `product_id`, [[small_image]] as `image` FROM {{%product_tmp}} WHERE `small_image` IS NOT NULL AND user_row <> 1
                UNION ALL SELECT [[id]] as `product_id`, [[big_image]] as `image` FROM {{%product_tmp}} WHERE `big_image` IS NOT NULL AND user_row <> 1
                UNION ALL SELECT [[id]] as `product_id`, [[super_big_image]] as `image` FROM {{%product_tmp}} WHERE `super_big_image` IS NOT NULL AND user_row <> 1
                UNION ALL SELECT [[product_id]] as `product_id`, [[image]] as `image` FROM {{%product_attachment_tmp}} WHERE `meaning` = 1 AND `image` IS NOT NULL AND user_row <> 1
            ')->queryAll();

            foreach ($results as $row) {
                if ($row['image'] != '' && !file_exists(yii::$app->params['uploadPath'] . '/' . $row['product_id'] . '/' . $row['image'])) {
                    $imagesForDownloadArr[] = ['product_id' => $row['product_id'], 'image' => $row['image']];
                }
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'])) {
                mkdir(yii::$app->params['xmlUploadPath']['current']);
            }

            $f = fopen(yii::$app->params['xmlUploadPath']['current'] . '/imagesforupload.txt', 'w+');
            foreach ($imagesForDownloadArr as $row) {
                fwrite($f, $row['product_id'] . ';' . $row['image'] . "\n");
            }
            fclose($f);
        }
        catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function actionMakefileslist()
    {
        try {
            $filesForDownloadArr = [];
            $results = yii::$app->db->createCommand('
                SELECT [[product_id]] as `product_id`, [[file]] as `file` FROM {{%product_attachment_tmp}} WHERE `meaning` = 0 AND `file` IS NOT NULL AND user_row <> 1
            ')->queryAll();

            foreach ($results as $row) {
                if ($row['file'] != '' && !file_exists(yii::$app->params['uploadPath'] . '/' . $row['product_id'] . '/' . $row['file'])) {
                    $filesForDownloadArr[] = ['product_id' => $row['product_id'], 'file' => $row['file']];
                }
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'])) {
                mkdir(yii::$app->params['xmlUploadPath']['current']);
            }

            $f = fopen(yii::$app->params['xmlUploadPath']['current'] . '/filesforupload.txt', 'w+');
            foreach ($filesForDownloadArr as $row) {
                fwrite($f, $row['product_id'] . ';' . $row['file'] . "\n");
            }
            fclose($f);
        }
        catch (Exception $e) {
            echo $e->getMessage() . "\n";
        }
    }

    public function actionGetimages()
    {
        echo "GateImages action\n";
    }

    public function actionMemory()
    {
        echo memory_get_peak_usage(), "\n";
        echo memory_get_usage(), "\n";
        echo ini_get('memory_limit'), "\n";
        echo ini_get('mysqlnd.net_read_buffer_size'), "\n";
    }
}
