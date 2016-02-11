<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\bootstrap\Alert;
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\grid\GridView;
use yii\grid\CheckboxColumn;
use yii\widgets\Pjax;
use kartik\file\FileInput;
use common\models\Image;
use backend\widgets\Spinner;

yii\jui\JuiAsset::register($this);

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

//TODO-cms Сделать удаление фотографий с подтверждением
//TODO-cms Разобраться с выводом ошибок валидации модели при загрузки файлов
//TODO-cms Исправить текст некоторых сообщений об ошибках, выводимых виджетом FileInput
//TODO-cms Получать из правил проверки модели (или из поведения) максимальный размер загружаемого

if( Yii::$app->session->hasFlash('image-error') )
{
    echo Alert::widget ([
        'options' => [
            'class' => 'alert-danger'
        ],
        'body' => Yii::$app->session->getFlash('error'),
    ]);
}

if( Yii::$app->session->hasFlash('image-success') )
{
    echo Alert::widget ([
        'options' => [
            'class' => 'alert-success'
        ],
        'body' => Yii::$app->session->getFlash('success'),
    ]);
}

Modal::begin([
    'id' => 'file-upload-modal',
    'header' => '<h4>' . Yii::t('app', 'Adding photos') . '</h4>',
    'toggleButton' => [
        'label' => '<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('app', 'Add photo'),
        'class' => 'btn btn-success'
    ],
]);

echo FileInput::widget([
    'name'=>'model_images[]',
    'id' => 'input-file',
    'options' => [
        'multiple' => true,
        'accept' => 'image/*',
    ],
    'pluginOptions' => [
        'allowedFileTypes' => ['image'],
        'allowedFileExtensions' => ['jpg', 'gif', 'png', 'jpeg'],
        'fileActionSettings' => [
            'indicatorNewTitle' => Yii::t('app', 'Not uploaded yet'),
            'indicatorSuccessTitle' => Yii::t('app', 'Uploaded'),
            'indicatorErrorTitle' => Yii::t('app', 'Upload Error'),
            'indicatorLoadingTitle' => Yii::t('app', 'Uploading ...'),
            'removeTitle' => Yii::t('app', 'Remove file'),
            'uploadTitle' => Yii::t('app', 'Upload file'),
        ],
        'layoutTemplates' => [
            'actions' => '<div class="file-actions">' .
                '    <div class="file-footer-buttons">' .
                '        {delete}' .
                '    </div>' .
                '    <div class="file-upload-indicator" tabindex="-1" title="{indicatorTitle}">{indicator}</div>' .
                '    <div class="clearfix"></div>' .
                '</div>',
        ],
        'maxFileSize' => 2048,
        'maxFileCount' => 10,
        'previewFileType' => 'image',
        'showRemove' => false,
        'uploadUrl' => Yii::$app->request->url,
    ],
]);

Modal::end();

Modal::begin([
    'id' => 'update-photo-modal',
    'header' => '<h4>' . Yii::t('app', 'Update photo') . '</h4>',
]);

echo '<div class="modal-body">' . Spinner::widget([
        'preset' => Spinner::MEDIUM,
        'color' => 'grey',
        'align' => 'center']) . '</div>';

Modal::end();
?>



<p>&nbsp;</p>
<?php Pjax::begin(['id' => 'photo-gv-container', 'timeout' => false, 'enablePushState' => false]); ?>

