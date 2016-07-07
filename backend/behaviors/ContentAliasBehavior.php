<?php
namespace backend\behaviors;


use Yii;
use yii\base\Behavior;
use common\components\ActiveRecord;
use common\models\MenuTree;

/**
 * ContentAliasBehavior is the class which gives to class-owner the ability to generate and store aliases
 *
 * @property string $moduleId name of module
 * @property string $controllerId name of controller
 * @property string $actionId name of action
 * @property string $ctgIdAttribute name of model attribute in which contains id of the category
 * @property string $itemIdAttribute name of model attribute in which contains id of the item
 * @property string $alias
 * @property int $canBeParent
 * @property int $showInMenu
 * @property int $showAsLink
 * @property int $status
 * @package backend\behaviors
 */
class ContentAliasBehavior extends Behavior
{

    private $alias = '';
    private $actionId;
    private $canBeParent = MenuTree::PARENT_CAN_NOT_BE;
    private $controllerId;
    private $ctgIdAttribute = 'ctg_id';
    private $itemIdAttribute = 'id';
    private $itemNameAttribute = 'name';
    private $model = null;
    private $moduleId = null;
    private $parentMenuItemtId = 1;
    private $showInMenu = MenuTree::HIDE_IN_MENU;
    private $showAsLink = MenuTree::SHOW_LINK_YES;
    private $status = MenuTree::STATUS_ACTIVE;

    /**
     * @return \common\models\MenuTree|null
     */
    public function getAliasModel()
    {
        if ($this->model === null) {
            $route = $this->moduleId === null ? $this->controllerId . '/' . $this->actionId : $this->moduleId . '/' . $this->controllerId . '/' . $this->actionId;
            if ($model = MenuTree::findItemByRoute($route, $this->owner->{$this->itemIdAttribute}, $this->getCtgId())) {
                $this->model = $model;
            }
        }

        return $this->model;
    }

    public function getTreeForDropDownList($fullTree = false)
    {
        if ($fullTree === true) {
            $tree = MenuTree::find()->where(['status' => MenuTree::STATUS_ACTIVE])->orderBy('lft')->all();
        } else {
            $tree = MenuTree::find()->where(['status' => MenuTree::STATUS_ACTIVE])->andWhere(['item_id' => null])->andWhere(['like', 'controller_id', $this->controllerId])->orderBy('lft')->all();
        }
        $optionsArray = [];
        foreach ($tree as $item) {
            $optionsArray[$item->id] = str_repeat('- - ', $item->depth) . $item->name;
        }

        return $optionsArray;
    }

    public function getShowMenuStatuses()
    {
        return MenuTree::getShowMenuStatuses();
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
     * @return null
     */
    public function getCtgIdAttribute()
    {
        return $this->ctgIdAttribute;
    }

    /**
     * @param null $ctgIdAttribute
     */
    public function setCtgIdAttribute($ctgIdAttribute)
    {
        $this->ctgIdAttribute = $ctgIdAttribute;
    }

    /**
     * @return mixed
     */
    public function getItemIdAttribute()
    {
        return $this->itemIdAttribute;
    }

    /**
     * @param mixed $itemIdAttribute
     */
    public function setItemIdAttribute($itemIdAttribute)
    {
        $this->itemIdAttribute = $itemIdAttribute;
    }

    public function getCtgId()
    {
        return ( !isset($this->owner->{$this->ctgIdAttribute}) || is_null($this->owner->{$this->ctgIdAttribute})) ? null : $this->owner->{$this->ctgIdAttribute};
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return int
     */
    public function getParentMenuItemtId()
    {
        return $this->parentMenuItemtId;
    }

    /**
     * @param int $parentMenuItemtId
     */
    public function setParentMenuItemtId($parentMenuItemtId)
    {
        $this->parentMenuItemtId = $parentMenuItemtId;
    }

    /**
     * @param string $alias
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;
    }

    /**
     * @return int
     */
    public function getCanBeParent()
    {
        return $this->canBeParent;
    }

    /**
     * @param int $canBeParent
     */
    public function setCanBeParent($canBeParent)
    {
        $this->canBeParent = $canBeParent;
    }

    /**
     * @return int
     */
    public function getShowInMenu()
    {
        return $this->showInMenu;
    }

    /**
     * @param int $showInMenu
     */
    public function setShowInMenu($showInMenu)
    {
        $this->showInMenu = $showInMenu;
    }

    /**
     * @return int
     */
    public function getShowAsLink()
    {
        return $this->showAsLink;
    }

    /**
     * @param int $showAsLink
     */
    public function setShowAsLink($showAsLink)
    {
        $this->showAsLink = $showAsLink;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getItemNameAttribute()
    {
        return $this->itemNameAttribute;
    }

    /**
     * @param string $itemNameAttribute
     */
    public function setItemNameAttribute($itemNameAttribute)
    {
        $this->itemNameAttribute = $itemNameAttribute;
    }

    public function saveAlias()
    {
        $model = $this->getAliasModel();
        if ($model === null) {
            $model = new MenuTree(Yii::$app->cache);


            $model->parent_id = $this->parentMenuItemtId;
            $model->name = $this->owner->{$this->itemNameAttribute};
            $model->alias = $this->alias;
            $model->module_id = $this->moduleId;
            $model->controller_id = $this->controllerId;
            $model->action_id = $this->actionId;
            $model->ctg_id = $this->getCtgId();
            $model->item_id = $this->owner->{$this->itemIdAttribute};
            $model->can_be_parent = $this->canBeParent;
            $model->show_in_menu = $this->showInMenu;
            $model->show_as_link = $this->showAsLink;
            $model->status = $this->status;

            $parent = $model->findOne(['id' => $model->parent_id]);
            $saveResult = $model->prependTo($parent);
        } else {
            $model->attachCache(Yii::$app->cache);
            $model->alias = $this->alias;
            $model->show_in_menu = $this->showInMenu;
            $saveResult = $model->save(true, ['alias', 'show_in_menu']);
        }

        return $saveResult;
    }

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_DELETE => function ($event) {
                $model = $this->getAliasModel();

                if ($model !== null) {
                    $model->attachCache(Yii::$app->cache);
                    $model->delete();
                }
            },
        ];
    }
}