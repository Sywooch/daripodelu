<?php

use yii;
use yii\helpers\Html;

/* @var $mail frontend\models\FeedbackForm */
?>
<table border="0" cellpadding="0" cellspacing="0" width="99%" style="background: white;">
    <tbody>
    <tr style="vertical-align:top;text-align:left;padding:0" align="left">
        <td bgcolor="white" style="font-family: Helvetica, Arial, sans-serif; font-size: 1.167em; padding: 30px 20px 20px 20px;">
            <p style="font-size: 1.5em; margin: 0; padding: 0 0 25px 0;">Здравствуйте!</p>
            <p style="margin: 0; padding: 0 0 15px 0;">С сайта "<?= yii::$app->config->siteName; ?>" поступило письмо.</p>
            <p style="margin: 0; padding: 0 0 0 0;">---------------------------------------------------------------------</p>
            <p style="margin: 0; padding: 3px 0 2px 0;">ТЕКСТ ПИСЬМА</p>
            <p style="margin: 0; padding: 0 0 20px 0;">---------------------------------------------------------------------</p>
            <p style="margin: 0; padding: 0 0 0 0;"><?= Html::encode($mail->message); ?></p>
            <p style="margin: 0; padding: 30px 0 0 0;">---------------------------------------------------------------------</p>
            <p style="margin: 0; padding: 3px 0 2px 0;">ИНФОРМАЦИЯ ОБ ОТПРАВИТЕЛЕ</p>
            <p style="margin: 0; padding: 0 0 5px 0;">---------------------------------------------------------------------</p>
            <p style="margin: 0; padding: 0 0 15px 0;"><b>E-mail или номер телефона:</b> <?= Html::encode($mail->emailPhone); ?></p>
            <p style="margin: 0; padding: 0 0 15px 0;">---------------------------------------------------------------------</p>
        </td>
    </tr>
    </tbody>
</table>