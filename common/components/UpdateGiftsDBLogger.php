<?php

namespace common\components;

use yii;
use common\models\UpdateGiftsDBLog;

class UpdateGiftsDBLogger extends yii\base\Component
{
    public function init()
    {
        parent::init();
    }

    public function info($action, $item, $message = '')
    {
        $this->log(UpdateGiftsDBLog::STATUS_INFO, $action, $item, $message, null);
    }

    public function success($action, $item, $message = '', $item_id = null)
    {
        $this->log(UpdateGiftsDBLog::STATUS_SUCCESS, $action, $item, $message, $item_id);
    }

    public function warning($action, $item, $message = '', $item_id = null)
    {
        $this->log(UpdateGiftsDBLog::STATUS_WARNING, $action, $item, $message, $item_id);
    }

    public function error($action, $item, $message = '', $item_id = null)
    {
        $this->log(UpdateGiftsDBLog::STATUS_ERROR, $action, $item, $message, $item_id);
    }

    public function log($status, $action, $item, $message = '', $item_id = null)
    {
        $loggerModel = new UpdateGiftsDBLog();
        $loggerModel->status = $status;
        $loggerModel->action = $action;
        $loggerModel->item = $item;
        $loggerModel->item_id = $item_id;
        $loggerModel->message = $message;

        return $loggerModel->save();
    }
}