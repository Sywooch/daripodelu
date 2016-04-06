<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\web\View;
use frontend\widgets\BlockWidget;

/* @var $this yii\web\View */
/* @var $model frontend\models\Product */
/* @var $slaveProduct frontend\models\SlaveProduct */
/* @var $productAttachment frontend\models\ProductAttachment */
/* @var $prints frontend\models\PrintKind[] */

$printsArr = [];
foreach ($prints as $print)
{
    $printsArr[$print->name] = [
        'name' => $print->description,
        'link' => $print->printLink->link,
    ];
}
?>
<div class="col-10">
    <div class="main-content" itemscope itemtype="http://schema.org/Product">
        <h1 itemprop="name"><?= $heading; ?></h1>
        <div class="container product">
            <div class="row">
                <div class="col-7">
                    <?= $this->render('_item', ['model' => $model]) ?>
                    <div class="add-item-box">
                        <a class="add-item-link" href="<?= Url::to(['product/view', 'id' => $model->id]) ?>" data-ajax="addColor">+ Выбрать еще цвет</a>
                    </div>
                </div>
                <div class="col-3">
                    <div class="description" itemprop="description">
                        <?= $model->content ?>
                    </div>
                    <dl class="properties" itemprop="additionalProperty" itemscope itemtype="http://schema.org/PropertyValue">
                    <?php if ( ! is_null($model->brand) && trim($model->brand) != ''): ?>
                        <dt itemprop="name">Бренд:</dt>
                        <dd itemprop="value"><?= $model->brand ?></dd>
                    <?php endif ?>
                    <?php if ( ! is_null($model->product_size) && trim($model->product_size) != ''): ?>
                        <dt itemprop="name">Размеры:</dt>
                        <dd itemprop="value"><?= $model->product_size ?> <a class="size-table" href="#">Таблица размеров</a></dd>
                    <?php endif ?>
                    <?php if ( ! is_null($model->matherial) && trim($model->matherial) != ''): ?>
                        <dt itemprop="name">Материал:</dt>
                        <dd itemprop="value"><?= $model->matherial ?></dd>
                    <?php endif ?>
                    <?php if ( ! is_null($model->weight) && trim($model->weight) != ''): ?>
                        <dt itemprop="name">Вес (1 шт.):</dt>
                        <dd itemprop="value"><?= $model->weight ?> г</dd>
                    <?php endif ?>
                    <?php if ( ! ( is_null($model->pack_sizex) && is_null($model->pack_sizey) && is_null($model->pack_sizez) )): ?>
                        <dt itemprop="name">Размеры коробки:</dt>
                        <?php
                        $glue = ' x ';
                        $size = implode(
                            $glue,
                            [$model->pack_sizex, $model->pack_sizey, $model->pack_sizez]
                        );
                        $size = rtrim($size, $glue);
                        ?>
                        <dd itemprop="value"><?= $size ?> см</dd>
                    <?php endif ?>
                    <?php if ( ! is_null($model->pack_weigh) && trim($model->pack_weigh) != ''): ?>
                        <dt itemprop="name"><?= $model->getAttributeLabel('pack_weigh') ?>:</dt>
                        <dd itemprop="value"><?= $model->pack_weigh ?> г.</dd>
                    <?php endif ?>
                    <?php if ( ! is_null($model->pack_volume) && trim($model->pack_volume) != ''): ?>
                        <dt itemprop="name"><?= $model->getAttributeLabel('pack_volume') ?>:</dt>
                        <dd itemprop="value"><?= floatval($model->pack_volume) / 1000000 ?> м<sup>3</sup></dd>
                    <?php endif ?>
                    <?php if ( ! is_null($model->pack_amount) && trim($model->pack_amount) != ''): ?>
                        <dt itemprop="name"><?= $model->getAttributeLabel('pack_amount') ?>:</dt>
                        <dd itemprop="value"><?= $model->pack_amount ?> шт.</dd>
                    <?php endif ?>
                    </dl>
                    <section class="additional-links-box">
                        <h5 class="h2">Возможные виды нанесения:</h5>
                        <?php foreach ($prints as $print): ?>
                            <p><a href="<?= $print->printLink->link; ?>"><span><?= $print->name; ?>-<?= $print->description; ?></span></a></p>
                        <?php endforeach; ?>
                    </section>
                    <div class="constructor-links-box">
                        <strong class="h2">Конструктор:</strong>
                        <a class="item" href="#">.pdf<b class="y-arrow"></b></a>
                        <a class="item" href="#">.cdr<b class="y-arrow"></b></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJs('
    $("body").on("keyup", function(event){
        var eventTarget = event.target;

        if($(eventTarget).is("input") && $(eventTarget).hasClass("size-count"))
        {
            var re = /[^\d]|^[0]+/;

            var str = String($(eventTarget).val()),
                parent = $(eventTarget).parents(".product-instance").eq(0),
                timeoutId = null,
                price = 0,
                priceContainer = parent.find("meta[itemprop=\"price\"]");

            if (priceContainer.length)
            {
                price = parseFloat(priceContainer.eq(0).attr("content"));
                if (isNaN(price))
                {
                    price = 0;
                }
            }

            str = str.replace(re, "");
            $(eventTarget).val(str);

            timeoutId = parent.data("timeoutId");
            clearTimeout(timeoutId);
            timeoutId = setTimeout(
                function(){
                    calcSum(parent, price);
                    timeoutId = parent.data("timeoutId");
                    clearTimeout(timeoutId);
                },
                200
            );
            parent.data("timeoutId", timeoutId);
        }
    });

    $("body").on("click", function(event){
        var eventTarget = event.target,
            changeColorLinkObj = null,
            addColorLinkObj = null;

        if ($(eventTarget).is("a[data-ajax=\"changeColor\"]"))
        {
            changeColorLinkObj = $(eventTarget);
        }
        else if($(eventTarget).parents("a[data-ajax=\"changeColor\"]").length)
        {
            changeColorLinkObj = $(eventTarget).parents("a[data-ajax=\"changeColor\"]").eq(0);
        }

        if (changeColorLinkObj !== null)
        {
            $.ajax({
                url: changeColorLinkObj.attr("href"),
                dataType: "html",
                beforeSend: function(){},
                success: function(data){
                    var container = changeColorLinkObj.parents(".product-instance").eq(0);

                    container.hide();
                    container.before(data);
                    container.remove();
                },
                error: function(){},
                statusCode: {
                    400: function() {
                        alert( "Bad Request" );
                    },
                    401: function() {
                        alert( "Unauthorized" );
                    },
                    403: function() {
                        alert( "Access Forbidden" );
                    },
                    404: function() {
                        alert( "Page Not Found" );
                    },
                    500: function() {
                        alert( "Internal Server Error" );
                    },
                    502: function() {
                        alert( "Bad Gateway" );
                    },
                    503: function() {
                        alert( "Service Unavailable" );
                    },
                    504: function() {
                        alert( "Gateway Timeout" );
                    }
                }
            });

            return false;
        }

        if ($(eventTarget).is("a[data-ajax=\"addColor\"]"))
        {
            addColorLinkObj = $(eventTarget);
        }
        else if($(eventTarget).parents("a[data-ajax=\"addColor\"]").length)
        {
            addColorLinkObj = $(eventTarget).parents("a[data-ajax=\"addColor\"]").eq(0);
        }

        if (addColorLinkObj !== null)
        {
            $.ajax({
                url: addColorLinkObj.attr("href"),
                dataType: "html",
                beforeSend: function(){},
                success: function(data){
                    var container = addColorLinkObj.parents(".add-item-box").eq(0);

                    container.before(data);
                },
                error: function(){},
                statusCode: {
                    400: function() {
                        alert( "Bad Request" );
                    },
                    401: function() {
                        alert( "Unauthorized" );
                    },
                    403: function() {
                        alert( "Access Forbidden" );
                    },
                    404: function() {
                        alert( "Page Not Found" );
                    },
                    500: function() {
                        alert( "Internal Server Error" );
                    },
                    502: function() {
                        alert( "Bad Gateway" );
                    },
                    503: function() {
                        alert( "Service Unavailable" );
                    },
                    504: function() {
                        alert( "Gateway Timeout" );
                    }
                }
            });

            return false;
        }
    });

    $("body").on("submit", function(event){
        var eventTarget = event.target,
            productsCount = 0;

        if ($(eventTarget).has("form[name=\"add2cartForm\"]"))
        {
            $(eventTarget).find("input.size-count").each(function(){
                productsCount += isNaN(parseInt($(this).val())) ? 0 : parseInt($(this).val())
            });

            if (productsCount == 0)
            {
                alert("Вы забыли указать количество товара");
                return false;
            }

            $.ajax({
                url: $(eventTarget).attr("action"),
                type: $(eventTarget).attr("method"),
                dataType: "json",
                data: $(eventTarget).serialize(),
                beforeSend: function () {
                },
                success: function (data) {
                    if (data.status == "success")
                    {
                        changeTotalPrice(data.rslt);
                        $(eventTarget).find("input.size-count").val("");
                        $(eventTarget).find(".total-box .total-info").hide();
                        $(eventTarget).find(".total-info .total-count").text("0");
                        $(eventTarget).find(".total-info .total-price").html(decoratePrice(0, "руб.", ","));
                    };
                },
                error: function () {
                },
                statusCode: {
                    400: function () {
                        alert("Bad Request");
                    },
                    401: function () {
                        alert("Unauthorized");
                    },
                    403: function () {
                        alert("Access Forbidden");
                    },
                    404: function () {
                        alert("Page Not Found");
                    },
                    500: function () {
                        alert("Internal Server Error");
                    },
                    502: function () {
                        alert("Bad Gateway");
                    },
                    503: function () {
                        alert("Service Unavailable");
                    },
                    504: function () {
                        alert("Gateway Timeout");
                    }
                }
            });
        }

        return false;
    });
', View::POS_READY); ?>