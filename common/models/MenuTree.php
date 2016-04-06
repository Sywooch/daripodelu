<?php

namespace common\models;

use common\components\ActiveRecord;
use common\components\MenuQuery;
use creocoder\nestedsets\NestedSetsBehavior;
use yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

//TODO-cms Сдлать подгрузку доступных модулей, действий, категорий и элементов. Например, чтобы можно было выбрать модуль "Page" и конкретную статическую страницу
//TODO-cms Сделать возможность указывать псевдоним страницы из других модулей, например, из News, Page, Article  и т.д.
//TODO-cms Сделать возможным указывать для модулей (например, Новости, Фотогалерея, Каталог, и т.д.) название страницы, meta description, meta keywords

/**
 * This is the model class for table "{{%menu_tree}}".
 *
 * @property integer $id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $depth
 * @property integer $parent_id
 * @property string $name
 * @property string $alias
 * @property string $module_id
 * @property string $controller_id
 * @property string $action_id
 * @property string $ctg_id
 * @property string $item_id
 * @property integer $can_be_parent
 * @property integer $show_in_menu
 * @property integer $show_as_link
 * @property integer $status
 *
 * @method \yii\db\ActiveQuery children
 * @method \yii\db\ActiveQuery parents
 * @method \yii\db\ActiveQuery leaves
 */
class MenuTree extends ActiveRecord
{

    const ROOT_ID = 1;

    const PARENT_CAN_NOT_BE = 0;
    const PARENT_CAN_BE = 1;

    const HIDE_IN_MENU = 0;
    const SHOW_IN_MENU = 1;

    const SHOW_LINK_NO = 0;
    const SHOW_LINK_YES = 1;

    const CACHE_KEY_ROUTES = 'menu_routes';
    const  CACHE_KEY_NAV = 'menu_nav';

    /**
     * @var integer Id of previous node.
     */
    public $prev_id = 0;

    private $cache = null;

