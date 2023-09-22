<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\User;
use app\models\Project;
use app\models\ProjectStaff;
use app\models\ChartSearch;
use yii\db\Query;
use yii\web\ForbiddenHttpException;

class ChartController extends Controller
{
    public function actionIndex()
    {
        if (Yii::$app->user->can('createProject')) {
            $searchModel = new ChartSearch();
        
            return $this->render('index', [
                'searchModel' => $searchModel,
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to chart.');
        }
    }

    public function actionGetData($startDate = null, $endDate = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $data = [];

        // Lấy danh sách tất cả người dùng một lần
        $users = User::find()->all();

        foreach ($users as $user) {
            // Tạo một truy vấn dự án cho người dùng hiện tại
            $projectQuery = Project::find()
                ->alias('p')
                ->innerJoin('project_staff ps', 'p.id = ps.projectId')
                ->where(['ps.userId' => $user->id]);

            if ($startDate && $endDate) {
                $projectQuery->andWhere(['between', 'p.createDate', $startDate, $endDate]);
            }

            $projectCount = $projectQuery->count();

            $data[] =  [
                'user' => $user->name,
                'total_projects' => $projectCount
            ];
        }

        return $data;
    }
}
