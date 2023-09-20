<?php

namespace app\controllers;

use app\models\Project;
use app\models\search\ProjectSearch;
use app\models\ProjectStaff;
use app\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Query;
use Yii;
use yii\web\ForbiddenHttpException;

/**
 * ProjectController implements the CRUD actions for Project model.
 */
class ProjectController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Project models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Yii::$app->user->can('indexProject')) {
            $searchModel = new ProjectSearch();
            $dataProvider = $searchModel->search($this->request->queryParams);

            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to list project.');
        }
    }

    /**
     * Displays a single Project model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (Yii::$app->user->can('detailProject')) {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to detail a project.');
        }
    }

    public function actionListUsersByProject($id)
    {
        $user = new Query();

        $user->from('project_staff')
            ->select(['user.id', 'user.username', 'user.email', 'user.role'])
            ->leftJoin('user', 'user.id = project_staff.userId')
            ->where(['project_staff.projectId' => $id]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $user->all();
    }

    /**
     * Creates a new Project model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (Yii::$app->user->can('createProject')) {
            $model = new Project();

            if ($this->request->isPost) {
                if ($model->load($this->request->post()) && $model->save()) {
                    // Lấy danh sách người dùng được chọn
                    // $selectedUserIds = $this->request->post('selectedUserIds', []);
                    $selectedStaffIds = $this->request->post('Project')['staffIds'];

                    if (!empty($selectedStaffIds)) {
                        foreach ($selectedStaffIds as $staffId) {
                            // Tạo một bản ghi StaffProject cho mỗi staff được chọn
                            $staffProject = new ProjectStaff();
                            $staffProject->userId = $staffId;
                            $staffProject->projectId = $model->id;
                            $staffProject->save();
                        }
                    }

                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                $model->loadDefaultValues();
            }

            // Lấy danh sách người dùng để hiển thị trong form
            $users = User::find()->all();

            return $this->render('create', [
                'model' => $model,
                'users' => $users,
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to create a project.');
        }
    }

    /**
     * Updates an existing Project model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->can('updateProject')) {
            $model = $this->findModel($id);

            if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
                // Lấy danh sách người dùng được chọn
                $selectedStaffIds = $this->request->post('Project')['staffIds'];
                if (!empty($selectedStaffIds)) {
                    // Xóa các bản ghi ProjectStaff cũ cho dự án này
                    ProjectStaff::deleteAll(['projectId' => $model->id]);

                    // Tạo bản ghi cho mỗi người dùng trong bảng ProjectStaff
                    foreach ($selectedStaffIds as $staffId) {
                        $projectStaff = new ProjectStaff();
                        $projectStaff->projectId = $model->id;
                        $projectStaff->userId = $staffId;
                        $projectStaff->save();
                    }
                }
            
                return $this->redirect(['view', 'id' => $model->id]);
            }

            // Lấy danh sách người dùng để hiển thị trong form
            $users = User::find()->all();

            // Lấy danh sách người dùng đã tham gia dự án
            $selectedUserIds = ProjectStaff::find()->select('userId')->where(['projectId' => $model->id])->column();

            return $this->render('update', [
                'model' => $model,
                'users' => $users,
                'selectedUserIds' => $selectedUserIds,
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to update a project.');
        }
    }

    /**
     * Deletes an existing Project model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->can('deleteProject')) {
            $this->findModel($id)->delete();

            return $this->redirect(['index']);
        } else {
            throw new ForbiddenHttpException('You do not have permission to delete a project.');
        }
    }

    /**
     * Finds the Project model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Project the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Project::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}