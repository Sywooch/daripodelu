<?php

use yii\helpers\Html;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = $heading;
?>
<div class="row">
    <div class="col-10">
        <main class="main-content">
            <h1>Заказ оформлен!</h1>
            <p>Мы приняли ваш заказ. В ближайшее время наш оператор свяжется с вами по телефону <strong><?= Html::encode($orderInfo['phone']); ?></strong>, который вы указали при оформлении заказа, для его подтверждения.</p>
            <p>Подробная информация о заказе отправлена на электронную почту <strong><?= Html::encode($orderInfo['email']); ?></strong>.</p>
        </main>
    </div>
</div>