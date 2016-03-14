<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use frontend\widgets\BlockWidget;

/* @var $feedbackModel frontend\models\FeedbackForm */

$feedbackModel = $this->params['feedbackModel'];
?>
<?php $this->beginContent('@app/views/layouts/base.php'); ?>
    <div class="menu-line"></div>
    <div class="main-wrapper">
        <header class="main-header">
            <div class="container">
                <div class="row">
                    <nav class="main-nav">
                        <ul class="nav-catalogue">
                            <li>
                                <a href="<?= Url::to(['catalogue/index']) ?>">Каталог продукции</a>
                            </li>
                        </ul>
                        <ul class="shop-cart">
                            <li>
                                <a href="<?= Url::to(['cart/index']) ?>">Корзина:  <span class="cart-total-price"><?= yii::$app->cart->getTotalPrice(); ?></span> руб.</a>
                            </li>
                        </ul>
                        <ul class="main-menu inl-blck">
                            <li>
                                <a href="<?= Url::to(['page/view', 'id' => 1]); ?>">Методы нанесения</a>
                            </li>
                            <li>
                                <a href="<?= Url::to(['page/view', 'id' => 2]); ?>">«Дари по делу»</a>
                            </li>
                            <li>
                                <a href="#">Контактная информация</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="container" itemscope itemtype="http://schema.org/Organization">
                <div class="row">
                    <div class="col-4">
                        <div class="phone-box">
                            <span class="phone block" itemprop="telephone"><?= yii::$app->config->sitePhone; ?></span>
                            <span class="schedule block" itemprop="openingHours"><?= yii::$app->config->siteWorkSchedule; ?></span>
                        </div>
                    </div>
                    <div class="col-2">
                        <div class="logo-box">
                            <a class="logo" href="/"><img src="/img/logo-min.png" alt="Дариподелу"></a>
                            <span class="tagline block">Бизнес-подарки на любой вкус</span>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="hd-line"></div>
        <div class="bd-box">
            <div class="container">
                <div class="row">
                    <?= $content ?>
                </div>
            </div>
        </div>
    </div>
    <div class="before-ft-lines"></div>
    <div class="footer-box">
        <div class="container">
            <div class="contacts-box">
                <span class="question">Есть к нам вопросы?</span>
                <div class="phone-box">
                    <span class="offer block">Просто позвоните нам:</span>
                    <span class="phone block"><?= yii::$app->config->sitePhone; ?></span>
                    <span class="schedule block"><?= yii::$app->config->siteWorkSchedule; ?></span>
                </div>
                <span class="offer block">или напишите:</span>
                <div class="feedback-form-box">
                    <?php $form = ActiveForm::begin([
                        'action' => Url::to(['site/sendmail']), 'id' => 'mail-form',
                        'enableAjaxValidation' => false,
                        'enableClientValidation' => true,
                    ]); ?>
                    <?= $form->field($feedbackModel, 'emailPhone', [
                        'template' => '<div class="field-box">{error}{input}</div>',
                        'inputOptions' => [
                            'placeholder' => $feedbackModel->getAttributeLabel('emailPhone'),
                        ],
                    ]); ?>
                    <?= $form->field($feedbackModel, 'message', [
                        'template' => '<div class="field-box textarea-box">{error}{input}</div>',
                        'inputOptions' => [
                            'placeholder' => $feedbackModel->getAttributeLabel('message'),
                        ],
                    ])->textarea(); ?>
                    <div class="btn-group">
                        <?= Html::submitButton('Задать вопрос', ['class' => 'btn btn-default', 'id' => 'send-button', 'name' => 'send-button']) ?>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
        <div class="ft-lines"></div>
        <footer class="container">
            <div class="copyright"><?= BlockWidget::widget(['position' => 'footer']) ?></div>
        </footer>
    </div>
<?php $this->endContent(); ?>
