<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use backend\models\Order;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */
/* @var $form yii\widgets\ActiveForm */
/* @var $files array */

$totalPrice = 0
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#order" aria-controls="order" role="tab" data-toggle="tab">Заказ</a></li>
            <li role="presentation"><a href="#client" aria-controls="client" role="tab" data-toggle="tab">Клиент</a></li>
        </ul>
        <div class="tab-content cms">
            <div role="tabpanel" id="order" class="tab-pane active">
                <dl class="order-info">
                    <dt>Номер заказа</dt>
                    <dd><?= $model->id; ?></dd>
                    <dt>Дата и время заказа</dt>
                    <dd><?= date('d.m.Y', strtotime($model->order_date)); ?>&nbsp;&nbsp;<?= date('H:i:s', strtotime($model->order_date)); ?></dd>
                    <dt>Статус заказа</dt>
                    <dd>
                        <?= $form->field($model, 'status')
                            ->dropDownList(Order::getStatusOptions(), ['style' => 'max-width: 200px;'])
                            ->label(false);
                        ?>
                    </dd>
                    <dt>Содержимое заказа</dt>
                    <dd>
                        <table class="table-bordered shop-cart">
<!--                        <table border="1">-->
                            <thead>
                            <tr>
                                <th></th>
                                <th>Наименование</th>
                                <th>Цена за штуку</th>
                                <th>Размер</th>
                                <th>Количество</th>
                                <th class="price">Общая сумма</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($model->dataArr as $key => $item): ?>
                                <tr>
                                    <td class="box"<?php if (count($item['size']) > 1): ?> rowspan="<?= count($item['size']); ?>"<?php endif; ?>>
                                        <div class="img-box"><img src="<?= $item['image'] ?>" alt="<?= $item['name'] ?>"></div>
                                    </td>
                                    <td class="name"<?php if (count($item['size']) > 1): ?> rowspan="<?= count($item['size']); ?>"<?php endif; ?>>
                                        <a href="/product/<?= $item['productId']; ?>.html" target="_blank"><?= $item['name'] ?></a>
                                    </td>
                                    <td class="item-price">
                                        <?= yii::$app->formatter->asDecimal($item['price'], 2); ?> руб.
                                    </td>
                                    <td class="size"><?= $item['size'][0]['sizeCode']; ?></td>
                                    <td class="count-field-box"><?= $item['size'][0]['quantity']; ?> шт.</td>
                                    <td class="price">
                                        <?= yii::$app->formatter->asDecimal($item['size'][0]['quantity'] * $item['price'], 2); ?> руб.
                                    </td>
                                    <?php $totalPrice += $item['size'][0]['quantity'] * $item['price']; ?>
                                </tr>
                                <?php for ($i = 1; $i < count($item['size']); $i++): ?>
                                    <tr>
                                        <td class="item-price">
                                            <?= yii::$app->formatter->asDecimal($item['price'], 2); ?> руб.
                                        </td>
                                        <td class="size"><?= $item['size'][$i]['sizeCode']; ?></td>
                                        <td class="count-field-box"><?= $item['size'][$i]['quantity']; ?> шт.</td>
                                        <td class="price">
                                            <?= yii::$app->formatter->asDecimal($item['size'][$i]['quantity'] * $item['price'], 2); ?> руб.
                                        </td>
                                        <?php $totalPrice += $item['size'][$i]['quantity'] * $item['price']; ?>
                                    </tr>
                                <?php endfor; ?>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td class="total-label" colspan="5">Общая сумма:</td>
                                <td class="total-price">
                                    <?= yii::$app->formatter->asDecimal($totalPrice, 2); ?> руб.
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </dd>
                    <dt>Прикрепленные файлы</dt>
                    <dd>
                    <?php if (count($files) > 0): ?>
                        <ul>
                            <?php foreach ($files as $file): ?>
                            <li><a href="<?= $file; ?>" target="_blank"><?= basename($file); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>Нет прикрепленных файлов.</p>
                    <?php endif ?>
                    </dd>
                </dl>
            </div>
            <div role="tabpanel" id="client" class="tab-pane">
                <?= $form->field($model, 'fio')->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;']) ?>

                <?= $form->field($model, 'phone')->textInput(['type' => 'tel', 'maxlength' => 255, 'style' => 'max-width: 400px;']) ?>

                <?= $form->field($model, 'email')->textInput(['type' => 'email', 'maxlength' => 255, 'style' => 'max-width: 400px;']) ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>
        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveOrder']
            ) ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyOrder']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
