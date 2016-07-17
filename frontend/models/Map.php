<?php

namespace frontend\models;

use common\models\Map as CommonMap;

/**
 * Class Map
 *
 * @package backend\models
 * @inheritdoc
 */
class Map extends CommonMap
{
    /**
     * Returns list of map controls
     *
     * @return array
     */
    public function getControls()
    {
        $controls = [];
        if ($this->geolocation_control == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_GEOLOCATION;
        }

        if ($this->search_control == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_SEARCH;
        }

        if ($this->route_editor == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_ROUTE;
        }

        if ($this->traffic_control == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_TRAFFIC;
        }

        if ($this->type_selector == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_TYPE;
        }

        if ($this->fullscreen_control == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_FULLSCREEN;
        }

        if ($this->zoom_control == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_ZOOM;
        }

        if ($this->ruler_control == static::CONTROL_ON) {
            $controls[] = static::CONTROL_YANDEX_RULER;
        }

        return $controls;
    }
}