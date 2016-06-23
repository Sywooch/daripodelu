<?php
ini_set('max_execution_time', 600);
ini_set('memory_limit', '512M');
/**
 * Yii bootstrap file.
 * Used for enhanced IDE code autocompletion.
 */
class
Yii extends \yii\BaseYii
{
    /**
     * @var BaseApplication|WebApplication|ConsoleApplication the application instance
     */
    public static $app;
}

spl_autoload_register(['Yii', 'autoload'], true, true);
Yii::$classMap = include(__DIR__ . '/vendor/yiisoft/yii2/classes.php');
Yii::$container = new yii\di\Container;

/**
 * Class BaseApplication
 * Used for properties that are identical for both WebApplication and ConsoleApplication
 *
 * @property \frontend\components\cart\ShopCart $cart
 * @property \common\components\Config $config
 * @property \common\components\UpdateGiftsDBLogger $updateGiftsDBLogger
 * @property \rkdev\yii2imagecache\ImageCache $imageCache
 * @property \demi\backup\Component $backup
 */
abstract class BaseApplication extends yii\base\Application
{
}

/**
 * Class WebApplication
 * Include only Web application related components here
 *
 * @property \app\components\User $user The user component. This property is read-only. Extended component.
 * @property \app\components\ErrorHandler $errorHandler The error handler application component. This property is read-only. Extended component.
 */
class WebApplication extends yii\web\Application
{
}

/**
 * Class ConsoleApplication
 * Include only Console application related components here
 *
 * @property \app\components\ConsoleUser $user The user component. This property is read-only. Extended component.
 */
class ConsoleApplication extends yii\console\Application
{
}