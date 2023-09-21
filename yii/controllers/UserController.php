<?php

namespace app\controllers;

use app\models\User;
use app\models\search\UserSearch;
use app\models\ProjectStaff;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Yii;
use yii\db\Query;
use yii\web\ForbiddenHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        if ($this->request->get('isApi') == true) {
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return [
                'status' => true,
                'dataProvider' => $dataProvider->getModels(), // Lấy mảng các dòng dữ liệu
            ];
        }

        if (Yii::$app->user->can('indexUser')) {
            return $this->render('index', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to list user.');
        }
    }


    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    // public function actionView($id)
    // {
    //     $model = $this->findModel($id);
    //     // if (Yii::$app->user->can('detailUser') || (Yii::$app->user->identity->role == 3 && Yii::$app->user->id == $model->id)) {
    //     if (Yii::$app->user->can('detailUser')) {
    //         if (Yii::$app->user->identity->role == 3) {
    //             if (Yii::$app->user->id == $model->id) {
    //                 return $this->render('view', [
    //                     'model' => $model,
    //                 ]);
    //             } else {
    //                 throw new ForbiddenHttpException('You do not have permission to detail a user.');
    //             }
    //         }
    //         return $this->render('view', [
    //             'model' => $model,
    //         ]);
    //     } else {
    //         throw new ForbiddenHttpException('You do not have permission to detail a user.');
    //     }
    // }
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $user = Yii::$app->user->identity;

        if (($user->role < 3) || ($user->role == 3 && $user->id == $model->id)) {
            return $this->render('view', [
                'model' => $model,
            ]);
        }

        throw new ForbiddenHttpException('You do not have permission to detail this user.');
    }

    public function actionListProjectsByUser($id)
    {
        $user = new Query();

        $user->from('project_staff')
            ->select(['project.id', 'project.name', 'project.description', 'project.createDate', 'user.username'])
            ->leftJoin('project', 'project.id = project_staff.projectId')
            ->innerJoin('user', 'project.projectManagerId = user.id')
            ->where(['project_staff.userId' => $id]);

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $user->all();
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        if (Yii::$app->user->can('createUser')) {
            $model = new User();

            if ($this->request->isPost) {
                if ($model->load($this->request->post()) && $model->save()) {
                    $model->assignRoleBasedOnRoleAttribute();
                    return $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                $model->loadDefaultValues();
            }

            return $this->render('create', [
                'model' => $model,
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to create a user.');
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->can('updateUser')) {
            $model = $this->findModel($id);

            if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }

            return $this->render('update', [
                'model' => $model,
            ]);
        } else {
            throw new ForbiddenHttpException('You do not have permission to update a user.');
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->can('deleteUser')) {
            $this->findModel($id)->delete();

            return $this->redirect(['index']);
        } else {
            throw new ForbiddenHttpException('You do not have permission to delete a user.');
        }
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionForgotPassword()
    {
        $model = new User();
        if ($model->load(Yii::$app->request->post())) {
            // Tìm người dùng dựa trên địa chỉ email
            $user = User::findOne(['email' => $model->email]);
            
            if ($user) {
                $user->generatePasswordResetToken();
                
                if ($user->save()) {
                    Yii::$app->mailer->htmlLayout = '@app/views/layouts/email';
                    // Gửi email đặt lại mật khẩu cho người dùng cụ thể
                    Yii::$app->mailer->compose(['html' => '@app/views/mail/passwordReset'], ['user' => $user])
                        ->setFrom('trananh123duong@gmail.com')
                        ->setTo($user->email)
                        ->setSubject('Đặt lại mật khẩu')
                        ->send();

                    Yii::$app->session->setFlash('success', 'Hãy kiểm tra email của bạn để đặt lại mật khẩu.');
                    return $this->redirect(['site/login']);
                }
            }
            // Hiển thị thông báo nếu địa chỉ email không tồn tại
            Yii::$app->session->setFlash('error', 'Không tìm thấy người dùng với địa chỉ email này.');
        }
        return $this->render('forgotPassword', ['model' => $model]);
    }

    public function actionPasswordReset($token)
    {
        $model = User::findByPasswordResetToken($token);

        if (!$model) {
            throw new NotFoundHttpException('Liên kết đặt lại mật khẩu không hợp lệ.');
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            // Xóa mã đặt lại mật khẩu sau khi đã sử dụng nó
            $model->password_reset_token = null;
            $model->save();

            Yii::$app->session->setFlash('success', 'Mật khẩu đã được đặt lại thành công.');
            return $this->redirect(['site/login']);
        }

        return $this->render('passwordReset', ['model' => $model]);
    }
}
