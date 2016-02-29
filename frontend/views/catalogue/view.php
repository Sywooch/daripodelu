<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use frontend\assets\Select2Asset;
use frontend\widgets\BlockWidget;

/* @var $this yii\web\View */
/* @var $categories frontend\models\Catalogue[] */
/* @var $productsProvider yii\data\ActiveDataProvider */
/* @var $products frontend\models\Product[] */
/* @var $filterTypes frontend\models\FilterType[] */

Select2Asset::register($this);

$this->params['breadcrumbs'][] = $heading;
$products = $productsProvider->getModels();
?>
<div class="col-2">
    <?php if ( is_array($categories) && count($categories) > 0): ?>
    <div class="ctg-list-box">
        <div class="ctg-list-top"></div>
        <ul class="no-ls ctg-list">
            <?php foreach ($categories as $category): ?>
            <li><a href="<?= Url::to(['catalogue/view', 'uri' => $category->uri]); ?>" title="<?= $category->name; ?>"><?= (mb_strlen($category->name, 'UTF-8') > 22 ? mb_substr($category->name, 0, 22, 'UTF-8') . '...': $category->name) ?></a><span class="items-count"><?= $category->products_count; ?></span></li>
            <?php endforeach; ?>
        </ul>
        <div class="ctg-list-bottom"></div>
    </div>
    <div class="clear-left"></div>
    <?php endif; ?>
    <?= BlockWidget::widget(['template' => 'category', 'position' => 'left']) ?>
