<?php

use yii;
use frontend\widgets\BlockWidget;
use frontend\widgets\CatalogueWidget;
/* @var $this yii\web\View */

?>
<div class="bd-box">
    <div class="container">
        <section class="main-ctg-list-box">
            <h2 class="h1">Выбери лучшее предложение</h2>

                <?= CatalogueWidget::widget(['template' => 'index']); ?>
            </div>
            <div class="clear"></div>
        </section>
    </div>
</div>
<div class="infblock-3-box">
    <div class="container">
        <div class="inf-col inf-col-1">
            <section class="about-inf-block">
                <?= BlockWidget::widget(['position' => 'main_center_left']) ?>
            </section>
        </div>
        <div class="inf-col inf-col-2">
            <section class="articles-inf-block">
                <h2>Полезные статьи</h2>
                <ul class="uf-articles-list">
                    <li>
                        <a href="#">«Подрывники» проморынка. Как бороться с теми, кто играет не по правилам<span class="panel"></span></a>
                    </li>
                    <li>
                        <a href="#">Где же кружка? Cамый эффективный промоинструмент в полном цвете<span class="panel"></span></a>
                    </li>
                    <li>
                        <a href="#">Печать на силиконе? Да!<span class="panel"></span></a>
                    </li>
                </ul>
                <a class="more" href="#"><span>Еще!</span></a>
            </section>
        </div>
        <div class="inf-col inf-col-3">
            <section class="news-inf-block">
                <h2>Новости</h2>
                <ul class="news-list-box">
                    <li>
                        <time datetime="2015-05-22">22 мая</time>
                        <a href="#">Тест-драйв: подводим итоги!<span class="panel"></span></a>
                    </li>
                    <li>
                        <time datetime="2015-05-15">15 мая</time>
                        <a href="#">Новинки июня. Часть II<span class="panel"></span></a>
                    </li>
                    <li>
                        <time datetime="2015-05-14">14 мая</time>
                        <a href="#">Толстой и мир. «Кенгуру» и боксер<span class="panel"></span></a>
                    </li>
                    <li>
                        <time datetime="2015-05-13">13 мая</time>
                        <a href="#">Тест-драйв: подводим итоги!<span class="panel"></span></a>
                    </li>
                    <li>
                        <time datetime="2015-05-12">12 мая</time>
                        <a href="#">Новинки июня. Часть II<span class="panel"></span></a>
                    </li>
                    <li>
                        <time datetime="2015-05-04">04 мая</time>
                        <a href="#">Толстой и мир. «Кенгуру» и боксер<span class="panel"></span></a>
                    </li>
                </ul>
            </section>
        </div>
        <div class="clear"></div>
    </div>
</div>