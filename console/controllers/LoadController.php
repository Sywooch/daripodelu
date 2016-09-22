<?php

namespace console\controllers;

use yii;
use backend\models\Product;
use common\models\UpdateGiftsDBLog;
use rkdev\loadgifts\LoadGiftsXML;
use \XMLReader;
use rkdev\giftsruxml\CtgProductPairsXMLParse;
use rkdev\giftsruxml\FilterXMLReader;
use rkdev\giftsruxml\ProductXMLReader;
use rkdev\giftsruxml\ProductAttachmentXMLReader;
use rkdev\giftsruxml\ProductFilterXMLReader;
use rkdev\giftsruxml\PrintXMLReader;
use rkdev\giftsruxml\SlaveProductXMLReader;
use rkdev\giftsruxml\StockXMLReader;
use rkdev\giftsruxml\TreeXMLParse;

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

            $treeXMLParser = new TreeXMLParse(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml');
            $treeXMLParser->parse();
            $results = $treeXMLParser->getResult();
            $treeXMLParser->clearResult();
            $treeXMLParser->close();

            $valuesArr = [];
            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['page_id']) && isset($value['parent_page_id'])) {
                        $valuesArr[] = [
                            (int) $value['page_id'],
                            (int) $value['parent_page_id'],
                            (isset($value['name']) ? $value['name']: ''),
                            (isset($value['uri']) ? $value['uri']: ''),
                            0,
                        ];
                    }
                }
            }
            unset($results);

            if (count($valuesArr) > 0) {
                yii::beginProfile('CatalogueInsertIntoDB');
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($valuesArr);
                do {
                    $valuesArrTmp = array_slice($valuesArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%catalogue_tmp}}',
                        ['id', 'parent_id', 'name', 'uri', 'user_row'],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
                yii::endProfile('CatalogueInsertIntoDB');
            }
        }
        catch (\Exception $e) {
            $treeXMLParser->close();
            echo $e->getMessage() . "\n";
        }

        return ;
    }

    /**
     * Запись товаров в БД
     *
     * @throws \Exception
     */
    public function actionInsertprod()
    {
        try {
            //Формирование массива товаров
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found.');
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')) {
                throw new \Exception('File stock.xml not found.');
            }

            $productXMLParser = new ProductXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
            $productXMLParser->parse();
            $results = $productXMLParser->getResult();
            $productXMLParser->clearResult();
            $productXMLParser->close();

            $stockXMLParse = new StockXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml');
            $stockXMLParse->parse();
            $stockResults = $stockXMLParse->getResult();
            $stockXMLParse->clearResult();
            $stockXMLParse->close();

            $stockArr = [];
            if (is_array($stockResults) && count($stockResults) > 0) {
                while (list(, $value) = each($stockResults)) {
                    if (isset($value['product_id'])) {
                        $productId = (int)$value['product_id'];
                        $stockArr[$productId] = [
                            'code' => isset($value['code']) ? $value['code'] : '',
                            'amount' => isset($value['amount']) ? (int) $value['amount'] : 0,
                            'free' => isset($value['free']) ? (int) $value['free'] : 0,
                            'inwayamount' => isset($value['inwayamount']) ? (int) $value['inwayamount'] : 0,
                            'inwayfree' => isset($value['inwayfree']) ? (int) $value['inwayfree'] : 0,
                            'dealerprice' => isset($value['dealerprice']) ? (float) $value['dealerprice'] : 0.00,
                            'enduserprice' => isset($value['enduserprice']) ? (float) $value['enduserprice'] : 0.00,
                        ];
                    }
                }
            }
            unset($stockResults);

            $valuesArr = [];
            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['product_id'])) {
                        $productId = (int)$value['product_id'];
                        $valuesArr[] = [
                            $productId,
                            (isset($value['group']) ? (int)$value['group'] : null),
                            (isset($value['code']) ? (string)$value['code'] : ''),
                            (isset($value['name']) ? (string)$value['name'] : ''),
                            (isset($value['product_size']) ? (string)$value['product_size'] : ''),
                            (isset($value['matherial']) ? (string)$value['matherial'] : ''),
                            (isset($value['small_image']) && isset($value['small_image']['src']) ? (string)$value['small_image']['src'] : ''),
                            (isset($value['big_image']) && isset($value['big_image']['src']) ? (string)$value['big_image']['src'] : ''),
                            (isset($value['super_big_image']) && isset($value['super_big_image']['src']) ? (string)$value['super_big_image']['src'] : ''),
                            (isset($value['content']) ? (string)$value['content'] : ''),
                            (isset($value['status']['id']) ? (string)$value['status']['id'] : null),
                            (isset($value['status']) ? (string)$value['status']['@value'] : ''),
                            (isset($value['brand']) ? (string)$value['brand'] : ''),
                            (isset($value['weight']) ? (float)$value['weight'] : 0.00),
                            (isset($value['pack']['amount']) ? (int)$value['pack']['amount'] : null),
                            (isset($value['pack']['weight']) ? (float)$value['pack']['weight'] : null),
                            (isset($value['pack']['volume']) ? (float)$value['pack']['volume'] : null),
                            (isset($value['pack']['sizex']) ? (float)$value['pack']['sizex'] : null),
                            (isset($value['pack']['sizey']) ? (float)$value['pack']['sizey'] : null),
                            (isset($value['pack']['sizez']) ? (float)$value['pack']['sizez'] : null),
                            (isset($stockArr[$productId]['amount']) ? $stockArr[$productId]['amount'] : 0),
                            (isset($stockArr[$productId]['free']) ? $stockArr[$productId]['free'] : 0),
                            (isset($stockArr[$productId]['inwayamount']) ? $stockArr[$productId]['inwayamount'] : 0),
                            (isset($stockArr[$productId]['inwayfree']) ? $stockArr[$productId]['inwayfree'] : 0),
                            (isset($stockArr[$productId]['enduserprice']) ? $stockArr[$productId]['enduserprice'] : 0.00),
                            0,
                        ];
                    }
                }
            }
            unset($results);

            //Запись информации о товарах в БД
            if (count($valuesArr) > 0) {
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($valuesArr);
                do {
                    $valuesArrTmp = array_slice($valuesArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%product_tmp}}',
                        [
                            'id',
                            'group_id',
                            'code',
                            'name',
                            'product_size',
                            'matherial',
                            'small_image',
                            'big_image',
                            'super_big_image',
                            'content',
                            'status_id',
                            'status_caption',
                            'brand',
                            'weight',
                            'pack_amount',
                            'pack_weigh',
                            'pack_volume',
                            'pack_sizex',
                            'pack_sizey',
                            'pack_sizez',
                            'amount',
                            'free',
                            'inwayamount',
                            'inwayfree',
                            'enduserprice',
                            'user_row'
                        ],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
            }
        }
        catch (\Exception $e) {
            $productXMLParser->close();
            echo $e->getMessage() . "\n";
        }

        return ;
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
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml')) {
                throw new \Exception('File tree.xml not found. "Category-Product" pairs were not inserted in DB.');
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found.');
            }

            $treeXMLParser = new CtgProductPairsXMLParse(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml');
            $treeXMLParser->parse();
            $results = $treeXMLParser->getResult();
            $treeXMLParser->clearResult();
            $treeXMLParser->close();

            $productXMLParser = new ProductXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
            $productXMLParser->parse();
            $productResults = $productXMLParser->getResult();
            $productXMLParser->clearResult();
            $productXMLParser->close();

            $productsArr = [];
            if (is_array($productResults) && count($productResults) > 0) {
                while (list(, $value) = each($productResults)) {
                    if (isset($value['product_id'])) {
                        $productsArr[] = (int)$value['product_id'];
                    }
                }
            }
            unset($productResults);
            $value = null;

            $valuesArr = [];
            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['page']) && isset($value['product'])) {
                        if (!in_array([(int)$value['page'], (int)$value['product'], 0], $valuesArr) && in_array($value['product'], $productsArr)) {
                            $valuesArr[] = [
                                (int)$value['page'],
                                (int)$value['product'],
                                0,
                            ];
                        }
                    }
                }
            }
            unset($results);

            //Запись информации о товарах в БД
            if (count($valuesArr) > 0) {
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($valuesArr);
                do {
                    $valuesArrTmp = array_slice($valuesArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%catalogue_product_tmp}}',
                        [
                            'catalogue_id',
                            'product_id',
                            'user_row'
                        ],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        return ;
    }

    public function actionInsertslaveprod()
    {
        try {
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The slave products were not inserted in DB.');
            }

            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')) {
                throw new \Exception('File stock.xml not found. The slave products were not inserted in DB.');
            }

            $slaveProductXMLParser = new SlaveProductXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
            $slaveProductXMLParser->parse();
            $results = $slaveProductXMLParser->getResult();
            $slaveProductXMLParser->clearResult();
            $slaveProductXMLParser->close();

            $stockXMLParse = new StockXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml');
            $stockXMLParse->parse();
            $stockResults = $stockXMLParse->getResult();
            $stockXMLParse->clearResult();
            $stockXMLParse->close();

            $stockArr = [];
            if (is_array($stockResults) && count($stockResults) > 0) {
                while (list(, $value) = each($stockResults)) {
                    if (isset($value['product_id'])) {
                        $productId = (int)$value['product_id'];
                        $stockArr[$productId] = [
                            'code' => isset($value['code']) ? $value['code'] : '',
                            'amount' => isset($value['amount']) ? (int) $value['amount'] : 0,
                            'free' => isset($value['free']) ? (int) $value['free'] : 0,
                            'inwayamount' => isset($value['inwayamount']) ? (int) $value['inwayamount'] : 0,
                            'inwayfree' => isset($value['inwayfree']) ? (int) $value['inwayfree'] : 0,
                            'dealerprice' => isset($value['dealerprice']) ? (float) $value['dealerprice'] : 0.00,
                            'enduserprice' => isset($value['enduserprice']) ? (float) $value['enduserprice'] : 0.00,
                        ];
                    }
                }
            }
            unset($stockResults);

            $slaveProductsArr = [];
            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['product_id'])) {
                        $productId = (int)$value['product_id'];
                        $mainProductId = (int)$value['main_product'];
                        $slaveProductsArr[] = [
                            $productId,
                            $mainProductId,
                            (isset($value['code']) ? (string)$value['code'] : ''),
                            (isset($value['name']) ? (string)$value['name'] : ''),
                            (isset($value['size_code']) ? (string)$value['size_code'] : ''),
                            (isset($value['weight']) ? (float)$value['weight'] : 0.00),
                            (isset($value['price']['price']) ? (float)$value['price']['price'] : 0.00),
                            (isset($value['price']['currency']) ? (string)$value['price']['currency'] : ''),
                            (isset($value['price']['name']) ? (string)$value['price']['name'] : ''),
                            (isset($stockArr[$productId]['amount']) ? $stockArr[$productId]['amount'] : 0),
                            (isset($stockArr[$productId]['free']) ? $stockArr[$productId]['free'] : 0),
                            (isset($stockArr[$productId]['inwayamount']) ? $stockArr[$productId]['inwayamount'] : 0),
                            (isset($stockArr[$productId]['inwayfree']) ? $stockArr[$productId]['inwayfree'] : 0),
                            (isset($stockArr[$productId]['enduserprice']) ? $stockArr[$productId]['enduserprice'] : 0.00),
                            0,
                        ];
                    }
                }
            }
            unset($results);

            //Запись информации о подчиненных товарах в БД
            if (count($slaveProductsArr) > 0) {
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($slaveProductsArr);
                do {
                    $valuesArrTmp = array_slice($slaveProductsArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%slave_product_tmp}}',
                        [
                            'id',
                            'parent_product_id',
                            'code',
                            'name',
                            'size_code',
                            'weight',
                            'price',
                            'price_currency',
                            'price_name',
                            'amount',
                            'free',
                            'inwayamount',
                            'inwayfree',
                            'enduserprice',
                            'user_row'
                        ],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        return ;
    }

    public function actionInsertattach()
    {
        try {
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The product attachments were not inserted in DB.');
            }

            $attachmentXMLParser = new ProductAttachmentXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
            $attachmentXMLParser->parse();
            $results = $attachmentXMLParser->getResult();
            $attachmentXMLParser->clearResult();
            $attachmentXMLParser->close();

            $prodAttachesArr = [];
            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['product_id'])) {
                        $productId = (int) $value['product_id'];
                        if (isset($value['product_attachment']) && is_array($value['product_attachment']) && count($value['product_attachment']) > 0) {
                            while (list(, $attach) = each($value['product_attachment'])) {
                                $prodAttachesArr[] = [
                                    $productId,
                                    (int) $attach['meaning'],
                                    (isset($attach['file']) ? (string) $attach['file'] : ''),
                                    (isset($attach['image']) ? (string) $attach['image'] : ''),
                                    (isset($attach['name']) ? (string) $attach['name'] : ''),
                                    0,
                                ];
                            }
                        }
                    }
                }
            }
            unset($results);

            //Запись информации о дополнительных файлах товара в БД
            if (count($prodAttachesArr) > 0) {
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($prodAttachesArr);
                do {
                    $valuesArrTmp = array_slice($prodAttachesArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%product_attachment_tmp}}',
                        ['product_id', 'meaning', 'file', 'image', 'name', 'user_row'],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        return ;
    }

    public function actionInsertprint()
    {
        try {
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The product prints were not inserted in DB.');
            }

            $printXMLParser = new PrintXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
            $printXMLParser->parse();
            $results = $printXMLParser->getResult();
            $printXMLParser->clearResult();
            $printXMLParser->close();

            //Парсирование xml файла с продуктами
            $printsArr = [];
            $productPrintsArr = [];

            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['product'])) {
                        $productId = (int) $value['product'];
                        if (isset($value['name'])) {
                            $printId = (string) $value['name'];
                            $productPrintsArr[] = [
                                $productId,
                                $printId,
                                0
                            ];

                            if (!array_key_exists($printId, $printsArr)) {
                                $printsArr[$printId] = [
                                    $printId,
                                    (isset($value['description']) ? (string) $value['description'] : ''),
                                    0
                                ];
                            }
                        }
                    }
                }
            }
            unset($results);

            if (count($printsArr) > 0) {
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($printsArr);
                do {
                    $valuesArrTmp = array_slice($printsArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%print_tmp}}',
                        ['name', 'description', 'user_row'],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);

                if (count($productPrintsArr) > 0) {
                    $valuesArrTmp = [];
                    $counter = 0;
                    $valuesArrLength = count($productPrintsArr);
                    do {
                        $valuesArrTmp = array_slice($productPrintsArr, $counter, $this->batchSize);
                        yii::$app->db->createCommand()->batchInsert(
                            '{{%product_print_tmp}}',
                            ['product_id', 'print_id', 'user_row'],
                            $valuesArrTmp
                        )->execute();
                        $counter += $this->batchSize;
                    }
                    while ($counter < $valuesArrLength);
                }
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        return ;
    }

    public function actionInsertfilters()
    {
        //Формирование таблиц с типами фильтров и фильтрами
        try {
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml')) {
                throw new \Exception('File filters.xml not found.');
            }

            $filterXMLParser = new FilterXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml');
            $filterXMLParser->parse();
            $results = $filterXMLParser->getResult();
            $filterXMLParser->clearResult();
            $filterXMLParser->close();

            $typesArrForInsert = [];
            $filtersArrForInsert = [];
            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['filtertypeid'])) {
                        $filterTypeId = (int) $value['filtertypeid'];
                        $typesArrForInsert[] = [
                            $filterTypeId,
                            (isset($filter['filtertypename']) ? (string) $value['filtertypename'] : ''),
                            0
                        ];

                        if (isset($value['filters']) && is_array($value['filters']) && count($value['filters']) > 0) {
                            while (list(, $filter) = each($value['filters'])) {
                                if (isset($filter['filterid'])) {
                                    $filterId = (int) $filter['filterid'];
                                    $filtersArrForInsert[] = [
                                        $filterId,
                                        (isset($filter['filtername']) ? (string) $filter['filtername'] : ''),
                                        $filterTypeId,
                                        0
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            unset($results);

            if (count($typesArrForInsert) > 0) {
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($typesArrForInsert);
                do {
                    $valuesArrTmp = array_slice($typesArrForInsert, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%filter_type_tmp}}',
                        ['id', 'name', 'user_row'],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
            }

            if (count($filtersArrForInsert) > 0) {
                //Запись фильтров в БД
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($filtersArrForInsert);
                do {
                    $valuesArrTmp = array_slice($filtersArrForInsert, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%filter_tmp}}',
                        ['id', 'name', 'type_id', 'user_row'],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
            }
        }
        catch (\Exception $e) {
            echo $e->getMessage() . "\n";
        }

        return ;
    }

    public function actionInsertprodfilters()
    {
        try {
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The filters for product were not inserted in DB.');
            }

            $productFilterXMLParser = new ProductFilterXMLReader(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
            $productFilterXMLParser->parse();
            $results = $productFilterXMLParser->getResult();
            $productFilterXMLParser->clearResult();
            $productFilterXMLParser->close();

            if (is_array($results) && count($results) > 0) {
                while (list(, $value) = each($results)) {
                    if (isset($value['product_id']) && isset($value['filters'])) {
                        $productId = (int) $value['product_id'];
                        if (is_array($value['filters']) && count($value['filters']) > 0) {
                            while (list(, $filter) = each($value['filters'])) {
                                if (isset($filter['filterid']) && isset($filter['filtertypeid'])) {
                                    $prodFiltersArr[] = [
                                        $productId,
                                        (int) $filter['filterid'],
                                        (int) $filter['filtertypeid'],
                                        0
                                    ];
                                }
                            }
                        }
                    }
                }
            }
            unset($results);

            //Запись связей товар-фильтр в БД
            if (count($prodFiltersArr) > 0) {
                yii::beginProfile('ProductFiltersInsertIntoDB');
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($prodFiltersArr);
                do {
                    $valuesArrTmp = array_slice($prodFiltersArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%product_filter_tmp}}',
                        ['product_id', 'filter_id', 'type_id', 'user_row'],
                        $valuesArrTmp
                    )->execute();
                    $counter += $this->batchSize;
                }
                while ($counter < $valuesArrLength);
                yii::endProfile('ProductFiltersInsertIntoDB');
            }
        }
        catch (\Exception $e) {
            yii::endProfile('ProductFiltersPrepare');
            echo $e->getMessage() . "\n";
        }

        return ;
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