</div>
<div class="col-8">
    <main class="main-content">
        <h1><?= $heading; ?></h1>
        <?php Pjax::begin(['id' => 'pl-container', 'scrollTo' => 1]); ?>
        <div class="filter-box">
            <?php
            $counter = 0;
            foreach ($filterTypes as $filterType):
                if ($filterType->id != 8) :
                $counter++;
                $selected = '';
            ?>
                <select class="choice" name="ProductSearch[filter][<?= Html::encode($filterType->id); ?>]" id="product-search-<?= Html::encode($filterType->id); ?>">
                    <?php
                    /* @var $filter frontend\models\Filter */
                    $filterParamsString = '';
                    foreach ($filterParams as $filterTypeId => $filterId):
                        if ($filterType->id != $filterTypeId):
                            $filterParamsString .= $filterTypeId . '.' . $filterId . '-';
                        endif;
                    endforeach;
                    $filterParamsString = trim($filterParamsString, '-');
                    $linkParamsArr = ['catalogue/view', 'uri' => $uri];
                    if ($filterParamsString != ''):
                        $linkParamsArr['filterParams'] = $filterParamsString;
                    endif;
                    ?>
                    <option value="<?= Url::to($linkParamsArr); ?>"><?= Html::encode($filterType->name); ?></option>
                    <?php foreach ($filterType->filters as $filter): ?>
                    <?php
                    /* @var $filter frontend\models\Filter */
                    $filterParamsString = $filterType->id . '.' . $filter->id;
                    foreach ($filterParams as $filterTypeId => $filterId):
                        if ($filterType->id != $filterTypeId):
                            $filterParamsString .= '-' . $filterTypeId . '.' . $filterId;
                        endif;
                    endforeach;
                    $selected = (isset($filterParams[$filterType->id]) && $filterParams[$filterType->id] == $filter->id)? ' selected' : '';
                    ?>
                    <option value="<?= Url::to(['catalogue/view', 'uri' => $uri, 'filterParams' => $filterParamsString]) ?>"<?= $selected; ?>><?= Html::encode($filter->name); ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if ($counter == 5): ?>
                <div class="hr"></div>
                <?php endif; ?>
            <?php
                endif;
            endforeach;
            ?>
            <?php if ($counter < 5): ?>
                <div class="hr"></div>
            <?php endif; ?>
            <?php
            if (count($filterParams) > 0):
                $filterParamsString = '';
                foreach ($filterParams as $filterTypeId => $filterId):
                    $filterParamsString .= $filterTypeId . '.' . $filterId . '-';
                endforeach;
                $filterParamsString = trim($filterParamsString, '-');
                $linkParamsArr = ['catalogue/view', 'uri' => $uri, 'filterParams' => $filterParamsString];
            else:
                $linkParamsArr = ['catalogue/view', 'uri' => $uri];
            endif;
            ?>
            <form id="product_search_form" action="<?= Url::to($linkParamsArr); ?>" method="post">
                <input type="text" name="ProductSearch[amount]" value="<?= $amountFilter ?>" style="width: 100px;" placeholder="тираж">
                <input type="text" name="ProductSearch[price][from]" value="<?= $priceFromFilter ?>" style="width: 90px;" placeholder="цена от">
                <span class="units">р.</span>
                <span class="txt">&ndash;</span>
                <input type="text" name="ProductSearch[price][to]" value="<?= $priceToFilter ?>" style="width: 90px;" placeholder="цена до">
                <span class="units">р.</span>
                <?= Html::submitButton('Применить', ['class' => 'btn btn-apply', 'name' => 'ProductSearchApply', 'title' => 'Применить параметры фильтра']); ?>
                <a class="new-link" href="<?= Url::to(['catalogue/view', 'uri' => $uri, 'filterParams' => '8.229']) ?>">Новинки</a>
                <span class="new-count txt"><?= $newProductsCount; ?></span>
                <?php if (isset($filterParams[8]) && $filterParams[8] == 229 && count($filterParams) == 1): ?>
                <a href="<?= Url::to(['catalogue/view', 'uri' => $uri]) ?>" title="Отменить фильтр" data-pjax="pl-container"><i class="fa fa-times-circle"></i></a>
                <?php endif; ?>
            </form>
        </div>
        <div class="products-list">
        <?php if (count($products) > 0): ?>
            <?php foreach ($products as $product): ?>
            <div class="product-item" itemscope itemtype="http://schema.org/Product">
                <div class="panel">
                    <div class="panel-top"></div>
                    <?php if ($product->status_id == 0): ?><span class="marker new">Новинка</span><? endif; ?>
                    <a class="name" href="<?= Url::to(['product/view', 'id' => $product->id]); ?>" itemprop="name">
                        <i class="product-img-border"><img class="product-img" src="<?= $product->smallImageUrl; ?>" alt="" itemprop="image"></i><span><?= Html::encode($product->name); ?></span>
                    </a>
                    <?php
                    list($integerPart, $fractionalPart) = explode('.', yii::$app->formatter->asDecimal($product->enduserprice, 2));
                    ?>
                    <div class="offers" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                        <span class="price"><?= $integerPart; ?><?php if ( ! is_null($fractionalPart)): ?>,<span class="small"><?= $fractionalPart; ?></span><?php endif; ?></span>
                        <span class="price-curr">руб.</span>
                        <meta itemprop="price" content="<?= str_replace('.', ',', yii::$app->formatter->asDecimal($product->enduserprice, 2)); ?>">
                        <meta itemprop="priceCurrency" content="RUB">
                    </div>
                    <dl class="info">
                        <dt>Артикул:</dt>
                        <dd itemprop="productID"><?= Html::encode($product->code); ?></dd>
                        <dt>Бренд:</dt>
                        <dd itemprop="brand"><?php if (is_null($product->brand) || trim($product->brand) == ''): ?>&ndash;<?php else: ?><?= Html::encode($product->brand); ?><?php endif; ?></dd>
                        <dt>На складе:</dt>
                        <dd><?= Html::encode($product->amount); ?> шт.</dd>
                        <dt>Свободно:</dt>
                        <dd><?= Html::encode($product->free); ?> шт.</dd>
                    </dl>
                    <?php if (count($product->groupProducts) > 1): ?>
                    <div class="similar-products">
                    <?php foreach ($product->groupProducts as $groupProduct): ?>
                    <?php /* @var $groupProduct frontend\models\Product */ ?>
                        <?php /*if ($product->id != $groupProduct->id): */ ?>
                        <span class="img-border"><?= Yii::$app->imageCache->thumb('/uploads/' . $groupProduct->id . '/' . $groupProduct->small_image, '36') ?></span>
                        <?php /* endif; */ ?>
                    <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
        </div>
        <?= LinkPager::widget([
            'pagination' => $productsProvider->pagination,
            'options' => ['class' => 'pagination inl-blck'],
            'prevPageLabel' => '<',
            'nextPageLabel' => '>',
        ]); ?>
        <?php Pjax::end(); ?>
    </main>
