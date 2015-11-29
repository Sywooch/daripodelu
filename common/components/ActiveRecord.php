<?php

namespace common\components;

use \Yii;

class ActiveRecord extends \yii\db\ActiveRecord {

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public $date_first;
    public $date_last;

    /**
     * @return array user type names indexed by type IDs
     */
    static public function getStatusOptions()
    {
        return array(
            self::STATUS_INACTIVE => 'Не опубликовано',
            self::STATUS_ACTIVE => 'Опубликовано',
        );
    }

    static public function getStatusName($index)
    {
        $options = self::getStatusOptions();

        return $options[$index];
    }
}

?>
