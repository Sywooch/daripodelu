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
/* @var $model app\models\Page */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="page-form">
    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Настройки</a></li>
            <li role="presentation"><a href="#seo" aria-controls="seo" role="tab" data-toggle="tab">SEO</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">
            <?= $form->field($model, 'name')->textInput(['id' => 'page-title', 'maxlength' => 255, 'style' => 'max-width: 600px;']) ?>

            <?php
            $aliasModel = $model->getAliasModel();
            echo $form->field($aliasModel? : new MenuTree(), 'alias')
                ->widget(InputAliasWidget::className(), [
                    'sourceFieldId' => 'page-title',
                    'url' => Url::to(['/menutree/makealias']),
                ])
                ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Page URL. Valid uppercase and lowercase letters of the alphabet, numbers, special characters "-", "_" (without quotes). Alias must end in a letter or number.'));
            ?>

            <?= $form->field($aliasModel? : new MenuTree(), 'parent_id')->dropDownList($model->getTreeForDropDownList(true), ['id' => 'menu-parent-id', 'style' => 'max-width: 500px;']); ?>

            <?= $form->field($aliasModel? : new MenuTree(), 'show_in_menu')
                ->dropDownList($model->getShowMenuStatuses(), ['style' => 'max-width: 100px;'])
                ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'If the field is selected "<strong> Yes </strong>", this item can be displayed in the site menu.')); ?>

            <?= $form->field($model, 'content')->widget(CKEditor::className(),[
                'editorOptions' => ElFinder::ckeditorOptions(
                    'elfinder',
                    [
                        'preset' => 'full',
                        'inline' => false,
                    ]
                ),
            ]); ?>

            </div>
            <div role="tabpanel" id="settings" class="tab-pane">

            <?php
            if ($model->isNewRecord)
            {
                $model->status = Page::STATUS_ACTIVE;
            }
            ?>
            <?= $form->field($model, 'status')->dropDownList(Page::getStatusOptions(), ['style' => 'max-width: 200px;'])->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Only pages with status "Published" is displayed on the site.')); ?>

            <?php if(! $model->isNewRecord): ?>
            <?= $form->field($model, 'last_update_date',['enableClientValidation' => FALSE])->textInput(['style' => 'max-width: 200px;','readonly' => TRUE]) ?>

            <?= $form->field($model, 'created_date',['enableClientValidation' => FALSE])->textInput(['style' => 'max-width: 200px;','readonly' => TRUE]) ?>
            <?php endif; ?>
            </div>
            <div role="tabpanel" id="seo" class="tab-pane">

            <?= $form->field($model, 'meta_title')
                    ->textInput(['maxlength' => 255, 'style' => 'max-width: 600px;'])
                    ->hint(
                        Yii::t('app', '<strong>Note:</strong>') . ' ' .
                        Yii::t('app', 'Is displayed in the title bar of the browser. If the field is empty, in the header of the browser window displays the page title.'));
            ?>

            <?= $form->field($model, 'meta_description')
                    ->textarea()
                    ->hint(
                        Yii::t('app', '<strong>Note:</strong>') . ' ' .
                        Yii::t('app', 'Used by search engines such as Google, Yandex, etc. in displaying search results.'));
            ?>

            <?= $form->field($model, 'meta_keywords')
                   ->textarea()
                    ->hint(
                        Yii::t('app', '<strong>Note:</strong>') . ' ' .
                        Yii::t('app', 'Words that best describe the content of the page. It can be both single words and phrases, but they must meet in the text on the page. Through this the search engines determine the relevance of a page to a particular request.'));
            ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>
        <div class="form-group btn-ctrl">
            <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'savePage']
            ) ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyPage']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<?php $this->registerJs("
    $('#page-nav').affix({
        offset: {
            top: 100,
            bottom: function () {
                return (this.bottom = $('.footer').outerHeight(true))
            }
        }
    })    
"); ?>