</div>
<?php $this->registerJs('
    $(document).on("pjax:complete", function() {
        if( $(".filter-box select.choice").length ) {
            $(".filter-box select.choice").select2({
                width: "resolve",
                minimumResultsForSearch: 9999
            }).on("change", function(){
                var selectItem = $(this),
                    idVal = "select2-" + selectItem.attr("id") + "-container",
                    inputSpan = null,
                    pseudoSpan = null,
                    pseudoBtn = null;

                if (selectItem.val() != "")
                {
                    $.pjax.reload(
                        "#pl-container",
                        {
                            "url": selectItem.val(),
                            "push": true,
                            "replace": false,
                            "timeout": 1000,
                            "scrollTo": 1
                        }
                    );
                }
            });

            $(".filter-box select.choice option:not(:nth-child(1)):selected").each(function(){
                var selectItem = $(this).parents("select").eq(0),
                    idVal = "select2-" + selectItem.attr("id") + "-container",
                    inputSpan = null,
                    pseudoSpan = null,
                    pseudoBtn = null;

                inputSpan = $("#" + idVal).parents(".select2-container").eq(0);
                inputSpan.addClass("not-empty");
                pseudoSpan = $("<span />")
                pseudoBtn = $("<span />")
                pseudoSpan.addClass("pseudo-span");
                pseudoBtn.attr("title", "Сбросить фильтр").addClass("pseudo-btn");

                pseudoBtn.on("click", function(){
                    selectItem.find("option").attr("selected", false);
                    selectItem.find("option:nth-child(1)").attr("selected", true).change();
                    inputSpan.removeClass("not-empty");
                    pseudoSpan.remove();
                    $(this).remove();
                });

                inputSpan.append(pseudoSpan, pseudoBtn);
            });
        }
    });

    if( $("select.choice").length ) {
        $("select.choice").select2({
            width: "resolve",
            minimumResultsForSearch: 9999
        }).on("change", function(){
            var selectItem = $(this),
                idVal = "select2-" + selectItem.attr("id") + "-container",
                inputSpan = null,
                pseudoSpan = null,
                pseudoBtn = null;

            if (selectItem.val() != "")
            {
                $.pjax.reload(
                    "#pl-container",
                    {
                        "url": selectItem.val(),
                        "push": true,
                        "replace": false,
                        "timeout": 1000,
                        "scrollTo": 1
                    }
                );
            }
        });

        $(".filter-box select.choice option:not(:nth-child(1)):selected").each(function(){
            var selectItem = $(this).parents("select").eq(0),
                idVal = "select2-" + selectItem.attr("id") + "-container",
                inputSpan = null,
                pseudoSpan = null,
                pseudoBtn = null;

            inputSpan = $("#" + idVal).parents(".select2-container").eq(0);
            inputSpan.addClass("not-empty");
            pseudoSpan = $("<span />")
            pseudoBtn = $("<span />")
            pseudoSpan.addClass("pseudo-span");
            pseudoBtn.attr("title", "Сбросить фильтр").addClass("pseudo-btn");

            pseudoBtn.on("click", function(){
                selectItem.find("option").attr("selected", false);
                selectItem.find("option:nth-child(1)").attr("selected", true).change();
                inputSpan.removeClass("not-empty");
                pseudoSpan.remove();
                $(this).remove();
            });

            inputSpan.append(pseudoSpan, pseudoBtn);
        });
    }

    $(document.body).on("submit", "#product_search_form", function(event) {
        event.preventDefault(); // stop default submit behavior when it bubbles to <body>

        $.pjax.submit(event, "#pl-container");
    });
', \yii\web\View::POS_READY) ?>