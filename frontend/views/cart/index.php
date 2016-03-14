<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $products frontend\models\Product[] */
/* @var $productsTmp frontend\models\Product[] */
/* @var $cart frontend\components\cart\ShopCart */

$productsTmp = $products;
$products = [];
foreach($productsTmp as $item)
{
    $products[$item->id] = $item;
}

$this->params['breadcrumbs'][] = $heading;
?>
<div class="col-10">
    <div class="main-content">
        <h1><?= $heading; ?></h1>
    <? if ($cart->getItemsCount() > 0): ?>
        <form action="#" method="post">
            <table class="shop-cart">
                <thead>
                <tr>
                    <th>Наименование</th>
                    <th>Цена за штуку</th>
                    <th>Размер</th>
                    <th>Количество</th>
                    <th class="price">Общая сумма</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cart->items as $key => $cartItem): ?>
                    <? if ($key > 0): ?>
                        <tr>
                            <td class="delimiter" colspan="6"></td>
                        </tr>
                    <? endif ?>
                    <tr>
                        <td class="name-box"<?php if (count($cartItem->size) > 1): ?> rowspan="<?= count($cartItem->size); ?>"<?php endif; ?>>
                            <div class="img-box"><img src="<?= $products[$cartItem->productId]->bigImageUrl; ?>" alt="<?= Html::encode($products[$cartItem->productId]->name); ?>"></div>
                            <div class="name"><?= $products[$cartItem->productId]->name; ?></div>
                            <div class="clear-left"></div>
                        </td>
                        <td class="item-price" data-value="<?= $products[$cartItem->productId]->enduserprice; ?>">
                            <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($products[$cartItem->productId]->enduserprice, 2)); ?><?= $integerPart; ?>,<?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.
                        </td>
                        <td class="size"><?= $cartItem->size[0]->sizeCode; ?></td>
                        <td class="count-field-box"><input class="count-field" id="count-item-1" name="count-item-1"
                                                           type="text" value="<?= $cartItem->size[0]->quantity; ?>">
                            шт.
                        </td>
                        <td class="price" data-value="<?= ($cartItem->size[0]->quantity * $products[$cartItem->productId]->enduserprice); ?>">
                            <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($cartItem->size[0]->quantity * $products[$cartItem->productId]->enduserprice, 2)); ?>
                            <?= $integerPart; ?>,<span class="small"><?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.</span>
                        </td>
                        <td class="delete-link-box"><span class="delete-link" title="Удалить"></span></td>
                    </tr>
                    <?php for ($i = 1; $i < count($cartItem->size); $i++): ?>
                        <tr>
                            <td class="item-price" data-value="<?= $products[$cartItem->productId]->enduserprice; ?>">
                                <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($products[$cartItem->productId]->enduserprice, 2)); ?>
                                <?= $integerPart; ?>,<?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.
                            </td>
                            <td class="size"><?= $cartItem->size[0]->sizeCode; ?></td>
                            <td class="count-field-box"><input class="count-field" id="count-item-1" name="count-item-1" type="text" value="<?= $cartItem->size[$i]->quantity; ?>"> шт.</td>
                            <td class="price" data-value="<?= ($cartItem->size[$i]->quantity * $products[$cartItem->productId]->enduserprice); ?>">
                                <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($cartItem->size[$i]->quantity * $products[$cartItem->productId]->enduserprice, 2)); ?>
                                <?= $integerPart; ?>,<span class="small"><?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.</span>
                            </td>
                            <td class="delete-link-box"><span class="delete-link" title="Удалить"></span></td>
                        </tr>
                    <?php endfor; ?>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td class="total-label" colspan="4">Общая сумма:</td>
                    <td class="total-price" data-value="<?= $cart->getTotalPrice(); ?>">
                        <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($cart->getTotalPrice(), 2)); ?>
                        <?= $integerPart; ?>,<span class="small"><?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.</span>
                    </td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </form>
    <?php else: ?>
        <p class="empty-cart-msg">Ваша корзина пока пуста</p>
    <? endif ?>
    </div>
</div>