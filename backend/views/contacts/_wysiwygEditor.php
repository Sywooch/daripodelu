<?php

use yii\helpers\Html;
use mihaildev\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model common\models\ContactsItem */
/* @var $contactItems common\models\ContactsItem[] */
/* @var $form yii\widgets\ActiveForm */
?>

<?= $form->field($model, 'value')->widget(CKEditor::className(),[
    'editorOptions' => [
        'height' => 100,
        'toolbarGroups' => [
            ['name' => 'mode'],
            ['name' => 'undo'],
            ['name' => 'clipboard'],
            ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
            ['name' => 'links', 'groups' => ['links', 'insert']],
        ],
        'removeButtons' => 'Subscript,Superscript,Flash,Image,Anchor,Table,HorizontalRule,Smiley,SpecialChar,PageBreak,Iframe',
        'removePlugins' => 'elementspath',
        'resize_enabled' => false,
    ],
]); ?>