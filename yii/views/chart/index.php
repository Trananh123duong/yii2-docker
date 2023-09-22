<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\web\JsExpression;

$this->title = 'Diff Column Chart';

// Register the Google Charts library
$this->registerJsFile('https://www.gstatic.com/charts/loader.js', ['position' => \yii\web\View::POS_HEAD]);
$this->registerJs('google.charts.load("current", {packages:["corechart"]});');

$startDatePickerID = 'start-date-picker';
$endDatePickerID = 'end-date-picker';

// Register JavaScript for datepickers
$this->registerJs(<<<JS
    var startDatePicker = $('#$startDatePickerID');
    var endDatePicker = $('#$endDatePickerID');

    startDatePicker.datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        onSelect: function(selectedDate) {
            endDatePicker.datepicker('option', 'minDate', selectedDate);
        }
    });

    endDatePicker.datepicker({
        dateFormat: 'yy-mm-dd',
        changeMonth: true,
        changeYear: true,
        onSelect: function(selectedDate) {
            startDatePicker.datepicker('option', 'maxDate', selectedDate);
        }
    });
JS
);
?>

<div class="site-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-md-4">
            <?php $form = ActiveForm::begin(['id' => 'date-range-form', 'method' => 'get']); ?>

            <h4>Select Date Range:</h4>

            <?= $form->field($searchModel, 'startDate')->widget(DatePicker::classname(), [
                'options' => ['class' => 'form-control', 'id' => $startDatePickerID],
                'clientOptions' => [
                    'dateFormat' => 'yy-mm-dd',
                    'changeMonth' => true,
                    'changeYear' => true,
                ],
            ])->label(false); ?>

            <?= $form->field($searchModel, 'endDate')->widget(DatePicker::classname(), [
                'options' => ['class' => 'form-control', 'id' => $endDatePickerID],
                'clientOptions' => [
                    'dateFormat' => 'yy-mm-dd',
                    'changeMonth' => true,
                    'changeYear' => true,
                ],
            ])->label(false); ?>

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary', 'id' => 'search-button']) ?>
                <?= Html::a('Reset', ['chart/index'], ['class' => 'btn btn-default']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <div class="col-md-8">
            <div id="chart_div" style="width: 100%; height: 400px;"></div>
        </div>
    </div>
</div>

<?php
$getDataUrl = Yii::$app->urlManager->createUrl(['/chart/get-data']);

// Register JavaScript for drawing the chart
$this->registerJs(<<<JS
    function drawChart() {
        var startDate = $('#$startDatePickerID').val();
        var endDate = $('#$endDatePickerID').val();

        $.ajax({
            url: '$getDataUrl',
            method: 'GET',
            data: { startDate: startDate, endDate: endDate },
            dataType: 'json',
            success: function(data) {
                var chartData = new google.visualization.DataTable();
                chartData.addColumn('string', 'User');
                chartData.addColumn('number', 'Total Projects');

                var chartRows = [];
                data.forEach(function(item) {
                    chartRows.push([item.user, parseInt(item.total_projects)]);
                });
                chartData.addRows(chartRows);

                var options = {
                    title: 'Project Count by User',
                    hAxis: { title: 'User' },
                    vAxis: { title: 'Total Projects' },
                    isStacked: true
                };

                var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));

                chart.draw(chartData, options);
            },
            error: function() {
                console.log('Error fetching data');
            }
        });
    }

    // Gọi hàm drawChart khi ấn nút "Search"
    $('#search-button').click(function(e) {
        e.preventDefault(); // Ngăn chặn hành động mặc định của nút "Submit"
        drawChart();
    });

    google.charts.setOnLoadCallback(drawChart);
JS
);