<?= Html::a('<span class="glyphicon glyphicon-floppy-save"></span> ' . Yii::t('app', 'Save changes'),
    ['image/sort'],
    [
        'class' => 'btn btn-primary btn-sm pull-right',
        'id' => 'img-save-sort-changes',
        'disabled' => 'disabled',
        'title' => Yii::t('app', 'Save changes after sorting'),
        'style' => 'margin-bottom: 5px;',
    ]
);
?>
<div class="clearfix">&nbsp;</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'id' => 'photoids',
    'columns' => [
        [
            'class' => CheckboxColumn::className(),
            'checkboxOptions' => [],
            'name' => 'photoids[]',
            'contentOptions' => ['style'=>'width: 30px'],
        ],
        [
            'class' => DataColumn::className(),
            'label' => 'Фото',
            'format' => 'html',
            'value' => function($item) { return Html::img($item->image_url_80x80); },
            'contentOptions' => [
                'style'=>'width: 30px',
                'alt' => '',
            ],
        ],
        'title',
        [
            'attribute' => 'is_main',
            'format' => 'raw',
            'value' => function($item) {
                return ($item->is_main === 1) ? '<span class="glyphicon glyphicon-ok"></span>' : Html::a('<span class="glyphicon glyphicon-remove"></span>',
                    ['image/setmain', 'id' => $item->id],
                    [
                        'class' => 'set-main-link',
                        'title' => ($item->is_main === 1) ? Yii::t('app', 'Main image') : Yii::t('app', 'Set image as main'),
                        'data-pjax' => 'photo-gv-container',
                    ]
                );
            },
            'contentOptions' => ['style'=>'width: 70px; text-align: center;'],
        ],
        [
            'attribute' => 'status',
            'format' => 'html',
            'value' => function($item){ return ($item->status === 1) ? '<span class="glyphicon glyphicon-ok"></span>' : '<span class="glyphicon glyphicon-remove"></span>'; },
            'contentOptions' => ['style'=>'width: 70px; text-align: center;'],
        ],
        [
            'class' => ActionColumn::className(),
            'controller' => 'image',
            'template' => '{update} {delete}',
            'buttons' => [
                'update' => function ($url) {
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, [
                        'title' => Yii::t('yii', 'Update'),
                        'aria-label' => Yii::t('yii', 'Update'),
                        'data-pjax' => '0',
//                        'data-toggle' => 'modal',
                        'data-target' => '#update-photo-modal',
                    ]);
                },
                'delete' => function ($url) {
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                        'class' => 'img-del-btn',
                        'title' => Yii::t('yii', 'Delete'),
                        'aria-label' => Yii::t('yii', 'Delete'),
                        'data-pjax'=>'photo-gv-container',
                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                        'onclick' => '$.pjax.defaults.timeout = false;',
                    ]);
                },
            ],
            'contentOptions' => ['style'=>'width: 50px'],
        ],
    ],
]); ?>

<?php $this->registerJs("

    var fixHelper = function(e, ui) {
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        },
        imgSaveSort = $('#img-save-sort-changes'),
        loadIcon = $('" .  Spinner::widget([
            'preset' => Spinner::TINY,
            'color' => 'grey',
            'align' => 'center']) . "');

    $('.table tbody').sortable({
        cursor: 'move',
        cursorAt: { left: 10 },
        helper: fixHelper,
        placeholder: 'ui-sortable-placeholder',
        update: function( event, ui ) {
            imgSaveSort.attr('disabled', false);
        }
    }).disableSelection();

    imgSaveSort.on('click', function(e){
        var link = $(this).attr('href'),
            imgList = {};

        $('.table tbody tr').each(function(index, value) {
            imgList[index] = $(this).attr('data-key');
        });


        $.ajax({
            type: 'POST',
            url: link,
            dataType: 'json',
            data: {sortData: imgList},
            beforeSend: function(){},
            success: function(data, textStatus, jqXHR){
                if (data.status == 'success')
                {
                    bootbox.alert('" . Yii::t('app', 'The changes was successfully saved!') . "');
                    imgSaveSort.attr('disabled', true);
                }
                else if (data.status == 'no_updated')
                {
                    bootbox.alert('" . Yii::t('app', 'No changes was made!') . "');
                }
                else
                {
                    bootbox.alert('" . Yii::t('app', 'No changes was made!') . "');
                }

//                $.pjax.defaults.timeout = false;
//                $.pjax.reload({container:'#photo-gv-container'});
            },
            error: function(){
                bootbox.alert('" . Yii::t('app', 'An error occurred while updating!') . "');
            },
            statusCode: {
                404: function() {
                  bootbox.alert('" . Yii::t('app', 'Page not found!') . "');
                },
                500: function() {
                  bootbox.alert('" . Yii::t('app', 'Internal server error!') . "');
                },
            }
        });

        return false;
    });

    $('.set-main-link').on('click', function() {
        parent = $(this).parent();

        parent.prepend(loadIcon);
        parent.find('.kv-spin').spin('tiny', 'grey');
        $(this).hide();
    });

    var isUploadedSuccess = false;
    $('#input-file').on('fileuploaded', function(event, data, previewId, index) {
        $('#' + previewId).remove();
        isUploadedSuccess = true;
    });

    $('#input-file').on('fileuploaderror', function(event, data, previewId, index) {
//        alert(data.response);
    });

    $('#file-upload-modal').on('hidden.bs.modal', function (e) {
        if (isUploadedSuccess)
        {
//            $('#photoids').yiiGridView('update');
            $.pjax.defaults.timeout = false;
            $.pjax.reload({container:'#photo-gv-container'});
        }
        $('#input-file').fileinput('clear');
        isUploadedSuccess = false;
    });

    $('a[data-target=#update-photo-modal]').click(function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        // load the url and show modal on success
        $('#update-photo-modal .modal-body').load(target, function() {
            $('#update-photo-modal').modal('show');
        });
    });

    $('#update-photo-modal').on('hidden.bs.modal', function (e) {
        $.pjax.defaults.timeout = false;
        $.pjax.reload({container:'#photo-gv-container'});
    });
"); ?>

<?php Pjax::end(); ?>