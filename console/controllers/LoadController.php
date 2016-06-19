<?php

namespace console\controllers;

use yii;
use backend\models\Product;
use common\models\UpdateGiftsDBLog;
use rkdev\loadgifts\LoadGiftsXML;

class LoadController extends \yii\console\Controller
{
    /**
    * Количество записей в одном insert-запросе
    */
    private $batchSize = 100;

    protected function makeArrFromTree(\SimpleXMLElement $tree)
    {
        $arr = [];
        foreach($tree as $key => $node)
        {
            $row = [
                'id' => (int) $node->page_id,
                'parent_id' => isset($node['parent_page_id'])? (int) $node['parent_page_id']: 0,
                'name' => (string) $node->name,
                'uri' => (string) $node->uri,
            ];

            if(isset($node->product))
            {
                foreach($node->product as $key => $product)
                {
                    $row['product'][] = [
                        'parent_id' => (int) $product->page,
                        'product_id' => (int) $product->product,
                    ];
                }
            }

            $arr[] = $row;

            if (isset($node->page))
            {
                $tmpArr = $this->makeArrFromTree($node->page);
                $arr = array_merge($arr, $tmpArr);
            }

        }

        return $arr;
    }

    protected function makeArrFromFilterTree(\SimpleXMLElement $tree)
    {
        $arr = [];
        foreach ($tree->filtertypes->filtertype as $element)
        {
            $filtersArr = [];
            if (isset($element->filters->filter))
            {
                if (count($element->filters->filter) > 1)
                {
                    foreach ($element->filters->filter as $filter)
                    {
                        $filtersArr[] = [
                            'filterid' => (int) $filter->filterid,
                            'filtername' => (string) $filter->filtername,
                        ];
                    }
                }
                else
                {
                    $filtersArr[] = [
                        'filterid' => (int) $element->filters->filter->filterid,
                        'filtername' => (string) $element->filters->filter->filtername,
                    ];
                }
            }

            $arr[] = [
                'filtertypeid' => (int) $element->filtertypeid,
                'filtertypename' => (string) $element->filtertypename,
                'filters' => $filtersArr,
            ];
        }

        return $arr;
    }

    protected function makeArrFromStockTree(\SimpleXMLElement $tree)
    {
        $arr = [];
        foreach ($tree->stock as $stock)
        {
            $productId = (int) $stock->product_id;
            $arr[$productId] = [
                'product_id' => $productId,
                'code' => (string) $stock->code,
                'amount' => (int) $stock->amount,
                'free' => (int) $stock->free,
                'inwayamount' => (int) $stock->inwayamount,
                'inwayfree' => (int) $stock->inwayfree,
                'enduserprice' => (float) $stock->enduserprice,
            ];
        }

        return $arr;
    }

