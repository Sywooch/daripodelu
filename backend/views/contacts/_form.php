<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\ActiveForm;
use common\models\ContactsItem;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsItem */
/* @var $contactItems common\models\ContactsItem[] */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="contacts-item-form">

        <?php $form = ActiveForm::begin(); ?>
        <div role="tabpanel">
            <ul class="nav nav-tabs">
                <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab"
                                                          data-toggle="tab">Основное</a></li>
                <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Настройка
                        видимости</a></li>
            </ul>

            <div class="tab-content cms">
                <div role="tabpanel" id="main" class="tab-pane active">

                    <?= $form->field($model, 'type')
                        ->dropDownList(['' => '---'] + ContactsItem::getTypes(), ['id' => 'contactTypeFld', 'style' => 'max-width: 200px;'])
                        ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'The type of information that may be contained in the field "Value".'));
                    ?>

                    <?= $form->field($model, 'name')->textInput(['style' => 'max-width: 600px;', 'maxlength' => true])->hint(
                        Yii::t('app', '<strong>Example:</strong>') . ' Адрес, Телефон, Факс, Эл. почта и т.д.'
                    ); ?>

                    <?php if ( !$model->isNewRecord || $model->type != ''): ?>
                        <div id="fieldContainer">
                            <?php
                            switch ($model->type) {
                                case ContactsItem::TYPE_FAX:
                                case ContactsItem::TYPE_PHONE:
                                    $template = '_phoneField';
                                    break;

                                case ContactsItem::TYPE_EMAIL:
                                    $template = '_emailField';
                                    break;

                                case ContactsItem::TYPE_ADDRESS:
                                    $template = '_textArea';
                                    break;

                                case ContactsItem::TYPE_OTHER:
                                    $template = '_wysiwygEditor';
                                    break;

                                default:
                                    $template = '_textField';
                            }

                            echo $this->renderAjax($template, ['model' => $model, 'form' => $form,])
                            ?>
                        </div>
                    <?php else: ?>
                        <div id="fieldContainer"></div>
                    <?php endif ?>
                </div>
                <div role="tabpanel" id="settings" class="tab-pane">
                    <?php
                    $contactItemsMap = count($contactItems) > 0 ? ArrayHelper::map($contactItems, 'weight', 'name') : [];
                    ?>
                    <?= $form->field($model, 'weight')->dropDownList(ArrayHelper::merge(
                        [0 => '--- ' . Yii::t('app', 'At the beginning') . ' ---'],
                        $contactItemsMap
                    ), ['style' => 'max-width: 200px;'])->label('Положение после');
                    ?>

                    <?php
                    if ($model->isNewRecord) {
                        $model->status = ContactsItem::STATUS_ACTIVE;
                    }
                    ?>
                    <?= $form->field($model, 'status')
                        ->dropDownList(ContactsItem::getStatusOptions(), ['style' => 'max-width: 200px;'])
                        ->hint(Yii::t('app', '<strong>Note:</strong>') . ' ' . Yii::t('app', 'Only contact items with status "Published" is displayed on the site.'));
                    ?>
                </div>
            </div>
            <div style="padding-bottom: 5px;">&nbsp;</div>

            <div class="form-group btn-ctrl">
                <?= Html::submitButton(
                    $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveContact']
                ); ?>
                <?= Html::submitButton(
                    Yii::t('app', 'Apply'),
                    ['class' => 'btn btn-default', 'name' => 'applyContact']
                ); ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php $this->registerJs('
    $("#contactTypeFld").on("change", function(){
        var fieldType = $(this).val();

        if (fieldType == "")
        {
            $("#fieldContainer").html("");
        }
        else
        {
            $.ajax({
                url: "' . Url::to(['change-field', 'id' => $model->id]) . '",
                type: "post",
                dataType: "json",
                data: {fieldType: fieldType},
                beforeSend: function () {
                },
                success: function (data) {
                    if (data.status == "success")
                    {
                        $("#fieldContainer").html(data.rslt);
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
    });
', View::POS_READY); ?>