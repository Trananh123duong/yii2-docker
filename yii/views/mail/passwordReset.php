<?php

/* @var $this yii\web\View */
/* @var $user app\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(['user/password-reset', 'token' => $user->password_reset_token]);
?>

<div class="password-reset">
    <p>Xin chào <?= $user->username ?>,</p>

    <p>Bạn đã yêu cầu đặt lại mật khẩu. Vui lòng nhấp vào liên kết dưới đây để đặt lại mật khẩu:</p>

    <p><?= $resetLink ?></p>

</div>
