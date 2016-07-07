<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use rkdev\yandexmaps\Map as YandexMap;
use rkdev\yandexmaps\Canvas as YandexCanvas;

/* @var $this yii\web\View */

$map = new YandexMap(
    'yandex_map',
    [
        'center' => [55.7372, 37.6066],
        'zoom' => 10,
    ],
    [
        'searchControlProvider' => 'yandex#search',
        'events' => [
            'click' => "function(e){
                var coords = e.get('coords');

                // Если метка уже создана – просто передвигаем ее.
                if (myPlacemark) {
                    myPlacemark.geometry.setCoordinates(coords);
                }
                // Если нет – создаем.
                else {
                    myPlacemark = new ymaps.Placemark(coords, {}, {
                        preset: 'islands#violetDotIconWithCaption',
                        draggable: true
                    });;
                    \$Maps['yandex_map'].geoObjects.add(myPlacemark);
                }
            }",
            'actiontick' => "function(e){
                var centerCoords = \$Maps['yandex_map'].getCenter();
            }",
        ],
    ]
);
?>
<?php $this->registerJs(" var myPlacemark; ", View::POS_READY); ?>
<?= YandexCanvas::widget([
    'htmlOptions' => [
        'style' => 'height: 400px;',
    ],
    'map' => $map,
]); ?>