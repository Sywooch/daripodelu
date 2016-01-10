<?php

namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use common\models\SEOInformation;


class SEOBehavior
{

    private $model = null;
    private $moduleId = null;
    private $controllerId;
    private $actionId;
    private $ctgIdAttribute = 'ctg_id';
    private $itemIdAttribute = 'id';

    /**
     * @return null
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param null $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return null
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }

    /**
     * @param null $moduleId
     */
    public function setModuleId($moduleId)
    {
        $this->moduleId = $moduleId;
    }

    /**
     * @return mixed
     */
    public function getControllerId()
    {
        return $this->controllerId;
    }

    /**
     * @param mixed $controllerId
     */
    public function setControllerId($controllerId)
    {
        $this->controllerId = $controllerId;
    }

    /**
     * @return mixed
     */
    public function getActionId()
    {
        return $this->actionId;
    }

    /**
     * @param mixed $actionId
     */
    public function setActionId($actionId)
    {
        $this->actionId = $actionId;
    }

    /**
     * @return string
     */
    public function getCtgIdAttribute()
    {
        return $this->ctgIdAttribute;
    }

    /**
     * @param string $ctgIdAttribute
     */
    public function setCtgIdAttribute($ctgIdAttribute)
    {
        $this->ctgIdAttribute = $ctgIdAttribute;
    }

    /**
     * @return string
     */
    public function getItemIdAttribute()
    {
        return $this->itemIdAttribute;
    }

    /**
     * @param string $itemIdAttribute
     */
    public function setItemIdAttribute($itemIdAttribute)
    {
        $this->itemIdAttribute = $itemIdAttribute;
    }


}