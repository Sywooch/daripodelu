<?php foreach ($blocks as $block): ?>
    <?php /* @var $block \backend\models\Block */ ?>
    <div class="ctg-inf-box">
        <?= $block->content; ?>
    </div>
<?php endforeach; ?>