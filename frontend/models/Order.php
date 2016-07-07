<?php

namespace frontend\models;

use Yii;
use common\models\Order as OrderCommon;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property string $fio
 * @property string $phone
 * @property integer $email
 * @property string $data
 * @property string $order_date
 * @property integer $status
 */
class Order extends OrderCommon
{
}