    /**
     * @param yii\caching\Cache $cache Used for caching routes and navigation menutree. Default is null.
     * @param array $config name-value pairs that will be used to initialize the object properties
     */
    public function __construct(yii\caching\Cache $cache = null, $config = [])
    {
        $this->cache = $cache;
        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu_tree}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'controller_id', 'action_id', 'parent_id'], 'required'],
            [['lft', 'rgt', 'depth', 'ctg_id', 'item_id', 'can_be_parent', 'show_in_menu', 'show_as_link', 'status'], 'integer'],
            [['lft', 'rgt', 'depth', 'ctg_id', 'prev_id'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['alias'], 'string', 'max' => 70],
            [['show_as_link'], 'default', 'value' => MenuTree::SHOW_LINK_YES],
            [['module_id', 'ctg_id', 'item_id'], 'default', 'value' => null],
            [
                ['lft', 'rgt', 'depth', 'ctg_id', 'name', 'module_id', 'controller_id', 'action_id', 'parent_id', 'ctg_id', 'prev_id', 'item_id', 'can_be_parent', 'show_in_menu', 'show_as_link', 'status'],
                'trim',
                'skipOnEmpty' => true,
            ],
            [
                ['alias'],
                'match',
                'pattern' => '/^[A-Za-z0-9]+([_\-]?[A-Za-z0-9]+)*$/',
                'message' => 'Разрешённые символы: заглавные и прописные буквы латинского алфавита, цифры, знаки "-", "_" (без кавычек). Псевдоним должен оканчиваться на букву или цифру.'
            ],
            [['module_id', 'controller_id', 'action_id'], 'string', 'max' => 40],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Родительский пункт меню'),
            'lft' => Yii::t('app', 'Lft'),
            'rgt' => Yii::t('app', 'Rgt'),
            'depth' => Yii::t('app', 'Уровень вложенности'),
            'name' => Yii::t('app', 'Название'),
            'alias' => Yii::t('app', 'Псевдоним'),
            'module_id' => Yii::t('app', 'Модуль'),
            'controller_id' => Yii::t('app', 'Контроллер'),
            'action_id' => Yii::t('app', 'Действие'),
            'ctg_id' => Yii::t('app', 'Категория'),
            'item_id' => Yii::t('app', 'ID объекта'),
            'can_be_parent' => Yii::t('app', 'Может иметь дочерние узлы'),
            'show_in_menu' => Yii::t('app', 'Может быть пунктом меню'),
            'show_as_link' => Yii::t('app', 'Отображать как ссылку'),
            'status' => Yii::t('app', 'Статус'),
            'prev_id' => 'Положение после',
        ];
    }

    /**
     * @return array
     */
    public static function getParentStatuses()
    {
        return array(
            static::PARENT_CAN_NOT_BE => Yii::t('app', 'No'),
            static::PARENT_CAN_BE => Yii::t('app', 'Yes'),
        );
    }

    public function getParentStatusName($index)
    {
        $options = static::getParentStatuses();

        return $options[$index];
    }

    /**
     * @return array
     */
    public static function getShowMenuStatuses()
    {
        return array(
            static::HIDE_IN_MENU => Yii::t('app', 'No'),
            static::SHOW_IN_MENU => Yii::t('app', 'Yes'),
        );
    }

    public function getShowMenuStatusName($index)
    {
        $options = $this->getShowMenuStatuses();

        return $options[$index];
    }

    /**
     * @param bool $selfEscape
     * @param bool $all If true, the resulting array consist both visible and hide menutree items. Default is false and returns only visible menutree items ( attribute 'status' equal true).
     * @return array|yii\db\ActiveRecord[] Array of common\models\MenuTree class instance
     */
    public function getTree($selfEscape = false, $all = false)
    {
        $escapeIds[] = -9999;

        if ($selfEscape === true)
        {
            $children = $this->children()->all();
            $escapeIds = ArrayHelper::getColumn($children, 'id');
            $escapeIds[] = $this->id;
        }

        if ($all)
        {
            $tree = $this->find()->where(['not in', 'id', $escapeIds])->orderBy('lft')->all();
        }
        else
        {
            $tree = $this->find()->where(['status' => MenuTree::STATUS_ACTIVE])->andWhere(['not in', 'id', $escapeIds])->orderBy('lft')->all();
        }

        return $tree;
    }

    /**
     * @param bool $selfEscape
     * @param bool $visible
     * @param bool $canBeParent
     * @return array
     */
    public function getTreeForDropDownList($selfEscape = false, $visible = true, $canBeParent = true)
    {
        $tree = $this->getTree($selfEscape, !$visible);
        $optionsArray = [];
        foreach ($tree as $item)
        {
            /* @var $item MenuTree */
            if ($canBeParent)
            {
                if ($item->can_be_parent == static::PARENT_CAN_BE)
                {
                    $optionsArray[$item->id] = str_repeat('- - ', $item->depth) . (($item->id !== 1) ? $item->name : Yii::t('app', 'No'));
                }
            }
            else
            {
                $optionsArray[$item->id] = str_repeat('- - ', $item->depth) . (($item->id !== 1) ? $item->name : Yii::t('app', 'No'));
            }
        }

        return $optionsArray;
    }

    public function getSiblingItems($selfEscape = true, $all = false)
    {
        $siblingItems = [];
        $modelId = $selfEscape === true ? $this->id : -9999;
        $parentItem = MenuTree::findOne(['id' => $this->parent_id]);
        if ($parentItem)
        {
            if (($all))
            {
                $siblingItems = $parentItem->children(1)->andWhere(['<>', 'id', $modelId])->all();
            }
            else
            {
                $siblingItems = $parentItem->children(1)->andWhere(['status' => MenuTree::STATUS_ACTIVE])->andWhere(['<>', 'id', $modelId])->all();
            }
        }

        return $siblingItems;
    }

    public static function makeRoutes(MenuTree $root = null, &$routesArr = [], $route = '')
    {
        if (is_null($root))
        {
            $root = MenuTree::findOne(['id' => MenuTree::ROOT_ID]);
        }

        $children = $root->children(1)->andWhere(['status' => MenuTree::STATUS_ACTIVE])->all();

        foreach ($children as $child)
        {
            $routesArr[$route . $child->alias] = ArrayHelper::toArray($child);
            if ( !$child->isLeaf())
            {
                MenuTree::makeRoutes($child, $routesArr, $route . $child->alias . '/');
            }
        }

        return $routesArr;
    }

    public function getRoutes()
    {
        if ( !is_null($this->cache))
        {
            $routes = $this->cache->get(MenuTree::CACHE_KEY_ROUTES);
            if ($routes === false)
            {
                $routes = MenuTree::makeRoutes();
                $this->cache->set(MenuTree::CACHE_KEY_ROUTES, $routes);
            }
        }
        else
        {
            $routes = MenuTree::makeRoutes();
        }

        return $routes;
    }

    private function createMenuItemsArray(MenuTree $root = null, $route = '/')
    {
        $menuItems = [];
        if (is_null($root))
        {
            $root = MenuTree::findOne(['id' => MenuTree::ROOT_ID]);
        }

        $children = $root->children(1)->andWhere(['status' => MenuTree::STATUS_ACTIVE])->andWhere(['show_in_menu' => MenuTree::SHOW_IN_MENU])->all();

        foreach ($children as $child)
        {
            /* @var $child MenuTree */
            if ($child->isLeaf() || $child->children(1)->andWhere(['show_in_menu' => MenuTree::SHOW_IN_MENU])->count() == 0)
            {
                $menuItems[] = ['label' => $child->name, 'url' => [$route . $child->alias]];
            }
            else
            {
                $items = $this->createMenuItemsArray($child, $route . $child->alias . '/');
                if ($child->show_as_link == MenuTree::SHOW_LINK_YES)
                {
                    $menuItems[] = ['label' => $child->name, 'url' => [$route . $child->alias], 'items' => $items];
                }
                else
                {
                    $menuItems[] = ['label' => $child->name, 'url' => ['#'], 'items' => $items];
                }
            }
        }

        return $menuItems;
    }

    public function getMenuItems()
    {
        if ( !is_null($this->cache))
        {
            $menuItems = $this->cache->get(MenuTree::CACHE_KEY_NAV);
            if ($menuItems === false)
            {
                $menuItems = $this->createMenuItemsArray();
                $this->cache->set(MenuTree::CACHE_KEY_NAV, $menuItems, Yii::$app->params['cacheNavExpire']);
            }
        }
        else
        {
            $menuItems = $this->createMenuItemsArray();
        }

        return $menuItems;
    }

    public function attachCache(yii\caching\Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Finds instance of the class
     *
     * @param  string $route for example, module/controller/action or controller/action
     * @param integer $itemId
     * @param integer $ctgId
     * @return array|null|yii\db\ActiveRecord
     */
    public static function findItemByRoute($route, $itemId = null, $ctgId = null)
    {
        $moduleId = null;
        $controllerId = null;
        $actionId = null;
        if (substr_count($route, '/') == 2)
        {
            list($moduleId, $controllerId, $actionId) = explode('/', $route);
        }
        elseif (substr_count($route, '/') == 1)
        {
            list($controllerId, $actionId) = explode('/', $route);
        }
        else
        {
            throw new yii\base\InvalidParamException('Invalid parameter "route" passed to a method ' . __METHOD__ . '. There is no controllerId and/or actionId.');
        }

        return MenuTree::find()->where(['module_id' => $moduleId, 'controller_id' => $controllerId, 'action_id' => $actionId, 'item_id' => $itemId, 'ctg_id' => $ctgId])->one();
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ( !is_null($this->cache))
        {
            $routes = MenuTree::makeRoutes();
            $this->cache->set(MenuTree::CACHE_KEY_ROUTES, $routes);

            $menuItems = $this->createMenuItemsArray();
            $this->cache->set(MenuTree::CACHE_KEY_NAV, $menuItems, Yii::$app->params['cacheNavExpire']);
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if ( !is_null($this->cache))
        {
            $routes = MenuTree::makeRoutes();
            $this->cache->set(MenuTree::CACHE_KEY_ROUTES, $routes);

            $menuItems = $this->createMenuItemsArray();
            $this->cache->set(MenuTree::CACHE_KEY_NAV, $menuItems, Yii::$app->params['cacheNavExpire']);
        }
    }

    public function behaviors()
    {
        return [
            'tree' => [
                'class' => NestedSetsBehavior::className(),
            ],
            'slug' => [
                'class' => 'Zelenin\yii\behaviors\Slug',
                'slugAttribute' => 'alias',
                'attribute' => 'name',
                // optional params
//                'ensureUnique' => true,
                'translit' => true,
                'replacement' => '-',
                'lowercase' => true,
                'immutable' => true,
                // If intl extension is enabled, see http://userguide.icu-project.org/transforms/general.
                'transliterateOptions' => 'Russian-Latin/BGN;'
            ]
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new MenuQuery(get_called_class());
    }
}
