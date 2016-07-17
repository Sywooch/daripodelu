<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%map}}".
 *
 * @property integer $id
 * @property string $vendor
 * @property string $name
 * @property string $module_id
 * @property string $controller_id
 * @property string $action_id
 * @property integer $ctg_id
 * @property integer $item_id
 * @property string $center_lat
 * @property string $center_lng
 * @property string $point_lat
 * @property string $point_lng
 * @property string $point_label
 * @property integer $zoom
 * @property string $type
 * @property integer $geolocation_control
 * @property integer $search_control
 * @property integer $route_editor
 * @property integer $traffic_control
 * @property integer $type_selector
 * @property integer $fullscreen_control
 * @property integer $zoom_control
 * @property integer $ruler_control
 * @property integer $status
 */
class Map extends \yii\db\ActiveRecord
{
    const VENDOR_GOOGLE = 'google';
    const VENDOR_YANDEX = 'yandex';

    /**
     * Yandex map type "Schema"
     */
    const TYPE_YANDEX_MAP = 'yandex#map';
    /**
     * Yandex map type "Satellite"
     */
    const TYPE_YANDEX_SATELLITE = 'yandex#satellite';
    /**
     * Yandex map type "Hybrid"
     */
    const TYPE_YANDEX_HYBRID = 'yandex#hybrid';
    /**
     * Google map type "Roadmap"
     */
    const TYPE_GOOGLE_ROADMAP = 'ROADMAP';
    /**
     * Google map type "Satellite"
     */
    const TYPE_GOOGLE_SATELLITE = 'SATELLITE';
    /**
     * Google map type "Hybrid"
     */
    const TYPE_GOOGLE_HYBRID = 'HYBRID';
    /**
     * Google map type "Terrain"
     */
    const TYPE_GOOGLE_TERRAIN = 'TERRAIN';

    const CONTROL_OFF = 0;
    const CONTROL_ON = 1;

