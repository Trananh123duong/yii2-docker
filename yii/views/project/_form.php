<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/** @var app\models\Project $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="project-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?php
    // Kiểm tra xem có phải đang cập nhật dự án hay không
    if (!$model->isNewRecord) {
        // Nếu đang cập nhật, hiển thị tên người quản lý hiện tại của dự án
        echo $form->field($model, 'oldProjectManager')->textInput(['value' => $model->projectManager->name, 'disabled' => true]);
        // Tạo một dropdownlist để chọn người quản lý mới (nếu cần)
        echo $form->field($model, 'projectManagerId')->dropDownList([], ['prompt' => 'Select a new project manager']);
    } else {
        // Nếu đang tạo mới, sử dụng dropdownlist như trước
        echo $form->field($model, 'projectManagerId')->dropDownList([], ['prompt' => 'Select a project manager']);
    }
    ?>

    <?= $form->field($model, 'staffIds')->checkboxList(
        \yii\helpers\ArrayHelper::map($users, 'id', 'username'),
        // ['separator' => '<br>']
        [
            'separator' => '<br>',
            'item' => function ($index, $label, $name, $checked, $value) use ($selectedUserIds) {
                $checked = in_array($value, $selectedUserIds) ? ['checked' => 'checked'] : [];
                return '<label><input type="checkbox" name="' . $name . '" value="' . $value . '" ' . Html::renderTagAttributes($checked) . '> ' . $label . '</label>';
            },
        ]
    
    ) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js = <<<JS
// Xây dựng dropdownlist ban đầu
var options = '<option value=\"\">Select a project manager</option>';
    $('#project-projectmanagerid').html(options);

    // Gọi API để cập nhật giá trị của dropdownlist
    $.ajax({
        url: '/user?isApi=true',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            if (data.status === true) {
                $.each(data.dataProvider, function(index, user) {
                    options += '<option value=\"' + user.id + '\">' + user.name + '</option>';
                });
                $('#project-projectmanagerid').html(options);
                // Đồng thời cập nhật dropdownlist cho việc chọn người quản lý mới
                $('#project-newprojectmanagerid').html(options);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error: ' + status + ' - ' + error);
        }
    });
JS;
$this->registerJs($js);
?>