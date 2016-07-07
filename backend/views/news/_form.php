<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\MenuTree;
use backend\widgets\InputAliasWidget;
use dosamigos\datetimepicker\DateTimePicker;
use mihaildev\ckeditor\CKEditor;
use mihaildev\elfinder\ElFinder;

/* @var $this yii\web\View */
/* @var $model app\models\News */
/* @var $form yii\widgets\ActiveForm */

//TODO-cms Решить проблему с отображением ошибок в полях на разных вкладках!
?>

<div class="news-form">
    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a>
            </li>
            <li role="presentation"><a href="#photo" aria-controls="photo" role="tab" data-toggle="tab">Фото</a></li>
            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab"
                                       data-toggle="tab">Настройки</a></li>
            <li role="presentation"><a href="#seo" aria-controls="seo" role="tab" data-toggle="tab">SEO</a></li>
        </ul>

        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">

                <?= $form->field($model, 'name')->textInput(['id' => 'news-title', 'maxlength' => 255, /*'style' => 'max-width: 600px;'*/]) ?>

                <?php
                $aliasModel = $model->getAliasModel();
                echo $form->field($aliasModel ?: new MenuTree(), 'alias')
                    ->widget(InputAliasWidget::className(), [
                        'sourceFieldId' => 'news-title',
                        'url' => Url::to(['/menutree/makealias']),
                    ])
                    ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Page URL. Valid uppercase and lowercase letters of the alphabet, numbers, special characters "-", "_" (without quotes). Alias must end in a letter or number.'));
                ?>

                <?= $form->field($aliasModel ?: new MenuTree(), 'parent_id')->dropDownList($model->getTreeForDropDownList(), ['id' => 'menu-parent-id', 'style' => 'max-width: 500px;']); ?>

                <?= $form->field($model, 'published_date')
                    ->widget(DateTimePicker::className(), [
                        'language' => 'ru',
                        'clientOptions' => [
                            'autoclose' => true,
                            'format' => 'dd.mm.yyyy, hh:ii:ss',
                            'todayBtn' => true
                        ],
                        'containerOptions' => [
                            'style' => 'width: 250px;',
                        ],
                    ])
                    ->hint(
                        Yii::t('app', '<strong>Note:</strong>') . ' ' .
                        Yii::t('app', 'Publication date news. The later date and time of publication, the higher is the news in the list.'));
                ?>

                <?= $form->field($model, 'intro')
                    ->textarea(['rows' => 4])
                    ->hint(
                        Yii::t('app', '<strong>Note:</strong>') . ' ' .
                        Yii::t('app', 'Introductory text is displayed after the news title in the news list. If the field is empty, after the news title are displayed first phrases of the main text.'));
                ?>

                <?= $form->field($model, 'content')->widget(CKEditor::className(), [
                    'editorOptions' => ElFinder::ckeditorOptions(
                        'elfinder',
                        [
                            'preset' => 'standard',
                            'inline' => false,
                        ]
                    ),
                ]); ?>

            </div>
            <div role="tabpanel" id="photo" class="tab-pane">
                <?php if ( !$model->isNewRecord): ?>
                    <?= $this->render('@app/views/image/_gridview', [
                        'dataProvider' => $model->behaviors['photo']->dataProvider,
                    ]) ?>
                <?php else: ?>

                    <?= Yii::t('app', 'Images can be added only after the news will be saved.') ?>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                    <p>&nbsp;</p>
                <?php endif; ?>

            </div>
            <div role="tabpanel" id="settings" class="tab-pane">
                <?php
                if ($model->isNewRecord) {
                    $model->status = app\models\News::STATUS_ACTIVE;
                }
                ?>
                <?= $form->field($model, 'status')
                    ->dropDownList(app\models\News::getStatusOptions(), ['style' => 'max-width: 200px;'])
                    ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Only news with status "Published" is displayed on the site.'));
                ?>

                <?php if ( !$model->isNewRecord): ?>
                    <?= $form->field($model, 'created_date')->textInput() ?>

                    <?= $form->field($model, 'last_update_date')->textInput() ?>
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
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveNews']
            ) ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-default', 'name' => 'applyNews']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>