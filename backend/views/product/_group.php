<?php

use yii\bootstrap\Button;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model backend\models\Product */
/* @var $products backend\models\Product[] */

$productsWithGroup = [];
foreach ($products as $product)
{
    if ($product->group_id == null)
    {
        $productsWithGroup[$product->id] = $product->name;
    }
}
?>
<?php if ($model->group_id != null): ?>
    <?= Button::widget([
        'label' => '<i class="glyphicon glyphicon-log-out"></i> ' . Yii::t('app', 'Leave the group'),
        'encodeLabel' => false,
        'options' => [
            'class' => 'btn-danger btn-sm pull-right',
            'href' => Url::to(['/product/leavegroup', 'id' => $model->id, 'tabNumber' => 7]),
            'style' => 'margin:5px; margin-bottom: 10px;',
        ],
        'tagName' => 'a',
    ]); ?>
<?php else: ?>
    <?php Modal::begin([
        'header' => '<h2>' . Yii::t('app', 'Create a group') . '</h2>',
        'toggleButton' => [
            'label' => '<i class="glyphicon glyphicon-plus"></i> ' . ($model->group_id != null ? Yii::t('app', 'Change group') : Yii::t('app', 'Create a group')),
            'class' => 'btn-link btn-sm btn pull-right',
            'style' => 'margin:5px; margin-bottom: 10px;',
        ],
        'footer' => Button::widget([
            'label' => Yii::t('app', 'Create'),
            'encodeLabel' => false,
            'options' => [
                'class' => 'btn-success',
//                'type' => 'submit',
                'name' => 'createGroup',
                'onclick' => 'return sendIdsForGrouping("abc", "' . Url::to(['/product/creategroup', 'id' => $model->id, 'tabNumber' => 7]) . '");',
            ],
        ]),
    ]); ?>
    <form id="abc" action="<?= Url::to(['/product/creategroup', 'id' => $model->id, 'tabNumber' => 7]); ?>" method="post">
    <?= $form->field($model, 'groupProductIds')
        ->widget(Select2::className(), [
            'data' => $productsWithGroup,
            'options' => [
                'multiple' => true,
            ],
            'pluginOptions' => [
                'width' => '100%'
            ],
        ])
        ->label('Товары в группе')->hint(
            Yii::t('app', '<strong>Note:</strong>') . ' ' .
            Yii::t('app', 'Selected items will be combined into a group')
        ); ?>
    </form>
    <?php Modal::end(); ?>
<?php endif; ?>
<?php Modal::begin([
    'header' => '<h2>' . ($model->group_id != null ? Yii::t('app', 'Change group') : Yii::t('app', 'Join the group')) . '</h2>',
    'toggleButton' => [
        'label' => '<i class="glyphicon glyphicon-log-in"></i> ' . ($model->group_id != null ? Yii::t('app', 'Change group') : Yii::t('app', 'Join the group')),
        'class' => 'btn-success btn-sm btn pull-right',
        'style' => 'margin:5px; margin-bottom: 10px;',
    ],
]); ?>
<?php Modal::end(); ?>
<?php $this->registerJs('
    function sendIdsForGrouping(formId, actionLink)
    {
        var form = $("#" + formId);

        if (form.length > 0)
        {
            $.post(
                form.attr("action"),
                form.serialize()
            );

            return true;
        }

        return false;
    }
', \yii\web\View::POS_END); ?>