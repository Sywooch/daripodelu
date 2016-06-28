<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;
use frontend\models\ProductAttachment;
use frontend\widgets\BlockWidget;

/* @var $this yii\web\View */
/* @var $model frontend\models\Product */
/* @var $slaveProduct frontend\models\SlaveProduct */
/* @var $productAttachment frontend\models\ProductAttachment */
?>
<div class="product-instance" id="product-<?= $model->id ?>">
    <div class="photo-box">
        <?php
        $imgPathArr = [];
        if (trim($model->super_big_image) != ''):
            $imgArr = [];
            foreach ($model->productAttachments as $productAttachment)
            {
                if ($productAttachment->meaning == ProductAttachment::IS_IMAGE)
                {
                    $imgArr[] = $productAttachment;
                }
            }
            ?>
            <span class="photo">
                <img src="<?= $model->superBigImageUrl ?>" alt="" itemprop="image">
            </span>
            <?php if (count($imgArr) > 1): ?>
            <div class="thumbs">
                <a href="<?= $model->superBigImageUrl ?>">
                    <img src="<?= $model->superBigImageUrl ?>" alt="">
                </a>
                <?php $imgPathArr[] = $model->super_big_image; ?>
                <?php foreach ($imgArr as $productAttachment): ?>
                    <?php if ( ! in_array($productAttachment->image, $imgPathArr)): ?>
                        <a href="<?= $productAttachment->imageUrl ?>">
                            <img src="<?= $productAttachment->imageUrl ?>" alt="">
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <?php
        else:
            /* @var $imgArr frontend\models\ProductAttachment[] */
            $imgArr = [];
            foreach ($model->productAttachments as $productAttachment):
                if ($productAttachment->meaning == ProductAttachment::IS_IMAGE):
                    $imgArr[] = $productAttachment;
                endif;
            endforeach;
            if (count($imgArr) > 0):
                ?>
                <span class="photo">
                    <img src="<?= $imgArr[0]->imageUrl; ?>" alt="" itemprop="image">
                </span>
                <?php if (count($imgArr) > 1): ?>
                <div class="thumbs">
                    <a href="<?= $imgArr[0]->imageUrl; ?>">
                        <img src="<?= $imgArr[0]->imageUrl; ?>" alt="">
                    </a>
                    <?php for ($i = 1; $i < count($imgArr); $i++): ?>
                        <a href="<?= $imgArr[$i]->imageUrl; ?>">
                            <img src="<?= $imgArr[$i]->imageUrl; ?>" alt="">
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
            <?php elseif (trim($model->big_image) != ''): ?>
                <span class="photo">
                    <img src="<?= $model->bigImageUrl ?>" alt="" itemprop="image">
                </span>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="color-option-box">
        <?php if (count($model->groupProducts) > 0): ?>
            <div class="colors-list">
                <?php foreach ($model->groupProducts as $groupProduct): ?>
                    <a<?php if ($model->id == $groupProduct->id): ?> class="active"<?php endif; ?> href="<?= Url::to(['product/view', 'id' => $groupProduct->id]) ?>" data-ajax="changeColor">
                        <?= Yii::$app->imageCache->thumb('/uploads/' . $groupProduct->id . '/' . $groupProduct->small_image, '36') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        <div class="offer" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
            <?php list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($model->enduserprice, 2)); ?>
            <span class="color-price"><?= $integerPart; ?>,<span class="small"><?php if ( ! is_null($fractionalPart)): ?><?= $fractionalPart; ?><?php else: ?>00<?php endif; ?> руб.</span></span>
            <meta itemprop="price" content="<?= preg_replace('/[ ]+/', '', yii::$app->formatter->asDecimal($model->enduserprice, 2)); ?>">
            <meta itemprop="priceCurrency" content="RUB">
        </div>
        <form action="<?= Url::to(['cart/add']); ?>" method="post" name="add2cartForm">
            <input type="hidden" name="product" value="<?= $model->id ?>">
            <table class="order-table">
                <thead>
                <tr>
                    <th>Размер</th>
                    <th>На складе</th>
                    <th>Тираж</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($model->slaveProducts) > 0): ?>
                    <?php foreach ($model->slaveProducts as $slaveProduct): ?>
                        <tr>
                            <td><?= $slaveProduct->size_code ?></td>
                            <td><?= $slaveProduct->amount ?> / <span title="Доступно для резервирования"><?= $slaveProduct->free ?></span></td>
                            <td class="field-box">
                                <input class="size-count" type="text" name="size[<?= $slaveProduct->id ?>_<?= $slaveProduct->size_code ?>]" id="size-<?= $slaveProduct->id ?>-<?= $slaveProduct->size_code ?>" data-target-id="product-<?= $model->id ?>" value="">шт.
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <?php if ( ! is_null($model->product_size) && trim($model->product_size) != ''): ?>
                            <td><?= $model->product_size; ?></td>
                        <?php else: ?>
                            <td style="padding-left: 20px;">&mdash;</td>
                        <?php endif; ?>
                        <td><?= $model->amount ?> / <span title="Доступно для резервирования"><?= $model->free ?></span></td>
                        <td class="field-box">
                            <input class="size-count" type="text" name="size" id="size-<?= $model->id ?>" value="">шт.
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <div class="total-box">
                <button class="btn to-cart" name="add2cartBtn" type="submit">В корзину</button>
                <span class="total-info"><span class="total-count">0</span> штук за <span class="total-price">0.<span class="small">00 руб.</span></span></span>
                <div class="clear"></div>
            </div>
        </form>
    </div>
    <div class="dashes-line"></div>
</div>
<?php $this->registerJs('
    if ($(".thumbs").length)
    {
        owlThumbs = $(".thumbs");

        owlThumbs.on("initialized.owl.carousel", function() {
            var photoWrapperContainer = $(this).parents(".photo-box").eq(0),
                photoWrapper = null,
                bigImg = null;

            if($(this).find(".owl-item").length <= 9)
            {
                $(this).find(".owl-prev, .owl-next").addClass("disabled");
            }

            if(photoWrapperContainer.length)
            {
                photoWrapper = photoWrapperContainer.find(".photo").eq(0);
                bigImg = photoWrapper.find("img").eq(0);
            }

            $(this).find(".owl-item a").click(function() {
                if(photoWrapper && bigImg)
                {
                    bigImg.attr("src", $(this).find("img").attr("src"));
                }

                return false;
            });
        });

        owlThumbs.owlCarousel({
            loop:true,
            nav:true,
            dots: false,
            items: 3,
            margin: 18
        });
    }
', View::POS_LOAD) ?>
