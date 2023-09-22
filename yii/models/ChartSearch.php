<?php

namespace app\models;

use yii\base\Model;

class ChartSearch extends Model
{
    public $startDate;
    public $endDate;

    public function rules()
    {
        return [
            [['startDate', 'endDate'], 'date', 'format' => 'yyyy-MM-dd'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
        ];
    }
}
