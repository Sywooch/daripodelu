<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\form\ActiveForm;
use kartik\depdrop\DepDrop;
use common\models\MenuTree;

/* @var $this yii\web\View */
/* @var $model common\models\MenuTree */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="tab-content cms" style="padding: 20px; border-top: 1px solid #ddd;">
        <?= $form->field($model, 'name')->textInput(['maxlength' => 255, 'style' => 'max-width: 800px;']) ?>

        <?= $form->field($model, 'parent_id')->dropDownList($model->getTreeForDropDownList(! $model->isNewRecord), ['id' => 'menu-parent-id', 'style' => 'max-width: 500px;']); ?>

        <?= $form->field($model, 'alias', [
                'addon' => [
                    'append' => [
                        'content' => Html::button(Yii::t('app', 'Create'), ['class'=>'btn btn-default', 'id' => 'alias-generate']),
                        'asButton' => true,
                    ],
                    'groupOptions' => ['class' => 'col-md-8'],
                ],
            ])
            ->textInput(['maxlength' => 70/*, 'style' => 'max-width: 600px;'*/])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Page URL. Valid uppercase and lowercase letters of the alphabet, numbers, special characters "-", "_" (without quotes). Alias must end in a letter or number.')); ?>

        <?php
        if ($model->isNewRecord)
        {
            $escapeSelf = false;
            $model->parent_id = 1;
            $model->can_be_parent = MenuTree::PARENT_CAN_BE;
            $model->show_in_menu = MenuTree::SHOW_IN_MENU;
            $model->status = MenuTree::STATUS_ACTIVE;
        }
        else
        {
            $escapeSelf = true;
            $previous = $model->prev()->one();
            $model->prev_id = ( ! is_null($previous)) ? $previous->id: -1;
        }
        ?>

        <?= $form->field($model, 'prev_id')->widget(DepDrop::className(), [
            'data' => ArrayHelper::merge(
                [-1 => '--- ' . Yii::t('app', 'At the beginning') . ' ---'],
                ArrayHelper::map($model->getSiblingItems($escapeSelf), 'id', 'name')
            ),
            'pluginOptions' => [
                'depends' => ['menu-parent-id'],
                'placeholder' => false,
                'url' => Url::to(['siblings']),
            ],
            'options' => ['style' => 'max-width: 500px;'],
        ]); ?>

        <?= $form->field($model, 'can_be_parent')->dropDownList($model->getParentStatuses(), ['style' => 'max-width: 100px;']); ?>

        <?= $form->field($model, 'show_in_menu')
            ->dropDownList($model->getShowMenuStatuses(), ['style' => 'max-width: 100px;'])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'If the field is selected "<strong> Yes </strong>", this item can be displayed in the site menu.')); ?>

        <?= $form->field($model, 'module_id')->textInput(['maxlength' => 40, 'style' => 'max-width: 300px;'])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'module_hint')); ?>

        <?= $form->field($model, 'controller_id')->textInput(['maxlength' => 40, 'style' => 'max-width: 300px;'])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'controller_hint')); ?>

        <?= $form->field($model, 'action_id')->textInput(['maxlength' => 40, 'style' => 'max-width: 300px;'])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'action_hint')); ?>

        <?= $form->field($model, 'ctg_id')->textInput(['maxlength' => 10, 'style' => 'max-width: 300px;'])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'ctg_id_hint')); ?>

        <?= $form->field($model, 'item_id')->textInput(['maxlength' => 10, 'style' => 'max-width: 300px;'])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'item_id_hint')); ?>

        <?= $form->field($model, 'status')
            ->dropDownList(MenuTree::getStatusOptions(), ['style' => 'max-width: 200px;'])
            ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Only menu items with status "Published" is displayed on the site.'));
        ?>
    </div>
    <div style="padding-bottom: 5px;">&nbsp;</div>
    <div class="form-group btn-ctrl">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveMenuItem']) ?>
        <?php if ( ! $model->isNewRecord): ?>
            <?= Html::submitButton(Yii::t('app', 'Apply'),['class' => 'btn btn-default', 'name' => 'applyMenuItem']); ?>
        <?php endif; ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php $this->registerJs("
    var aliasGenerate = $('#alias-generate'),
        menuName = $('#menu-name'),
        menuAlias = $('#menu-alias');

    aliasGenerate.on('click', function(){
        var str = menuName.val();

        $.ajax({
            type: 'GET',
            url: '" . Url::to(['makealias']) . "',
            dataType: 'json',
            data: {phrase: str},
            beforeSend: function(){
                if ( str == '' || str == null)
                {
                    bootbox.alert('" . Yii::t('app', 'You must fill in the name of the menu item.') . "');

                    return false;
                }
                else
                {
                    menuAlias.addClass('loading');
                    aliasGenerate.addClass('loading');
                }
            },
            success: function(data, textStatus, jqXHR){
                menuAlias.val(data.rslt);
                menuAlias.removeClass('loading');
                aliasGenerate.removeClass('loading');
            },
            error: function(){
                bootbox.alert('" . Yii::t('app', 'An error occurred while updating!') . "');
                menuAlias.removeClass('loading');
                aliasGenerate.removeClass('loading');
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
    });
");