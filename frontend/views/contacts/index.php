<?php

use yii\helpers\Html;
use rkdev\yandexmaps\Map as YandexMap;
use rkdev\yandexmaps\Canvas as YandexCanvas;
use rkdev\yandexmaps\objects\Placemark as YandexPlacemark;
use frontend\models\ContactsItem;

/* @var $this yii\web\View */
/* @var $heading string */
/* @var $items frontend\models\ContactsItem[] */
/* @var $map frontend\models\Map*/

$this->params['breadcrumbs'][] = $heading;
?>
<div class="col-10">
    <main class="main-content">
        <h1><?= Html::encode($heading); ?></h1>
        <div class="row" itemscope="" itemtype="http://schema.org/Organization">
            <div class="col-5">
            <?php if (count($items)): ?>
                <?php foreach ($items as $item): ?>
                    <?php if ($item->type == ContactsItem::TYPE_EMAIL): ?>
                    <div class="contacts-item row">
                        <div class="col-3 contacts-item_title"><?= Html::encode($item->name); ?></div>
                        <div class="col-7 contacts-item_value"><a class="email" href="mailto:<?= $item->value; ?>"><span itemprop="email"><?= $item->value; ?></span></a></div>
                    </div>
                    <?php elseif ($item->type == ContactsItem::TYPE_PHONE): ?>
                    <div class="contacts-item row">
                        <div class="col-3 contacts-item_title"><?= Html::encode($item->name); ?></div>
                        <div class="col-7 contacts-item_value"><a class="phone" href="tel:<?= $item->value; ?>"><span itemprop="telephone"><?= $item->value; ?></span></a></div>
                    </div>
                    <?php elseif ($item->type == ContactsItem::TYPE_FAX): ?>
                    <div class="contacts-item row">
                        <div class="col-3 contacts-item_title"><?= Html::encode($item->name); ?></div>
                        <div class="col-7 contacts-item_value" itemprop="faxNumber"><?= $item->value; ?></div>
                    </div>
                    <?php elseif ($item->type == ContactsItem::TYPE_ADDRESS): ?>
                    <div class="contacts-item row">
                        <div class="col-3 contacts-item_title"><?= Html::encode($item->name); ?></div>
                        <div class="col-7 contacts-item_value" itemprop="address"><?= $item->value; ?></div>
                    </div>
                    <?php else: ?>
                    <div class="contacts-item row">
                        <div class="col-3 contacts-item_title"><?= Html::encode($item->name); ?></div>
                        <div class="col-7 contacts-item_value"><?= $item->value; ?></div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            </div>
            <div class="col-5">
                <?php
                if (! is_null($map)):
                    $placemark = new YandexPlacemark([$map->point_lat, $map->point_lng],
                        [],
                        []
                    );

                    $ymap = new YandexMap(
                        'yandex_map',
                        [
                            'center' => [$map->center_lat, $map->center_lng],
                            'zoom' => $map->zoom,
                            'type' => $map->type,
                            'controls' => $map->getControls(),
                        ],
                        [
                            'objects' =>['myPlacemark' => $placemark],
                        ]
                    );
                    ?>
                    <?= YandexCanvas::widget([
                        'htmlOptions' => [
                            'style' => 'height: 300px;',
                        ],
                        'map' => $ymap,
                    ]); ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
