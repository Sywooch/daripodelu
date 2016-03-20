<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $products frontend\models\Product[] */
/* @var $productsTmp frontend\models\Product[] */
/* @var $cart frontend\components\cart\ShopCart */
/* @var $orderForm frontend\models\OrderForm */

$productsTmp = $products;
$products = [];
foreach($productsTmp as $item)
{
    $products[$item->id] = $item;
}

$this->params['breadcrumbs'][] = $heading;
?>
<div class="row">
    <div class="col-10">
        <div class="main-content">
            <h1><?= $heading; ?></h1>
        <? if ($cart->getItemsCount() > 0): ?>
            <?php Pjax::begin(['id' => 'cart', 'enablePushState' => false]) ?>
            <form id="cart_form" action="<?= Url::to(['cart/changequantity']) ?>" method="post">
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
                                <input type="hidden" name="item[<?= $key; ?>][product]" value="<?= $cartItem->productId; ?>">
                            </td>
                            <td class="item-price" data-value="<?= $products[$cartItem->productId]->enduserprice; ?>">
                                <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($products[$cartItem->productId]->enduserprice, 2)); ?><?= $integerPart; ?>,<?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.
                            </td>
                            <td class="size"><?= $cartItem->size[0]->sizeCode; ?></td>
                            <td class="count-field-box"><input class="count-field" name="item[<?= $key; ?>][size][<?= $cartItem->size[0]->sizeId; ?>_<?= $cartItem->size[0]->sizeCode; ?>]"
                                                               type="text" value="<?= $cartItem->size[0]->quantity; ?>">
                                шт.
                            </td>
                            <td class="price" data-value="<?= ($cartItem->size[0]->quantity * $products[$cartItem->productId]->enduserprice); ?>">
                                <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($cartItem->size[0]->quantity * $products[$cartItem->productId]->enduserprice, 2)); ?>
                                <?= $integerPart; ?>,<span class="small"><?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.</span>
                            </td>
                            <td class="delete-link-box"><a class="delete-link" href="<?= Url::to(['cart/deletesize', 'productId' => $cartItem->productId, 'sizeCode' => $cartItem->size[0]->sizeCode]); ?>" title="Удалить"></a></td>
                        </tr>
                        <?php for ($i = 1; $i < count($cartItem->size); $i++): ?>
                        <tr>
                            <td class="item-price" data-value="<?= $products[$cartItem->productId]->enduserprice; ?>">
                                <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($products[$cartItem->productId]->enduserprice, 2)); ?>
                                <?= $integerPart; ?>,<?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.
                            </td>
                            <td class="size"><?= $cartItem->size[$i]->sizeCode; ?></td>
                            <td class="count-field-box"><input class="count-field" name="item[<?= $key; ?>][size][<?= $cartItem->size[$i]->sizeId; ?>_<?= $cartItem->size[$i]->sizeCode; ?>]" type="text" value="<?= $cartItem->size[$i]->quantity; ?>"> шт.</td>
                            <td class="price" data-value="<?= ($cartItem->size[$i]->quantity * $products[$cartItem->productId]->enduserprice); ?>">
                                <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($cartItem->size[$i]->quantity * $products[$cartItem->productId]->enduserprice, 2)); ?>
                                <?= $integerPart; ?>,<span class="small"><?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.</span>
                            </td>
                            <td class="delete-link-box"><a class="delete-link" href="<?= Url::to(['cart/deletesize', 'productId' => $cartItem->productId, 'sizeCode' => $cartItem->size[$i]->sizeCode]); ?>" title="Удалить"></a></td>
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
            <?php Pjax::end(); ?>
        <?php else: ?>
            <p class="empty-cart-msg">Ваша корзина пока пуста</p>
        <? endif ?>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-10">
        <div class="checkout-form-block">
            <b class="top"></b>
            <h6>Ваши персональные данные</h6>
            <div class="checkout-form-box">
                <?php $form = ActiveForm::begin(['method' => 'post', 'options' => ['enctype' => 'multipart/form-data']]); ?>
                    <?= $form->field($orderForm, 'name', ['template' => "{error}\n{input}"])->textInput(['maxlength' => 255, 'placeholder' => $orderForm->getAttributeLabel('name')])->label(false); ?>
                    <div class="cols">
                        <?= $form->field($orderForm, 'phone', ['options' => ['class' => 'form-group col'], 'template' => "{error}\n{input}"])->textInput(['type' => 'tel', 'class' => 'form-control phone-input', 'maxlength' => 255, 'placeholder' => $orderForm->getAttributeLabel('phone')])->label(false); ?>
                        <?= $form->field($orderForm, 'email', ['options' => ['class' => 'form-group col'], 'template' => "{error}\n{input}"])->textInput(['type' => 'email', 'maxlength' => 255, 'placeholder' => $orderForm->getAttributeLabel('email')])->label(false); ?>
                    </div>
                    <p class="label">Загрузить изображение логотипа для рассчета стоимости нанесения</p>
                    <div class="input-file-box">
                        <div class="input-file-item">
                            <input class="file-field" type="text" value="">
                            <span class="img-btn attach-item" title="Прикрепить еще"></span>
                            <?= Html::activeFileInput($orderForm, 'fileOne', ['accept' => '.cdr,.ai,.psd,.tif,image/*,application/pdf']) ?>
                        </div>
                        <div class="input-file-item">
                            <input class="file-field" type="text" value="">
                            <span class="img-btn attach-item" title="Прикрепить еще"></span>
                            <?= Html::activeFileInput($orderForm, 'fileTwo', ['accept' => '.cdr,.ai,.psd,.tif,image/*,application/pdf']) ?>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <div class="btn-group">
<!--                        <button class="btn btn-default" type="submit">Отправить на расчет</button>-->
                        <?= Html::submitButton(
                            'Отправить на расчет',
                            ['class' => 'btn btn-default', 'name' => 'saveSettings']
                        ) ?>
                    </div>
                <?php ActiveForm::end(); ?>
            </div>
            <b class="bottom"></b>
        </div>
    </div>
</div>
<?php $this->registerJs('
    $("body").on("keyup", function(event){
        var eventTarget = event.target;

        if($(eventTarget).is("input") && $(eventTarget).hasClass("count-field"))
        {
            var re = /[^\d]|^[0]+/;

            var str = String($(eventTarget).val()),
                parent = $(eventTarget).parents(".shop-cart").eq(0),
                timeoutId = null;

            str = str.replace(re, "");
            $(eventTarget).val(str);

            timeoutId = parent.data("timeoutId");
            clearTimeout(timeoutId);
            timeoutId = setTimeout(
                function(){
                    $("#cart_form").submit();
                    timeoutId = parent.data("timeoutId");
                    clearTimeout(timeoutId);
                },
                200
            );
            parent.data("timeoutId", timeoutId);
        }
    });

    $(document.body).on("submit", "#cart_form", function(event) {
        event.preventDefault();

        $.pjax.submit(event, "#cart");
    });

    $(document).on("pjax:success", function() {
        var obj = $("table.shop-cart .total-price");

        if (obj.length)
        {
            changeTotalPrice(obj.attr("data-value"));
        }
    })
', \yii\web\View::POS_READY) ?>