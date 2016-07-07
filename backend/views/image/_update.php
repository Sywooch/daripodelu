<?php

use yii\bootstrap\ActiveForm;
use yii\bootstrap\Alert;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\spinner\Spinner;

/* @var $this yii\web\View */
/* @var $model app\models\Image */
/* @var $form yii\bootstrap\ActiveForm */

if (Yii::$app->session->hasFlash('error')) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-danger'
        ],
        'body' => Yii::$app->session->getFlash('error'),
    ]);
}

if (Yii::$app->session->hasFlash('success')) {
    echo Alert::widget([
        'options' => [
            'class' => 'alert-success'
        ],
        'body' => Yii::$app->session->getFlash('success'),
    ]);
}

?>

<div class="image-form">
    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'id' => 'update-form',
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['id' => 'album-title', 'maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 4, 'maxlength' => 255]) ?>

    <?= $form->field($model, 'status')
        ->dropDownList(app\models\Album::getStatusOptions(), ['style' => 'max-width: 200px;'])
        ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Only images with status "Published" is displayed on the site.'));
    ?>

    <div style="border-top: 1px solid #e5e5e5; padding-bottom: 5px;">&nbsp;</div>
    <div class="form-group btn-ctrl">
        <?= Html::submitButton(Yii::t('app', 'Save'), [
                'class' => 'btn btn-primary',
                'name' => 'saveImage',
                'id' => 'saveImage',
                'onclick' => "
                    var form = $('#update-form');

                    if (form.find('.has-error').length)
                    {
                        return false;
                    }

                    $.ajax({
                        url: form.attr('action'),
                        type: 'post',
                        data: form.serialize(),
                        beforeSend: function () {
                            $('#update-photo-modal .modal-body').html('" .
                    Spinner::widget([
                            'preset' => Spinner::MEDIUM,
                            'color' => 'grey',
                            'align' => 'center']
                    ) .
                    "');
                        },
                        success: function (data) {
                            $('#update-photo-modal .modal-body').html(data);
                        }
                    });


                    return false
                ",
            ]
        ) ?>
        <? /*= Html::submitButton(
            Yii::t('app', 'Apply'),
            ['class' => 'btn btn-default', 'name' => 'applyImage', 'id' => 'applyImage']
        );*/ ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>