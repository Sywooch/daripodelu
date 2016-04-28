<?php

use yii\bootstrap\Button;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use backend\models\Product;
use backend\models\ProductAttachment;
use kartik\grid\GridView;
use kartik\grid\DataColumn;
use kartik\grid\ActionColumn;
use kartik\select2\Select2;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

/* @var $this yii\web\View */
/* @var $model backend\models\Product */
/* @var $form yii\widgets\ActiveForm */
/* @var $prints backend\models\PrintKind[] */
/* @var $filterTypes backend\models\FilterType[] */
/* @var $tabNumber integer */

if ($tabNumber < 1 || $tabNumber > 7)
{
    $tabNumber = 1;
}

$printsArr = ArrayHelper::map($prints, 'name', 'description');
foreach ($printsArr as $key => &$value)
{
    $value = $key . ' - ' . $value;
}

$photoDataProvider = new ActiveDataProvider([
    'query' => new ActiveQuery('\backend\models\ProductAttachment'),
]);

$filesDataProvider = new ActiveDataProvider([
    'query' => new ActiveQuery('\backend\models\ProductAttachment'),
]);

$slavesDataProvider = new ActiveDataProvider([
    'query' => new ActiveQuery('\backend\models\SlaveProduct'),
]);

$groupDataProvider = new ActiveDataProvider([
    'query' => new ActiveQuery('\backend\models\Product'),
]);

$productAttachmentFiles = [];
$productAttachmentImages = [];
foreach ($model->productAttachments as $productAttachment)
{
    if ($productAttachment->meaning == ProductAttachment::IS_FILE)
    {
        $productAttachmentFiles[] = $productAttachment;
    }
    elseif ($productAttachment->meaning == ProductAttachment::IS_IMAGE)
    {
        $productAttachmentImages[] = $productAttachment;
    }
}

