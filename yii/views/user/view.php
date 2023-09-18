<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Project;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
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

<div class="user-view">

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
            'username',
            // 'password',
            'name',
            'email:email',
            // 'role',
            [
                'attribute' => 'role',
                'value' => function ($model) {
                    switch ($model->role) {
                        case 1:
                            return 'Administrator';
                        case 2:
                            return 'Project Management';
                        case 3:
                            return 'Staff';
                        default:
                            return 'Unknown';
                    }
                },
            ],
            'description:ntext',
        ],
    ]) ?>

</div>

<div id="project-table-container">
    <table id="project-table" class="display">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Manager</th>
                <th>Description</th>
                <th>Create Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="project-table-body"></tbody>
    </table>
</div>


<?php
$js = <<<JS
$(document).ready(function() {
    $.ajax({
        url: '/user/list-projects-by-user?id=' + $model->id,
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            updateProjectTable(data);
        },
        error: function(xhr, status, error) {
            console.error('API Error:', error);
        }
    });

    function updateProjectTable(projectsData) {
        var tableBody = $('#project-table-body');
        
        tableBody.empty();

        projectsData.forEach(function(project) {
            var row = '<tr>' +
                '<td>' + project.id + '</td>' +
                '<td>' + project.name + '</td>' +
                '<td>' + project.username + '</td>' +
                '<td>' + project.description + '</td>' +
                '<td>' + project.createDate + '</td>' +
                '<td><a href="/project/view?id=' + project.id + '"><span class="glyphicon glyphicon-eye-open"></span> Detail project</a></td>' +
                '</tr>';

            tableBody.append(row);
        });
    }
});
JS;

$this->registerJs($js);
?>



