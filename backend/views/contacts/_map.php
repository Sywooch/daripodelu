<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use rkdev\yandexmaps\Map as YandexMap;
use rkdev\yandexmaps\Canvas as YandexCanvas;
use rkdev\yandexmaps\objects\Placemark as YandexPlacemark;
use backend\models\Map;

/* @var $this yii\web\View */
/* @var $mapModel backend\models\Map */

if ($mapModel->isNewRecord) {
    $mapModel->center_lat = 55.7372;
    $mapModel->center_lng = 37.6066;
    $mapModel->point_lat = 55.7372;
    $mapModel->point_lng = 37.6066;
    $mapModel->zoom = 10;
}

$placemark = new YandexPlacemark(
    [$mapModel->point_lat, $mapModel->point_lng],
    [],
    [
        'events' => [
            'dragend' => new JsExpression("function(e){
                var coords = e.get('target').geometry.getCoordinates();

                $('#pointLatField').val(coords[0]);
                $('#pointLngField').val(coords[1]);
            }"),
        ],
        'preset' => 'islands#violetDotIconWithCaption',
        'draggable' => true,
    ]
);

$map = new YandexMap(
    'yandex_map',
    [
        'center' => [$mapModel->center_lat, $mapModel->center_lng],
        'zoom' => $mapModel->zoom,
        'type' => $mapModel->type,
        'controls' => ['zoomControl', 'searchControl', 'typeSelector',  'fullscreenControl', 'geolocationControl'],
    ],
    [
        'objects' =>['myPlacemark' => $placemark],
        'events' => [
            'click' => new JsExpression("function(e){
                var coords = e.get('coords'),
                    myPlacemark = ymaps.geoQuery(\$Maps['yandex_map'].geoObjects).get(0);

                $('#pointLatField').val(coords[0]);
                $('#pointLngField').val(coords[1]);

                // Если метка уже создана – просто передвигаем ее.
                if (myPlacemark) {
                    myPlacemark.geometry.setCoordinates(coords);
                }
            }"),
            'actiontick' => new JsExpression("function(e){
                var centerCoords = \$Maps['yandex_map'].getCenter();

                \$('#centerLatField').val(centerCoords[0]);
                \$('#centerLngField').val(centerCoords[1]);
            }"),
            'boundschange' => new JsExpression("function(e){
                if (e.get('newZoom') != e.get('oldZoom')) {
                    \$('#zoomField').val(e.get('newZoom'));
                }
            }"),
            'typechange' => new JsExpression("function(e){
                \$('#typeField').val(e.get('target').getType());
            }"),
        ],
    ]
);

?>
<?= YandexCanvas::widget([
    'htmlOptions' => [
        'style' => 'height: 400px;',
    ],
    'map' => $map,
]); ?>

<?php $mapForm = ActiveForm::begin(['action' => Url::to(['index', 'tabIndex' => 1])]); ?>

    <?= $mapForm->field($mapModel, 'center_lat')->hiddenInput(['id' => 'centerLatField',])->label(false); ?>

    <?= $mapForm->field($mapModel, 'center_lng')->hiddenInput(['id' => 'centerLngField',])->label(false); ?>

    <?= $mapForm->field($mapModel, 'point_lat')->hiddenInput(['id' => 'pointLatField',])->label(false); ?>

    <?= $mapForm->field($mapModel, 'point_lng')->hiddenInput(['id' => 'pointLngField',])->label(false); ?>

    <?= $mapForm->field($mapModel, 'zoom')->hiddenInput(['id' => 'zoomField',])->label(false); ?>

    <?= $mapForm->field($mapModel, 'type')->hiddenInput(['id' => 'typeField',])->label(false); ?>

    <div class="panel panel-default">
        <div class="panel-heading">Элементы управления картой</div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <?= $mapForm->field($mapModel, 'zoom_control')->checkbox(); ?>

                    <?= $mapForm->field($mapModel, 'type_selector')->checkbox(); ?>

                    <?= $mapForm->field($mapModel, 'ruler_control')->checkbox(); ?>

                    <?= $mapForm->field($mapModel, 'search_control')->checkbox(); ?>
                </div>
                <div class="col-md-6">
                    <?= $mapForm->field($mapModel, 'route_editor')->checkbox(); ?>

                    <?= $mapForm->field($mapModel, 'geolocation_control')->checkbox(); ?>

                    <?= $mapForm->field($mapModel, 'traffic_control')->checkbox(); ?>

                    <?= $mapForm->field($mapModel, 'fullscreen_control')->checkbox(); ?>
                </div>
            </div>
        </div>
    </div>

    <?= $mapForm->field($mapModel, 'status')
        ->dropDownList(Map::getStatusOptions(), ['style' =>'max-width: 400px;'])
        ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Only map with status "Published" is displayed on the site.'));
    ?>

    <div class="form-group btn-ctrl">
        <?= Html::submitButton(
            Yii::t('app', 'Save'),
            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveMap']
        ); ?>
    </div>

<?php ActiveForm::end();