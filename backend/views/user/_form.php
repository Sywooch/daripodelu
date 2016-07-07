<?php

use yii\bootstrap\Button;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use common\models\User;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model common\models\User */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>
    <div role="tabpanel">
        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a href="#main" aria-controls="main" role="tab" data-toggle="tab">Основное</a>
            </li>
            <li role="presentation"><a href="#fio" aria-controls="fio" role="tab" data-toggle="tab">ФИО</a></li>
        </ul>
        <div class="tab-content cms">
            <div role="tabpanel" id="main" class="tab-pane active">

                <?php if ($model->isNewRecord): ?>
                    <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;']) ?>
                <?php else: ?>
                    <?= $form->field($model, 'username')->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;', 'readonly' => 'readonly']) ?>
                    <?= Button::widget([
                        'label' => Yii::t('app', 'Change password'),
                        'encodeLabel' => false,
                        'options' => [
                            'class' => 'btn-link',
                            'type' => 'button',
                            'style' => 'margin:5px; margin-bottom: 10px;',
                            'data' => [
                                'toggle' => 'modal',
                                'target' => '#changePasswordModal',
                            ],
                        ],
                    ]); ?>
                <?php endif; ?>

                <?= $form->field($model, 'email')->textInput(['maxlength' => 255, 'style' => 'max-width: 400px;']) ?>

                <?php if ($model->isNewRecord): ?>
                    <?= $form->field($model, 'password')->passwordInput(['maxlength' => 40, 'style' => 'max-width: 400px;']) ?>

                    <?= $form->field($model, 'password_repeat')->passwordInput(['maxlength' => 40, 'style' => 'max-width: 400px;']) ?>
                <?php endif; ?>

                <?= $form->field($model, 'role')->dropDownList(['' => 'Выбрать роль...'] + User::getRoles(), ['style' => 'max-width: 400px;']); ?>

                <?= $form->field($model, 'status')->dropDownList(User::getStatuses(), ['style' => 'max-width: 400px;']); ?>
            </div>
            <div role="tabpanel" id="fio" class="tab-pane">

                <?= $form->field($model, 'last_name')->textInput(['maxlength' => 30, 'style' => 'max-width: 400px;']) ?>

                <?= $form->field($model, 'first_name')->textInput(['maxlength' => 30, 'style' => 'max-width: 400px;']) ?>

                <?= $form->field($model, 'middle_name')->textInput(['maxlength' => 30, 'style' => 'max-width: 400px;']) ?>
            </div>
        </div>
        <div style="padding-bottom: 5px;">&nbsp;</div>
        <div class="form-group btn-ctrl">
            <?php
            $class = 'default';
            if (Yii::$app->user->identity->role == User::ROLE_ADMIN):
                ?>
                <?= Html::submitButton(
                $model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Save'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'name' => 'saveUser']
            ) ?>
            <?php else:
                $class = 'primary';
            endif; ?>
            <?= Html::submitButton(
                Yii::t('app', 'Apply'),
                ['class' => 'btn btn-' . $class, 'name' => 'applyUser']
            ); ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<?php if ( !$model->isNewRecord): ?>
    <?php $changePasswordForm = ActiveForm::begin([
        'action' => Url::to(['/user/changepassword', 'id' => $model->id]),
        'method' => 'post',
    ]); ?>

    <?php Modal::begin([
        'id' => 'changePasswordModal',
        'header' => '<h2>' . Yii::t('app', 'Changing password') . '</h2>',
        'footer' => Button::widget([
            'label' => Yii::t('app', 'Save'),
            'encodeLabel' => false,
            'options' => [
                'class' => 'btn-success',
                'name' => 'joinGroup',
            ],
        ]),
    ]); ?>

    <?= $changePasswordForm->field($model, 'password')->passwordInput(['maxlength' => 40, 'style' => 'max-width: 100%;'])->label(Yii::t('app', 'New password')) ?>

    <?= $changePasswordForm->field($model, 'password_repeat')->passwordInput(['maxlength' => 40, 'style' => 'max-width: 100%;']) ?>

    <?php Modal::end(); ?>

    <?php ActiveForm::end(); ?>
<?php endif; ?>
