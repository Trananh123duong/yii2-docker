<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Project $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Projects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>

<style>
    table {
        border-spacing: 10px;
        width: 100%;
    }
    table, th, td {
        border: 1px solid black;
    }
    th, td {
        padding: 10px;
    }

</style>

<div class="project-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [
                'attribute' =>'manager',
                'value' => function ($model) {
                    return $model->projectManager->username;
                }
            ],
            'description:ntext',
            'createDate',
            'updateDate',
        ],
    ]) ?>

</div>

<div id="user-table-container">
    <table id="staff-table" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="staff-table-body"></tbody>
    </table>
</div>


<?php
$js = <<<JS
$(document).ready(function() {
    $.ajax({
        url: '/project/list-users-by-project?id=' + $model->id,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            updateStaffTable(data);
        },
        error: function(xhr, status, error) {
            console.error('API Error:', error);
        }
    });

    function updateStaffTable(staffsData) {
        var tableBody = $('#staff-table-body');
        
        tableBody.empty();

        staffsData.forEach(function(staff) {
            var row = '<tr>' +
                '<td>' + staff.id + '</td>' +
                '<td>' + staff.username + '</td>' +
                '<td>' + staff.email + '</td>' +
                '<td>' + staff.role + '</td>' +
                '<td><a href="/user/view?id=' + staff.id + '"><span class="glyphicon glyphicon-eye-open"></span> Detail user</a></td>' +
                '</tr>';

            tableBody.append(row);
        });
    }
});
JS;

$this->registerJs($js);
?>