    /**
     * Очистка таблиц от записей
     *
     * @throws yii\db\Exception
     */
    public function actionDroptables()
    {
        try
        {
            if ( ! file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml'))
            {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/tree.xml' . '" not found.' . "\n\r");
            }

            if ( ! file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml'))
            {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/product.xml' . '" not found.' . "\n\r");
            }

            if ( ! file_exists(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml'))
            {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/filters.xml' . '" not found.' . "\n\r");
            }

            if ( ! file_exists(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml'))
            {
                throw new \Exception('New file "' . yii::$app->params['xmlUploadPath']['current'] . '/stock.xml' . '" not found.' . "\n\r");
            }

            yii::$app->db->createCommand()->delete('{{%product_print_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%print_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%product_filter_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%product_attachment_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%slave_product_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%product_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%filter_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%filter_type_tmp}}')->execute();
            yii::$app->db->createCommand()->delete('{{%catalogue_tmp}}')->execute();
        }
        catch (\Exception $e)
        {
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

        try
        {
            $treeXML = $loadXMLObject->get(yii::$app->params['gate']['tree'], $login, $password);
            if($treeXML === false)
            {
                throw new \Exception('File tree.xml was not processed.');
            }

            $treeXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml');
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }

        try
        {
            $productsXML = $loadXMLObject->get(yii::$app->params['gate']['product'], $login, $password);
            if($treeXML === false)
            {
                throw new \Exception('File product.xml was not processed.');
            }

            $productsXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/product.xml');
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }

        try
        {
            $filtersXML = $loadXMLObject->get(yii::$app->params['gate']['filters'], $login, $password);
            if($filtersXML === false)
            {
                throw new \Exception('File stock.xml was not processed.');
            }

            $filtersXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml');
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }

        try
        {
            $stockXML = $loadXMLObject->get(yii::$app->params['gate']['stock'], $login, $password);
            if($stockXML === false)
            {
                throw new \Exception('File stock.xml was not processed.');
            }

            $stockXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml');
        }
        catch (\Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
    }

    public function actionDownloadstock()
    {
        $loadXMLObject = LoadGiftsXML::getInstance();
        $login = mb_strrpos(yii::$app->basePath, 'daripodelu/console') !== false ? null : yii::$app->config->gateLogin;
        $password = mb_strrpos(yii::$app->basePath, 'daripodelu/console') !== false ? null : yii::$app->config->gatePassword;

        try
        {
            Yii::$app->updateGiftsDBLogger->info(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::ITEM_STOCK, 'Началось скачивание файла stock.xml с gifts.ru.');

            $stockXML = $loadXMLObject->get(yii::$app->params['gate']['stock'], $login, $password);
            if($stockXML === false)
            {
                throw new \Exception('File stock.xml was not processed.');
            }

            if ($stockXML->saveXML(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml'))
            {
                Yii::$app->updateGiftsDBLogger->success(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::ITEM_STOCK, 'Файл stock.xml загружен с gifts.ru и сохранен на сервере.');
            }
            else
            {
                Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::ITEM_STOCK, 'Файл stock.xml не удалось сохранить на сервере.');
            }
        }
        catch (\Exception $e)
        {
            Yii::$app->updateGiftsDBLogger->error(UpdateGiftsDBLog::ACTION_LOAD, UpdateGiftsDBLog::ITEM_STOCK, $e->getMessage());
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
        try
        {
            if (! file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml'))
            {
                throw new \Exception('File tree.xml not found.');
            }

            $treeXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml')
            );

            if($treeXML === false)
            {
                throw new \Exception('File tree.xml was not processed.');
            }

            //Формирование массива категорий
            yii::beginProfile('CatalogueFileAnalyze');
            $treeArr = $this->makeArrFromTree($treeXML);
            yii::endProfile('CatalogueFileAnalyze');

            $valuesArr = [];
            if (count($treeArr) > 0)
            {
                foreach ($treeArr as $row)
                {
                    $valuesArr[] = [
                        $row['id'],
                        $row['parent_id'],
                        $row['name'],
                        $row['uri'],
                        0,
                    ];
                }
            }

            if (count($valuesArr) > 0)
            {
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
                while($counter < $valuesArrLength);
                yii::endProfile('CatalogueInsertIntoDB');
            }
        }
        catch (\Exception $e)
        {
            yii::endProfile('CatalogueFileAnalyze');
            yii::endProfile('CatalogueInsertIntoDB');
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
        try
        {
            //Формирование массива категорий
            yii::beginProfile('ProductsPrepare');
            if (! file_exists(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml'))
            {
                throw new \Exception('File tree.xml not found. Products were not inserted in DB.');
            }

            $treeXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/tree.xml')
            );

            if($treeXML === false)
            {
                throw new \Exception('File tree.xml was not processed. Products were not inserted in DB.');
            }

            $treeArr = $this->makeArrFromTree($treeXML);

            $productCategoryPairs = [];
            if (count($treeArr) > 0)
            {
                foreach ($treeArr as $row)
                {
                    //Формирование массива из пар 'id товара' => 'id родительской категории'
                    if(isset($row['product']) && is_array($row['product']))
                    {
                        foreach ($row['product'] as $prod)
                        {
                            $productCategoryPairs[$prod['product_id']] = $prod['parent_id'];
                        }
                    }
                }
            }

            if (! file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml'))
            {
                throw new \Exception('File product.xml not found.');
            }

            $productsXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')
            );

            if($productsXML === false)
            {
                throw new \Exception('File product.xml was not processed.');
            }

            $stockArr = [];
            try
            {
                //Закгрузка файла stock.xml
                yii::beginProfile('StockFilePrepare');
                $stockXML = new \SimpleXMLElement(
                    file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')
                );
                yii::endProfile('StockFilePrepare');

                if($stockXML === false)
                {
                    throw new \Exception('File stock.xml was not processed.');
                }

                //Формирование массива с количеством товаров и их ценами
                yii::beginProfile('StockFileAnalyze');
                $stockArr = $this->makeArrFromStockTree($stockXML);
                yii::endProfile('StockFileAnalyze');
            }
            catch (Exception $e)
            {
                yii::endProfile('StockFilePrepare');
                yii::endProfile('StockFileAnalyze');
                echo $e->getMessage() . "\n";
            }

            //Парсирование xml файла с продуктами
            $valuesArr = [];
            foreach($productsXML->product as $key => $product)
            {
                $productId = (int)$product->product_id;
                $valuesArr[] = [
                    $productId,
                    (isset($productCategoryPairs[$productId]) ? $productCategoryPairs[$productId] : 1),
                    (isset($product->group) ? (int)$product->group : null),
                    (isset($product->code) ? (string)$product->code : ''),
                    (isset($product->name) ? (string)$product->name : ''),
                    (isset($product->product_size) ? (string)$product->product_size : ''),
                    (isset($product->matherial) ? (string)$product->matherial : ''),
                    (isset($product->small_image) ? (string)$product->small_image['src'] : ''),
                    (isset($product->big_image) ? (string)$product->big_image['src'] : ''),
                    (isset($product->super_big_image) ? (string)$product->super_big_image['src'] : ''),
                    (isset($product->content) ? (string)$product->content : ''),
                    (isset($product->status['id']) ? (string)$product->status['id'] : null),
                    (isset($product->status) ? (string)$product->status : ''),
                    (isset($product->brand) ? (string)$product->brand : ''),
                    (isset($product->weight) ? (float)$product->weight : 0.00),
                    (isset($product->pack->amount) ? (int)$product->pack->amount : null),
                    (isset($product->pack->weight) ? (float)$product->pack->weight : null),
                    (isset($product->pack->volume) ? (float)$product->pack->volume : null),
                    (isset($product->pack->sizex) ? (float)$product->pack->sizex : null),
                    (isset($product->pack->sizey) ? (float)$product->pack->sizey : null),
                    (isset($product->pack->sizez) ? (float)$product->pack->sizez : null),
                    (isset($stockArr[$productId]['amount']) ? $stockArr[$productId]['amount'] : 0),
                    (isset($stockArr[$productId]['free']) ? $stockArr[$productId]['free'] : 0),
                    (isset($stockArr[$productId]['inwayamount']) ? $stockArr[$productId]['inwayamount'] : 0),
                    (isset($stockArr[$productId]['inwayfree']) ? $stockArr[$productId]['inwayfree'] : 0),
                    (isset($stockArr[$productId]['enduserprice']) ? $stockArr[$productId]['enduserprice'] : 0.00),
                    0,
                ];
            }
            yii::endProfile('ProductsPrepare');

            //Запись информации о товарах в БД
            if (count($valuesArr) > 0)
            {
                yii::beginProfile('ProductsInsertIntoDB');
                $valuesArrTmp = [];
                $counter = 0;
                $valuesArrLength = count($valuesArr);
                do {
                    $valuesArrTmp = array_slice($valuesArr, $counter, $this->batchSize);
                    yii::$app->db->createCommand()->batchInsert(
                        '{{%product_tmp}}',
                        [
                            'id',
                            'catalogue_id',
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
                while($counter < $valuesArrLength);
                yii::endProfile('ProductsInsertIntoDB');
            }
        }
        catch (\Exception $e)
        {
            yii::endProfile('ProductsPrepare');
            yii::endProfile('ProductsInsertIntoDB');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertslaveprod()
    {
        try
        {
            yii::beginProfile('SlaveProductsPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The slave products were not inserted in DB.');
            }

            $productsXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')
            );

            if ($productsXML === false) {
                throw new \Exception('File product.xml was not processed. The slave products were not inserted in DB.');
            }

            $stockArr = [];
            try
            {
                //Закгрузка файла stock.xml
                yii::beginProfile('StockFilePrepare');
                $stockXML = new \SimpleXMLElement(
                    file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')
                );
                yii::endProfile('StockFilePrepare');

                if($stockXML === false)
                {
                    throw new \Exception('File stock.xml was not processed.');
                }

                //Формирование массива с количеством товаров и их ценами
                yii::beginProfile('StockFileAnalyze');
                $stockArr = $this->makeArrFromStockTree($stockXML);
                yii::endProfile('StockFileAnalyze');
            }
            catch (Exception $e)
            {
                yii::endProfile('StockFilePrepare');
                yii::endProfile('StockFileAnalyze');
                echo $e->getMessage() . "\n";
            }

            //Парсирование xml файла с продуктами
            $slaveProductsArr = [];
            foreach($productsXML->product as $key => $product)
            {
                $productId = (int)$product->product_id;

                //Формирование массива подчиненных товаров
                if (isset($product->product))
                {
                    if(count($product->product) > 1)  //Если у товара несколько подчиненных товаров
                    {
                        foreach ($product->product as $item)
                        {
                            $slaveProductId = (int) $item->product_id;
                            $slaveProductsArr[] = [
                                $slaveProductId,
                                $productId,
                                (isset($item->code)? (string) $item->code: ''),
                                (isset($item->name)? (string) $item->name: ''),
                                (isset($item->size_code)? (string) $item->size_code: ''),
                                (isset($item->weight)? (float) $item->weight: 0.00),
                                (isset($item->price->price)? (float) $item->price->price: 0.00),
                                (isset($item->price->currency)? (string) $item->price->currency: ''),
                                (isset($item->price->name)? (string) $item->price->name: ''),
                                (isset($stockArr[$slaveProductId]['amount']) ? $stockArr[$slaveProductId]['amount'] : 0),
                                (isset($stockArr[$slaveProductId]['free']) ? $stockArr[$slaveProductId]['free'] : 0),
                                (isset($stockArr[$slaveProductId]['inwayamount']) ? $stockArr[$slaveProductId]['inwayamount'] : 0),
                                (isset($stockArr[$slaveProductId]['inwayfree']) ? $stockArr[$slaveProductId]['inwayfree'] : 0),
                                (isset($stockArr[$slaveProductId]['enduserprice']) ? $stockArr[$slaveProductId]['enduserprice'] : 0.00),
                                0,
                            ];
                        }
                    }
                    else  //Если у товара один подчиненный товар
                    {
                        $slaveProductId = (int) $product->product->product_id;
                        $slaveProductsArr[] = [
                            $slaveProductId,
                            $productId,
                            (isset($product->product->code)? (string) $product->product->code: ''),
                            (isset($product->product->name)? (string) $product->product->name: ''),
                            (isset($product->product->size_code)? (string) $product->product->size_code: ''),
                            (isset($product->product->weight)? (float) $product->product->weight: 0.00),
                            (isset($product->product->price->price)? (float) $product->product->price->price: 0.00),
                            (isset($product->product->price->currency)? (string) $product->product->price->currency: ''),
                            (isset($product->product->price->name)? (string) $product->product->price->name: ''),
                            (isset($stockArr[$slaveProductId]['amount']) ? $stockArr[$slaveProductId]['amount'] : 0),
                            (isset($stockArr[$slaveProductId]['free']) ? $stockArr[$slaveProductId]['free'] : 0),
                            (isset($stockArr[$slaveProductId]['inwayamount']) ? $stockArr[$slaveProductId]['inwayamount'] : 0),
                            (isset($stockArr[$slaveProductId]['inwayfree']) ? $stockArr[$slaveProductId]['inwayfree'] : 0),
                            (isset($stockArr[$slaveProductId]['enduserprice']) ? $stockArr[$slaveProductId]['enduserprice'] : 0.00),
                            0,
                        ];
                    }
                }
            }
            yii::endProfile('SlaveProductsPrepare');

            //Запись информации о подчиненных товарах в БД
            if (count($slaveProductsArr) > 0)
            {
                yii::beginProfile('SlaveProductsInsertIntoDB');
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
                while($counter < $valuesArrLength);
                yii::endProfile('SlaveProductsInsertIntoDB');
            }
        }
        catch (\Exception $e)
        {
            yii::endProfile('SlaveProductsPrepare');
            yii::endProfile('SlaveProductsInsertIntoDB');
            echo $e->getMessage() . "\n";
        }

    }

    public function actionInsertattach()
    {
        try
        {
            yii::beginProfile('ProductAttachmentsPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The product attachments were not inserted in DB.');
            }

            $productsXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')
            );

            if ($productsXML === false) {
                throw new \Exception('File product.xml was not processed. The product attachments were not inserted in DB.');
            }

            //Парсирование xml файла с продуктами
            $prodAttachesArr = [];
            foreach($productsXML->product as $key => $product)
            {
                $productId = (int)$product->product_id;

                //Формирование массива с дополнительными файлами товаров
                if (isset($product->product_attachment))
                {
                    if(count($product->product_attachment) > 1)
                    {
                        foreach ($product->product_attachment as $item) //Если у товара несколько дополнительных файлов
                        {
                            $prodAttachesArr[] = [
                                $productId,
                                (int) $item->meaning,
                                (isset($item->file )? (string) $item->file : null),
                                (isset($item->image)? (string) $item->image: null),
                                (isset($item->name)? (string) $item->name: null),
                                0,
                            ];
                        }
                    }
                    else //Если у товара только один дополнительный файл
                    {
                        $prodAttachesArr[] = [
                            $productId,
                            (int) $product->product_attachment->meaning,
                            (isset($product->product_attachment->file )? (string) $product->product_attachment->file : null),
                            (isset($product->product_attachment->image)? (string) $product->product_attachment->image: null),
                            (isset($product->product_attachment->name)? (string) $product->product_attachment->name: null),
                            0,
                        ];
                    }
                }
            }
            yii::endProfile('ProductAttachmentsPrepare');

            //Запись информации о дополнительных файлах товара в БД
            if (count($prodAttachesArr) > 0)
            {
                yii::beginProfile('ProductAttachInsertIntoDB');
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
                while($counter < $valuesArrLength);
                yii::endProfile('ProductAttachInsertIntoDB');
            }
        }
        catch (\Exception $e)
        {
            yii::endProfile('ProductAttachmentsPrepare');
            yii::endProfile('ProductAttachInsertIntoDB');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertprint()
    {
        try
        {
            yii::beginProfile('ProductPrintsPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The product prints were not inserted in DB.');
            }

            $productsXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')
            );

            if ($productsXML === false) {
                throw new \Exception('File product.xml was not processed. The product prints were not inserted in DB.');
            }

            //Парсирование xml файла с продуктами
            $printsArr = [];
            $productPrintsArr = [];
            foreach($productsXML->product as $key => $product)
            {
                $productId = (int)$product->product_id;

                //Формирование массива с методами печати товаров и масива с парами "метод печати -  Id товара"
                if (isset($product->print))
                {
                    if (count($product->print) > 1) //Если у товара несколько методов печати
                    {
                        foreach ($product->print as $print)
                        {
                            $printId = (string) $print->name;
                            $printDescription = (string) $print->description;
                            if( ! array_key_exists($printId, $printsArr))
                            {
                                $printsArr[$printId] = [$printId, $printDescription, 0];
                            }
                            $productPrintsArr[] = [
                                $productId,
                                $printId,
                                0
                            ];
                        }
                    }
                    else //Если у товара только один метод печати
                    {
                        $printId = (string) $product->print->name;
                        $printDescription = (string) $product->print->description;
                        if( ! array_key_exists($printId, $printsArr))
                        {
                            $printsArr[$printId] = [$printId, $printDescription, 0];
                        }
                        $productPrintsArr[] = [
                            $productId,
                            $printId,
                            0
                        ];
                    }
                }
            }
            yii::endProfile('ProductPrintsPrepare');

            if (count($printsArr) > 0)
            {
                yii::beginProfile('PrintsInsertIntoDB');
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
                while($counter < $valuesArrLength);
                yii::endProfile('PrintsInsertIntoDB');

                if (count($productPrintsArr) > 0)
                {
                    yii::beginProfile('ProductPrintsInsertIntoDB');
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
                    while($counter < $valuesArrLength);
                    yii::endProfile('ProductPrintsInsertIntoDB');
                }
            }
        }
        catch (\Exception $e)
        {
            yii::endProfile('ProductPrintsPrepare');
            yii::endProfile('ProductPrintsInsertIntoDB');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertfilters()
    {
        //Формирование таблиц с типами фильтров и фильтрами
        try
        {
            yii::beginProfile('FiltersPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml')) {
                throw new \Exception('File filters.xml not found.');
            }

            $filtersXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/filters.xml')
            );

            if ($filtersXML === false) {
                throw new \Exception('File filters.xml was not processed.');
            }
            yii::endProfile('FiltersPrepare');

            //Формирование массива из типов фильтров
            yii::beginProfile('FiltersFileAnalyze');
            $filterTypesArr = $this->makeArrFromFilterTree($filtersXML);
            yii::endProfile('FiltersFileAnalyze');

            if (count($filterTypesArr) > 0)
            {
                $typesArrForInsert = [];
                $filtersArrForInsert = [];
                foreach ($filterTypesArr as $type)
                {
                    $typesArrForInsert[] = [$type['filtertypeid'], $type['filtertypename'], 0];
                    if (count($type['filters']) > 0)
                    {
                        foreach ($type['filters'] as $filter)
                        {
                            $filtersArrForInsert[] = [$filter['filterid'], $filter['filtername'], $type['filtertypeid'], 0];
                        }
                    }
                }

                if (count($typesArrForInsert) > 0)
                {
                    yii::beginProfile('FilterTypesInsertIntoDB');
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
                    while($counter < $valuesArrLength);
                    yii::endProfile('FilterTypesInsertIntoDB');
                }

                if (count($filtersArrForInsert) > 0)
                {
                    //Запись фильтров в БД
                    yii::beginProfile('FiltersInsertIntoDB');
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
                    while($counter < $valuesArrLength);
                    yii::endProfile('FiltersInsertIntoDB');
                }
            }
        }
        catch(\Exception $e)
        {
            yii::endProfile('FiltersFileAnalyze');
            yii::endProfile('FilterTypesInsertIntoDB');
            yii::endProfile('FiltersInsertIntoDB');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionInsertprodfilters()
    {
        try
        {
            yii::beginProfile('ProductFiltersPrepare');
            if ( !file_exists(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')) {
                throw new \Exception('File product.xml not found. The filters for product were not inserted in DB.');
            }

            $productsXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/product.xml')
            );

            if ($productsXML === false) {
                throw new \Exception('File product.xml was not processed. The filters for product were not inserted in DB.');
            }

            //Парсирование xml файла с продуктами
            $prodFiltersArr = [];
            foreach($productsXML->product as $key => $product)
            {
                $productId = (int)$product->product_id;

                //Формирование массива с фильтрами, которые можно применять к товару
                if (isset($product->filters->filter))
                {
                    if (count($product->filters->filter) > 1) //Если к товару можно применить только один фильтр
                    {
                        foreach ($product->filters->filter as $filter)
                        {
                            $prodFiltersArr[] = [
                                $productId,
                                (int) $filter->filterid,
                                (int) $filter->filtertypeid,
                                0,
                            ];
                        }
                    }
                    else //Если к товару можно применить несколько фильтров
                    {
                        $prodFiltersArr[] = [
                            $productId,
                            (int) $product->filters->filter->filterid,
                            (int) $product->filters->filter->filtertypeid,
                            0,
                        ];
                    }
                }
            }
            yii::endProfile('ProductFiltersPrepare');

            //Запись связей товар-фильтр в БД
            if (count($prodFiltersArr) > 0)
            {
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
                while($counter < $valuesArrLength);
                yii::endProfile('ProductFiltersInsertIntoDB');
            }
        }
        catch(\Exception $e)
        {
            yii::endProfile('ProductFiltersPrepare');
            yii::endProfile('ProductFiltersInsertIntoDB');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionMakeimglist()
    {
        try
        {
            $imagesForDownloadArr = [];
            $results = yii::$app->db->createCommand('
                SELECT [[id]] as `product_id`, [[small_image]] as `image` FROM {{%product_tmp}} WHERE `small_image` IS NOT NULL AND user_row <> 1
                UNION ALL SELECT [[id]] as `product_id`, [[big_image]] as `image` FROM {{%product_tmp}} WHERE `big_image` IS NOT NULL AND user_row <> 1
                UNION ALL SELECT [[id]] as `product_id`, [[super_big_image]] as `image` FROM {{%product_tmp}} WHERE `super_big_image` IS NOT NULL AND user_row <> 1
                UNION ALL SELECT [[product_id]] as `product_id`, [[image]] as `image` FROM {{%product_attachment_tmp}} WHERE `meaning` = 1 AND `image` IS NOT NULL AND user_row <> 1
            ')->queryAll();

            foreach ($results as $row)
            {
                if ( $row['image'] != '' && ! file_exists(yii::$app->params['uploadPath'] . '/' . $row['product_id'] . '/' . $row['image']))
                {
                    $imagesForDownloadArr[] = ['product_id' => $row['product_id'], 'image' => $row['image']];
                }
            }

            if (! file_exists(yii::$app->params['xmlUploadPath']['current']))
            {
                mkdir(yii::$app->params['xmlUploadPath']['current']);
            }

            $f = fopen(yii::$app->params['xmlUploadPath']['current'] . '/imagesforupload.txt' , 'w+');
            foreach ($imagesForDownloadArr as $row)
            {
                fwrite($f,$row['product_id'] . ';' . $row['image'] . "\n");
            }
            fclose($f);
        }
        catch (Exception $e)
        {
            echo $e->getMessage() . "\n";
        }
    }

    public function actionMakefileslist()
    {
        try
        {
            $filesForDownloadArr = [];
            $results = yii::$app->db->createCommand('
                SELECT [[product_id]] as `product_id`, [[file]] as `file` FROM {{%product_attachment_tmp}} WHERE `meaning` = 0 AND `file` IS NOT NULL AND user_row <> 1
            ')->queryAll();

            foreach ($results as $row)
            {
                if ( $row['file'] != '' && ! file_exists(yii::$app->params['uploadPath'] . '/' . $row['product_id'] . '/' . $row['file']))
                {
                    $filesForDownloadArr[] = ['product_id' => $row['product_id'], 'file' => $row['file']];
                }
            }

            if (! file_exists(yii::$app->params['xmlUploadPath']['current']))
            {
                mkdir(yii::$app->params['xmlUploadPath']['current']);
            }

            $f = fopen(yii::$app->params['xmlUploadPath']['current'] . '/filesforupload.txt' , 'w+');
            foreach ($filesForDownloadArr as $row)
            {
                fwrite($f,$row['product_id'] . ';' . $row['file'] . "\n");
            }
            fclose($f);
        }
        catch (Exception $e)
        {
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