$photoDataProvider->setModels($productAttachmentImages);
$filesDataProvider->setModels($productAttachmentFiles);
$slavesDataProvider->setModels($model->slaveProducts);
$groupDataProvider->setModels($model->groupProducts);
?>
<div class="product-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation"<?php if ($tabNumber == 1): ?> class="active"<?php endif; ?>><a href="#main"
                                                                                                   aria-controls="main"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Основное</a>
            </li>
            <li role="presentation"<?php if ($tabNumber == 2): ?> class="active"<?php endif; ?>><a href="#pack"
                                                                                                   aria-controls="pack"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Упаковка</a>
            </li>
            <li role="presentation"<?php if ($tabNumber == 3): ?> class="active"<?php endif; ?>><a href="#photo"
                                                                                                   aria-controls="photo"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Доп.
                    фотографии</a></li>
            <li role="presentation"<?php if ($tabNumber == 4): ?> class="active"<?php endif; ?>><a href="#files"
                                                                                                   aria-controls="files"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Файлы</a>
            </li>
            <li role="presentation"<?php if ($tabNumber == 5): ?> class="active"<?php endif; ?>><a href="#filters"
                                                                                                   aria-controls="filters"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Применяемые
                    фильтры</a></li>
            <li role="presentation"<?php if ($tabNumber == 6): ?> class="active"<?php endif; ?>><a href="#slave"
                                                                                                   aria-controls="slave"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Дочерние
                    товары</a></li>
            <li role="presentation"<?php if ($tabNumber == 7): ?> class="active"<?php endif; ?>><a href="#group"
                                                                                                   aria-controls="group"
                                                                                                   role="tab"
                                                                                                   data-toggle="tab">Связанные
                    товары</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane<?php if ($tabNumber == 1): ?> active<?php endif; ?>">
                <div class="product-img-place">
                    <div class="product-img-border">
                        <? if (isset($model->small_image) && file_exists($model->smallImagePath)): ?>
                            <img src="<?= $model->smallImageUrl; ?>" alt="">
                        <?php else: ?>
                            <img src="<?= Yii::getAlias('@web/img/no-image.png'); ?>" alt="">
                        <? endif ?>
                    </div>
                </div>
                <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'style' => 'max-width: 600px;',]) ?>

                <?= $form->field($model, 'product_size')->textInput(['maxlength' => true, 'style' => 'max-width: 400px;',]) ?>

                <?= $form->field($model, 'matherial')->textInput(['maxlength' => true, 'style' => 'max-width: 400px;',]) ?>

                <?= $form->field($model, 'prints')->widget(Select2::className(), [
                    'data' => $printsArr,
                    'options' => [
                        'multiple' => true,
                    ],
                    'pluginOptions' => [
                        'width' => '600px'
                    ],
                ]); ?>

                <?= $form->field($model, 'content')->widget(CKEditor::className(), [
                    'editorOptions' => ElFinder::ckeditorOptions(
                        'elfinder',
                        [
                            'preset' => 'full',
                            'inline' => false,
                        ]
                    ),
                ]); ?>

                <?= $form->field($model, 'status_id')->dropDownList(Product::getStatusOptions(), ['style' => 'max-width: 400px;',])->label('Статус'); ?>

                <?= $form->field($model, 'brand')->textInput(['maxlength' => true, 'style' => 'max-width: 400px;',]) ?>

                <?= $form->field($model, 'weight')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'free')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'inwayamount')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'inwayfree')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'enduserprice')->textInput(['style' => 'max-width: 200px;',]) ?>

            </div>
            <div role="tabpanel" id="pack" class="tab-pane<?php if ($tabNumber == 2): ?> active<?php endif; ?>">
                <?= $form->field($model, 'pack_amount')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_weigh')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_volume')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_sizex')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_sizey')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'pack_sizez')->textInput(['style' => 'max-width: 200px;',]) ?>

                <?= $form->field($model, 'amount')->textInput(['style' => 'max-width: 200px;',]) ?>
            </div>
            <div role="tabpanel" id="photo" class="tab-pane<?php if ($tabNumber == 3): ?> active<?php endif; ?>">
                <?php Pjax::begin(['id' => 'extra-images']); ?>
                <?= GridView::widget([
                    'dataProvider' => $photoDataProvider,
                    'filterModel' => null,
                    'rowOptions' => [
                        'class' => 'gv-row-90',
                    ],
                    'summary' => '',
                    'columns' => [
                        [
                            'format' => 'html',
                            'contentOptions' => ['class' => 'gv-td-img-90', 'style' => 'width: 90px'],
                            'value' => function ($row) use ($model)
                            {
                                $src = is_null($row->image) ? '/admin/img/no-image.png' : '/uploads/' . $model->id . '/' . $row->image;

                                return Html::img($src, ['class' => 'gv-prod-img-90']);
                            }
                        ],
                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'name',
                            'editableOptions' => [
                                'size' => 'lg',
                                'submitButton' => [
                                    'class' => 'btn btn-sm btn-primary',
                                    'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                                ],
                            ],
                            'refreshGrid' => true,
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'template' => '{delete}',
                            'contentOptions' => ['style' => 'width: 50px'],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
            <div role="tabpanel" id="files" class="tab-pane<?php if ($tabNumber == 4): ?> active<?php endif; ?>">
                <?php
                $i = 0;
                Pjax::begin(['id' => 'extra-files']);
                ?>
                <?= GridView::widget([
                    'dataProvider' => $filesDataProvider,
                    'filterModel' => null,
                    'summary' => '',
                    'columns' => [
                        [
                            'class' => DataColumn::className(),
                            'label' => 'Скачать',
                            'format' => 'raw',
                            'value' => function ($row) use ($model)
                            {
                                return Html::a('<i class="glyphicon glyphicon-download-alt"></i>', '/uploads/' . $model->id . '/' . $row->file, ['data' => ['pjax' => 0], 'title' => 'Скачать']);
                            },
                            'contentOptions' => ['style' => 'width: 50px; text-align: center;'],
                        ],
                        [
                            'class' => 'kartik\grid\EditableColumn',
                            'attribute' => 'name',
                            'editableOptions' => function ($model, $key, $index, $grid)
                            {
                                return [
                                    'size' => 'lg',
                                    'submitButton' => [
                                        'class' => 'btn btn-sm btn-primary',
                                        'icon' => '<i class="glyphicon glyphicon-ok"></i>',
                                    ],
                                    'options' => ['id' => 'file-attachment-' . $index],
                                ];
                            },
                            'refreshGrid' => true,
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'template' => '{delete}',
                            'contentOptions' => ['style' => 'width: 50px'],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
            <div role="tabpanel" id="filters" class="tab-pane<?php if ($tabNumber == 5): ?> active<?php endif; ?>">
                <?php foreach ($filterTypes as $index => $filterType): ?>
                    <?= $form->field($filterType, '[' . $filterType->id . ']value')
                        ->widget(Select2::className(), [
                            'data' => ArrayHelper::map($filterType->filters, 'id', 'name'),
                            'options' => [
                                'multiple' => true,
                            ],
                            'pluginOptions' => [
                                'width' => '600px'
                            ],
                        ])
                        ->label($filterType->name); ?>
                <?php endforeach; ?>
            </div>
            <div role="tabpanel" id="slave" class="tab-pane<?php if ($tabNumber == 6): ?> active<?php endif; ?>">
                <?= Button::widget([
                    'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add'),
                    'encodeLabel' => false,
                    'options' => [
                        'class' => 'btn-success btn-sm pull-right',
                        'href' => Url::to(['/slaveproduct/create', 'id' => $model->id, 'referrer' => Url::to(['product/update', 'id' => $model->id, 'tabNumber' => 6])]),
                        'style' => 'margin:5px; margin-bottom: 10px;',
                    ],
                    'tagName' => 'a',
                ]); ?>
                <div class="clearfix">&nbsp;</div>
                <?php Pjax::begin(['id' => 'slave']); ?>
                <?= GridView::widget([
                    'dataProvider' => $slavesDataProvider,
                    'filterModel' => null,
                    'summary' => '',
                    'columns' => [
                        [
                            'attribute' => 'name',
                        ],
                        [
                            'attribute' => 'size_code',
                            'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'controller' => 'slaveproduct',
                            'template' => '{update} {delete}',
                            'urlCreator' => function ($action, $row, $key, $index) use ($model)
                            {
                                if ($action === 'update')
                                {
                                    $url = Url::to(['slaveproduct/update', 'id' => $row->id, 'referrer' => Url::to(['product/update', 'id' => $model->id, 'tabNumber' => 6])]);
                                    return $url;
                                }
                                if ($action === 'delete')
                                {
                                    $url = Url::to(['slaveproduct/delete', 'id' => $row->id, 'referrer' => Url::to(['product/update', 'id' => $model->id, 'tabNumber' => 6])]);
                                    return $url;
                                }
                            },
                            'contentOptions' => ['style' => 'width: 50px'],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
            <div role="tabpanel" id="group" class="tab-pane<?php if ($tabNumber == 7): ?> active<?php endif; ?>">
                <?= Button::widget([
                    'label' => '<i class="glyphicon glyphicon-log-out"></i> ' . Yii::t('app', 'Leave the group'),
                    'encodeLabel' => false,
                    'options' => [
                        'class' => 'btn-primary btn-sm pull-right',
                        'href' => Url::to(['/product/grouplogout', 'id' => $model->id]),
                        'style' => 'margin:5px; margin-bottom: 10px;',
                    ],
                    'tagName' => 'a',
                ]); ?>
                <div class="clearfix">&nbsp;</div>
                <?php Pjax::begin(['id' => 'slave']); ?>
                <?= GridView::widget([
                    'dataProvider' => $groupDataProvider,
                    'filterModel' => null,
                    'rowOptions' => [
                        'class' => 'gv-row-90',
                    ],
                    'summary' => '',
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'contentOptions' => ['style' => 'width: 50px'],
                        ],
                        [
                            'format' => 'html',
                            'contentOptions' => ['class' => 'gv-td-img-90', 'style' => 'width: 90px'],
                            'value' => function ($row)
                            {
                                $src = is_null($row->small_image) ? '/admin/img/no-image.png' : '/uploads/' . $row->id . '/' . $row->small_image;

                                return Html::img($src, ['class' => 'gv-prod-img-90']);
                            }
                        ],
                        [
                            'attribute' => 'code',
                            'contentOptions' => ['style' => 'width: 80px'],
                        ],
                        [
                            'attribute' => 'name',
                        ],
                        [
                            'attribute' => 'enduserprice',
                            'label' => 'Цена',
                            'contentOptions' => ['style' => 'width: 120px; text-align: right;'],
                            'value' => function ($row)
                            {
                                return Yii::$app->formatter->asDecimal($row->enduserprice, 2);
                            }
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'controller' => 'slaveproduct',
                            'template' => '{update} {delete}',
                            'contentOptions' => ['style' => 'width: 50px'],
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>
        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveProduct']
            ) ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyProduct']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
