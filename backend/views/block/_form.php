<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;
use app\models\Page;
use backend\widgets\InputAliasWidget;
use common\models\MenuTree;

/* @var $this yii\web\View */
/* @var $model backend\models\Block */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="block-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#settings" aria-controls="main" role="tab" data-toggle="tab">Настройка видимости</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">

                <?= $form->field($model, 'name')
                        ->textInput(['maxlength' => true])
                        ->hint(
                            Yii::t('app', '<strong>Note:</strong>') . ' ' .
                            Yii::t('app', 'Is displayed only in the list of blocks.')
                        ); ?>

                <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'content')->widget(CKEditor::className(),[
                    'editorOptions' => ElFinder::ckeditorOptions(
                        'elfinder',
                        [
                            'preset' => 'full',
                            'inline' => false,
                            'allowedContent' => true,
                        ]
                    ),
                ]); ?>
            </div>
            <div role="tabpanel" id="settings" class="tab-pane">
                <?= $form->field($model, 'position')->dropDownList($model->positions(), ['style' => 'max-width: 400px;']) ?>

                <?php
                if ($model->isNewRecord)
                {
                    $readOnly = ['readOnly' => true];
                    $model->show_all_pages = 1;
                }
                else
                {
                    $readOnly = $model->show_all_pages == 1 ? ['readOnly' => true] : [];
                }
                ?>

                <?= $form->field($model, 'show_all_pages')->checkbox(); ?>

                <?= $form->field($model, 'show_on_pages')->textarea(array_merge(['rows' => 6], $readOnly)); ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>

        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveBlock']
            ); ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyBlock']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php $this->registerJs('
    $("#block-show_all_pages").change(function(){
        if ($(this).is(":checked"))
        {
            $("#block-show_on_pages").attr("readOnly", true);
        }
        else
        {
            $("#block-show_on_pages").attr("readOnly", false);
        }
    });
', yii\web\View::POS_READY); ?>