<?php

use yii\helpers\Html;

$client = $mail['client'];
$order = $mail['order'];
$images = $mail['images'];

$totalSum = 0;
foreach ($order['content'] as $item)
{
    foreach ($item['size'] as $size)
    {
        $totalSum += $item['price'] * $size['quantity'];
    }
}

/* @var $message \yii\swiftmailer\Message */

?>
<table border="0" cellpadding="0" cellspacing="0" width="600px" style="background: white; font-family: 'Open Sans', Sans-Serif; font-size: 14px;">
    <tbody>
    <tr>
        <td style="text-align: center; padding: 10px 0 30px;">
            <a href="<?= yii::$app->params['protocol']; ?><?= yii::$app->params['site']; ?>/" target="_blank">
                <img src="<?= $message->embed($logoMin); ?>" style="height: 70px;" alt="<?= Html::encode(yii::$app->config->siteName); ?>">
            </a>
        </td>
    </tr>
    <tr>
        <td style="padding-bottom: 40px; text-align: center;">Здравствуйте, <?= Html::encode($client['name']) ?></td>
    </tr>
    <tr>
        <td style="padding-bottom: 15px; text-align: center;">
            Вы сделали заказ <span style="color: #a172cc; font-weight: bold;"><?= date('d.m.Y', strtotime($order['date'])); ?></span> в <span style="color: #a172cc; font-weight: bold;"><?= date('h:i', strtotime($order['date'])); ?></span> на сайте "<?= Html::encode(yii::$app->config->siteName); ?>" на сумму:
        </td>
    </tr>
    <tr>
        <td style="color: #ff80ab; padding-bottom: 40px; text-align: center; font-size: 18px; font-weight: bold"><?= yii::$app->formatter->asDecimal($totalSum, 2); ?> руб.</td>
    </tr>
    <tr>
        <td style="padding-bottom: 40px; text-align: center">
            Мы приняли ваш заказ. В ближайшее время наш оператор свяжется с вами для подтверждения заказа.
        </td>
    </tr>
    <tr>
        <td style="padding-bottom: 40px;">
            <table border="0" cellpadding="0" cellspacing="0" width="600px" style="background: white; height: 20px">
                <tbody>
                <tr>
                    <td style="width: 180px;">
                        <div style="height: 5px; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #000;"></div>
                    </td>
                    <td style="width: 240px; text-align: center;">Ваш заказ <span style="white-space: nowrap">№ <?= $order['id'] ?></span></td>
                    <td style="width: 180px">
                        <div style="height: 5px; border-bottom-width: 1px; border-bottom-style: solid; border-bottom-color: #000;"></div>
                    </td>
                </tr>
                </tbody>
            </table>
        </td>
    </tr>
    <tr>
        <td style="padding: 0 0 40px">
            <table border="0" cellpadding="0" cellspacing="0" width="600px" style="background: white; font-size: 13px;">
                <thead>
                <tr style="border-bottom: 1px solid #ddd;">
                    <th colspan="2" style="border-bottom: 1px solid #ddd; text-align: left; padding: 0 0 10px;">Товар</th>
                    <th style="border-bottom: 1px solid #bbb; text-align: left; padding: 0 0 10px;">Цена за 1 шт.</th>
                    <th style="border-bottom: 1px solid #bbb; text-align: center; padding: 0 0 10px;">Размер</th>
                    <th style="border-bottom: 1px solid #bbb; text-align: center; padding: 0 0 10px;">Кол-во</th>
                    <th style="border-bottom: 1px solid #bbb; text-align: right; padding: 0 5px 10px 0;">Общая сумма</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($order['content'] as $key => $item): ?>
                    <?php if ($key == 0): ?>
                    <tr>
                        <td colspan="6" style="height: 20px;"></td>
                    </tr>
                    <?php else: ?>
                    <tr>
                        <td colspan="6" style="height: 30px;"></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td<?php if (count($item['size']) > 1): ?> rowspan="<?= count($item['size']); ?>"<?php endif; ?> style="width: 80px; padding: 0 5px;">
                            <div><img src="<?= $message->embed($images[$item['productId']]); ?>" alt="<?= $item['name'] ?>" style="max-height: 80px; max-width: 80px; padding: 0 5px;"></div>
                        </td>
                        <td<?php if (count($item['size']) > 1): ?> rowspan="<?= count($item['size']); ?>"<?php endif; ?> style="width: 120px; padding: 0 10px 0 5px;">
                            <a href="<?= yii::$app->params['protocol']; ?><?= yii::$app->params['site']; ?>/product/<?= $item['productId']; ?>.html" target="_blank"><?= $item['name'] ?></a>
                        </td>
                        <td style="width: 100px;">
                            <?= yii::$app->formatter->asDecimal($item['price'], 2); ?> руб.
                        </td>
                        <td style="width: 60px; text-align: center; padding: 0 5px;"><?= $item['size'][0]['sizeCode']; ?></td>
                        <td style="width: 70px; text-align: center; padding: 0 5px;"><?= $item['size'][0]['quantity']; ?> шт.</td>
                        <td style="width: 120px; text-align: right; padding: 0 5px;">
                            <?= yii::$app->formatter->asDecimal($item['size'][0]['quantity'] * $item['price'], 2); ?> руб.
                        </td>
                    </tr>
                    <?php for ($i = 1; $i < count($item['size']); $i++): ?>
                        <tr>
                            <td style="width: 100px;">
                                <?= yii::$app->formatter->asDecimal($item['price'], 2); ?> руб.
                            </td>
                            <td style="width: 60px; text-align: center; padding: 0 5px;"><?= $item['size'][$i]['sizeCode']; ?></td>
                            <td style="width: 70px; text-align: center; padding: 0 5px;"><?= $item['size'][$i]['quantity']; ?> шт.</td>
                            <td style="width: 120px; text-align: right; padding: 0 5px;">
                                <?= yii::$app->formatter->asDecimal($item['size'][$i]['quantity'] * $item['price'], 2); ?> руб.
                            </td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="6" style="height: 30px;"></td>
                </tr>
                <tr>
                    <td colspan="6" style="border-top: 1px solid #bbb; text-align: right; padding: 10px 5px 0; font-size: 16px;">
                        <span style="display: inline-block; margin-right: 20px;">Итого:</span>
                        <span style="color: #ff80ab; font-weight: bold;"><?= yii::$app->formatter->asDecimal($totalSum, 2); ?> руб.</span>
                    </td>
                </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    <tr>
        <td style="border-top: 1px solid #000; padding: 30px 0 30px;">
            <table border="0" cellpadding="0" cellspacing="0" width="600px" style="background: white; font-size: 13px;">
                <tr>
                    <td style="font-weight: bold;">Получатель:</td>
                </tr>
                <tr>
                    <td style="padding: 0 0 15px;"><?= $client['name']; ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Контактный телефон:</td>
                </tr>
                <tr>
                    <td style="padding: 0 0 15px;"><?= $client['phone']; ?></td>
                </tr>
                <tr>
                    <td style="font-weight: bold;">Электронная почта:</td>
                </tr>
                <tr>
                    <td><?= $client['email']; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    </tbody>
    <tfoot>
    <tr>
        <td style="border-top: 1px solid #000; padding: 40px 0 40px; font-size: 13px; text-align: center;">
            Если у вас есть вопросы, пожалуйста, <br>позвоните нам по телефону <span style="font-weight: bold"><?= yii::$app->config->sitePhone; ?></span> (<?= yii::$app->config->siteWorkSchedule; ?>)<br>или напишите письмо на <a href="mailto:<?= yii::$app->config->siteEmail; ?>"><?= yii::$app->config->siteEmail; ?></a>.
        </td>
    </tr>
    </tfoot>
</table>