    const CONTROL_YANDEX_GEOLOCATION = 'geolocationControl';
    const CONTROL_YANDEX_SEARCH = 'searchControl';
    const CONTROL_YANDEX_ROUTE = 'routeEditor';
    const CONTROL_YANDEX_TRAFFIC = 'trafficControl';
    const CONTROL_YANDEX_TYPE = 'typeSelector';
    const CONTROL_YANDEX_FULLSCREEN = 'fullscreenControl';
    const CONTROL_YANDEX_ZOOM = 'zoomControl';
    const CONTROL_YANDEX_RULER = 'rulerControl';

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%map}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vendor', 'name', 'controller_id', 'action_id', 'center_lat', 'center_lng', 'point_lat', 'point_lng', 'type'], 'required'],
            [['ctg_id', 'item_id', 'zoom', 'geolocation_control', 'search_control', 'route_editor', 'traffic_control', 'type_selector', 'fullscreen_control', 'zoom_control', 'ruler_control', 'status'], 'integer'],
            [['center_lat', 'center_lng', 'point_lat', 'point_lng'], 'number'],
            [['vendor'], 'string', 'max' => 30],
            [['name', 'point_label'], 'string', 'max' => 255],
            [['module_id', 'controller_id', 'action_id', 'type'], 'string', 'max' => 40],
            [['zoom',], 'default', 'value' => 10],
            [
                ['geolocation_control', 'search_control', 'route_editor', 'traffic_control', 'fullscreen_control'],
                'default',
                'value' => 0
            ],
            [['type_selector', 'zoom_control', 'ruler_control', 'status'], 'default', 'value' => 1],
            [['module_id', 'ctg_id', 'item_id', 'point_label'], 'default', 'value' => null],
            [
                ['module_id', 'controller_id', 'action_id', 'ctg_id', 'item_id'],
                'unique',
                'targetAttribute' => ['module_id', 'controller_id', 'action_id', 'ctg_id', 'item_id'],
                'message' => 'The combination of Модуль, Контроллер, Действие, Категория and Элемент has already been taken.'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'vendor' => Yii::t('app', 'Карта'),
            'name' => Yii::t('app', 'Название карты'),
            'module_id' => Yii::t('app', 'Модуль'),
            'controller_id' => Yii::t('app', 'Контроллер'),
            'action_id' => Yii::t('app', 'Действие'),
            'ctg_id' => Yii::t('app', 'Категория'),
            'item_id' => Yii::t('app', 'Элемент'),
            'center_lat' => Yii::t('app', 'Широта центра карты'),
            'center_lng' => Yii::t('app', 'Долгота центра карты'),
            'point_lat' => Yii::t('app', 'Широта объекта'),
            'point_lng' => Yii::t('app', 'Долгота объекта'),
            'point_label' => Yii::t('app', 'Текст метки'),
            'zoom' => Yii::t('app', 'Масштаб'),
            'type' => Yii::t('app', 'Тип карты'),
            'geolocation_control' => Yii::t('app', 'Кнопка "Геолокация"'),
            'search_control' => Yii::t('app', 'Поисковая строка'),
            'route_editor' => Yii::t('app', 'Кнопка "Построение маршрута"'),
            'traffic_control' => Yii::t('app', 'Кнопка "Пробки"'),
            'type_selector' => Yii::t('app', 'Кнопка "Выбор типа карты"'),
            'fullscreen_control' => Yii::t('app', 'Кнопка "Полноэкранный режим"'),
            'zoom_control' => Yii::t('app', 'Элемент управления "Изменение масштаба"'),
            'ruler_control' => Yii::t('app', 'Элемент управления "Измерерние расстояния"'),
            'status' => Yii::t('app', 'Статус'),
        ];
    }

    /**
     * Returns the list of statuses
     *
     * @return array
     */
    public static function getStatusOptions()
    {
        return [
            static::STATUS_INACTIVE => 'Не опубликовано',
            static::STATUS_ACTIVE => 'Опубликовано',
        ];
    }

    /**
     * Returns status name by Id
     *
     * @param $index the status Id
     * @return mixed
     * @throws InvalidParamException
     */
    public static function getStatusName($index)
    {
        $options = static::getStatusOptions();
        if ( !isset($options[$index])) {
            throw new InvalidParamException;
        }

        return $options[$index];
    }

    /**
     * Returns the list of the map types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            static::TYPE_YANDEX_MAP => 'Яндекс.Карты. Тип карты "схема"',
            static::TYPE_YANDEX_SATELLITE => 'Яндекс.Карты. Карта из спутниковых снимков',
            static::TYPE_YANDEX_HYBRID => 'Яндекс.Карты. Сочетание обычной и спутниковой карты',
            static::TYPE_GOOGLE_ROADMAP => 'Google Maps. Стандартное представление',
            static::TYPE_GOOGLE_SATELLITE => 'Google Maps. Карта из спутниковых снимков',
            static::TYPE_GOOGLE_HYBRID => 'Google Maps. Сочетание обычной и спутниковой карты',
            static::TYPE_GOOGLE_TERRAIN => 'Google Maps. Карта на основе данных о ландшафте',
        ];
    }

    /**
     * Returns name of the map type by Id
     *
     * @param $index the type Id
     * @return string
     * @throws InvalidParamException
     */
    public static function getTypeName($index)
    {
        $types = static::getTypes();
        if ( !isset($types[$index])) {
            throw new InvalidParamException;
        }

        return $types[$index];
    }

    /**
     * Returns the list of map vendors
     *
     * @return array
     */
    public function getVendors()
    {
        return [
            static::VENDOR_GOOGLE => 'Google Maps',
            static::VENDOR_YANDEX => 'Яндекс.Карты',
        ];
    }

    /**
     * Returns the name of map vendor by Id
     *
     * @param string $index vendor Id
     * @return string
     * @throws InvalidParamException
     */
    public static function getVendorName($index)
    {
        $vendors = static::getVendors();
        if ( !isset($vendors[$index])) {
            throw new InvalidParamException;
        }

        return $vendors[$index];
    }

    /**
     * Returns the map's info by controller and action
     *
     * @param string $controller name of controller
     * @param string $action name of action
     * @param integer $itemId item ID. Default null
     * @param integer $ctgId category ID. Default null
     * @param string $module name of module. Default null
     * @return null|Map
     */
    public static function findByController($controller, $action, $itemId = null, $ctgId = null, $module = null)
    {
        return static::find()->where(['module_id' => $module, 'controller_id' => $controller, 'action_id' => $action, 'item_id' => $itemId, 'ctg_id' => $ctgId])->one();
    }
}
