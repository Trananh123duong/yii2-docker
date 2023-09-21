<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\search\UserSearch $model */
/** @var yii\widgets\ActiveForm $form */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

<div class="form-group">
    <?= Html::submitButton('Gửi Email Đặt Lại Mật Khẩu', ['class' => 'btn btn-primary']) ?>
</div>

<?php ActiveForm::end(); ?>
