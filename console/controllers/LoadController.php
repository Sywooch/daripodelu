<?php

namespace console\controllers;

use yii;
use backend\models\Product;
use rkdev\loadgifts\LoadGiftsXML;

class LoadController extends \yii\console\Controller
{
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

    public function actionIndex()
    {
        //Создание объекта для загрузки файлов
        $loadXMLObject = LoadGiftsXML::getInstance();

        try
        {
            //Закгрузка файла tree.xml
            yii::beginProfile('CatalogueFileDownload');
            $treeXML = $loadXMLObject->get(yii::$app->params['gate']['tree']);
            yii::endProfile('CatalogueFileDownload');

            if($treeXML === false)
            {
                throw new \Exception('File tree.xml was not processed.');
            }

            //Формирование массива категорий
            yii::beginProfile('CatalogueFileAnalyze');
            $treeArr = $this->makeArrFromTree($treeXML);
            yii::endProfile('CatalogueFileAnalyze');

            if (count($treeArr) > 0)
            {
                $valuesArr = []; //массив для хранения строк при ставке в таблицу БД
                $productCategoryPairs = []; //массив из пар 'id товара' => 'id родительской категории'
                foreach($treeArr as $row)
                {
                    $valuesArr[] = [
                        $row['id'],
                        $row['parent_id'],
                        $row['name'],
                        $row['uri'],
                    ];

                    //Формирование массива из пар 'id товара' => 'id родительской категории'
                    if(isset($row['product']) && is_array($row['product']))
                    {
                        foreach ($row['product'] as $prod)
                        {
                            $productCategoryPairs[$prod['product_id']] = $prod['parent_id'];
                        }
                    }
                }

                if (count($valuesArr) > 0)
                {
                    //Запись категорий в соответствующую таблицу БД
                    yii::beginProfile('CatalogueInserIntoDB');
                    yii::$app->db->createCommand()->delete('{{%product}}')->execute();
                    yii::$app->db->createCommand()->delete('{{%catalogue}}')->execute();
                    yii::$app->db->createCommand()->batchInsert('{{%catalogue}}',['id', 'parent_id', 'name', 'uri'], $valuesArr)->execute();
                    yii::endProfile('CatalogueInserIntoDB');

                    //Формирование таблицы с товарами в БД
                    try
                    {
                        //Закгрузка файла product.xml
                        yii::beginProfile('ProductsFileDownload');
                        $productsXML = $loadXMLObject->get(yii::$app->params['gate']['product']);
                        yii::endProfile('ProductsFileDownload');

                        yii::beginProfile('ProductsFileAnalyze');
                        $valuesArr = [];
                        $valuesRow = [];
                        $iterationCounter = 0;
                        foreach($productsXML->product as $key => $product)
                        {
                            $productId = (int) $product->product_id;
                            $valuesRow = [
                                $productId,
                                0,
                                (isset($productCategoryPairs[$productId])? $productCategoryPairs[$productId]: 1),
                                (isset($product->group)? (int) $product->group: null),
                                (isset($product->code)? (string) $product->code: ''),
                                (isset($product->name)? (string) $product->name: ''),
                                (isset($product->product_size)? (string) $product->product_size: ''),
                                (isset($product->matherial)? (string) $product->matherial: ''),
                                (isset($product->small_image)? (string) $product->small_image['src']: ''),
                                (isset($product->big_image)? (string) $product->big_image['src']: ''),
                                (isset($product->super_big_image)? (string) $product->super_big_image['src']: ''),
                                (isset($product->content)? (string) $product->content: ''),
                                (isset($product->status['id'])? (string) $product->status['id']: null),
                                (isset($product->status)? (string) $product->status: ''),
                                (isset($product->brand)? (string) $product->brand: ''),
                                (isset($product->weight)? (float) $product->weight: 0.00),
                                (isset($product->pack->amount)? (int) $product->pack->amount: null),
                                (isset($product->pack->weight)? (float) $product->pack->weight: null),
                                (isset($product->pack->volume)? (float) $product->pack->volume: null),
                                (isset($product->pack->sizex)? (float) $product->pack->sizex: null),
                                (isset($product->pack->sizey)? (float) $product->pack->sizey: null),
                                (isset($product->pack->sizez)? (float) $product->pack->sizez: null),
                                0,
                                0,
                                0,
                                0,
                                0.00,
                                0,
                            ];
                            $valuesArr[] = $valuesRow;

//                            $iterationCounter++;

//                            if($iterationCounter > 3) break;
                        }
                        yii::endProfile('ProductsFileAnalyze');

                        if (count($valuesArr) > 0)
                        {
                            yii::beginProfile('ProductsInserIntoDB');
                            yii::$app->db->createCommand()->batchInsert(
                                '{{%product}}',
                                [
                                    'id',
                                    'main_product_id',
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
                                $valuesArr
                            )->execute();
                            yii::endProfile('ProductsInserIntoDB');
                        }

                    }
                    catch(\Exception $e)
                    {
                        yii::endProfile('ProductsInserIntoDB');
                        yii::endProfile('ProductsFileDownload');
                        echo $e->getMessage() . "\n";
                    }
                }
            }
        }
        catch(\Exception $e)
        {
            yii::endProfile('CatalogueFileDownload');
            yii::endProfile('CatalogueFileAnalyze');
            yii::endProfile('CatalogueInserIntoDB');

            echo $e->getMessage() . "\n";
        }

        echo "Index action\n";
    }

    public function actionGetimages()
    {
        echo "GateImages action\n";
    }
}
