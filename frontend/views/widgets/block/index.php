<?php foreach ($blocks as $block): ?>
    <?php /* @var $block \backend\models\Block */ ?>
    <?= $block->content; ?>
<?php endforeach; ?>