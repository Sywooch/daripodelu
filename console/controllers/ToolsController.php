<?php

namespace console\controllers;

use Yii;

class ToolsController extends \yii\console\Controller
{
    public function actionCreatethumbs()
    {
        try {
            yii::beginProfile('CreateThumbs');
            $results = yii::$app->db->createCommand('
                SELECT [[id]] as `product_id`, [[small_image]] as `image` FROM {{%product}} WHERE `small_image` IS NOT NULL
            ')->queryAll();

            foreach ($results as $row) {
                $relSrcPath = $row['product_id'] . '/' . $row['image'];
                $srcPath = yii::$app->params['uploadPath'] . '/' . $relSrcPath;
                if ($row['image'] != '' && file_exists($srcPath) && !file_exists(Yii::$app->imageCache->getThumbFullPath($relSrcPath, '36'))) {
                    Yii::$app->imageCache->thumbConsole($relSrcPath, '36');
                }
            }
            yii::endProfile('CreateThumbs');
        }
        catch (Exception $e) {
            yii::endProfile('CreateThumbs');
            echo $e->getMessage() . "\n";
        }
    }

    public function actionCreateDbBackup()
    {
        $backup = Yii::$app->backup;
        $file = $backup->create();

        $this->stdout('Backup file created: ' . $file . PHP_EOL, \yii\helpers\Console::FG_GREEN);
    }
}
