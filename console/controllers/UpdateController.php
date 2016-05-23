<?php

namespace console\controllers;

use yii;

class UpdateController extends \yii\console\Controller
{
    public function actionStock()
    {
        $stockArr = [];
        try
        {
            //Закгрузка файла stock.xml
            yii::beginProfile('update_StockFilePrepare');
            $stockXML = new \SimpleXMLElement(
                file_get_contents(yii::$app->params['xmlUploadPath']['current'] . '/stock.xml')
            );
            yii::endProfile('update_StockFilePrepare');

            if($stockXML === false)
            {
                throw new \Exception('File stock.xml was not processed.');
            }

            //Формирование массива с количеством товаров и их ценами
            yii::beginProfile('update_StockFileAnalyze');
            $stockArr = $this->makeArrFromStockTree($stockXML);
            yii::endProfile('update_StockFileAnalyze');

            if (count($stockArr) > 0)
            {
                $productResults = Yii::$app->db->createCommand('
                    SELECT [[id]], [[code]] FROM {{%product_tmp}}
                ')->queryAll();
                foreach ($productResults as $row)
                {
                    if (isset($stockArr[$row['id']]))
                    {
                        Yii::$app->db->createCommand()->update(
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
                foreach ($slaveProductResults as $row)
                {
                    if (isset($stockArr[$row['id']]))
                    {
                        Yii::$app->db->createCommand()->update(
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

                Yii::$app->db->createCommand('CALL gifts_update_stock()')->execute();
            }
        }
        catch (Exception $e)
        {
            yii::endProfile('update_StockFilePrepare');
            yii::endProfile('update_StockFileAnalyze');
            echo $e->getMessage() . "\n";
        }

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
}
