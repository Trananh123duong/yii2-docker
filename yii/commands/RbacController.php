<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\rbac\DbManager;
use app\models\User;

class RbacController extends Controller
{
    public function actionInit()
    {
        $auth = Yii::$app->authManager;

        // Tạo các vai trò
        $administrator = $auth->createRole('administrator');
        $projectManagement = $auth->createRole('projectManagement');
        $staff = $auth->createRole('staff');

        // Thêm các vai trò vào hệ thống
        $auth->add($administrator);
        $auth->add($projectManagement);
        $auth->add($staff);

        // Tạo các quyền
        $detailProject = $auth->createPermission('detailProject');
        $indexProject = $auth->createPermission('indexProject');
        $createProject = $auth->createPermission('createProject');
        $updateProject = $auth->createPermission('updateProject');
        $deleteProject = $auth->createPermission('deleteProject');

        $detailUser = $auth->createPermission('detailUser');
        $indexUser = $auth->createPermission('indexUser');
        $createUser = $auth->createPermission('createUser');
        $updateUser = $auth->createPermission('updateUser');
        $deleteUser = $auth->createPermission('deleteUser');

        $updatePassword = $auth->createPermission('updatePassword');
        
        $statistics = $auth->createPermission('statistics');

        // Thêm các quyền vào hệ thống
        $auth->add($detailProject);
        $auth->add($indexProject);
        $auth->add($createProject);
        $auth->add($updateProject);
        $auth->add($deleteProject);

        $auth->add($detailUser);
        $auth->add($indexUser);
        $auth->add($createUser);
        $auth->add($updateUser);
        $auth->add($deleteUser);

        $auth->add($updatePassword);

        $auth->add($statistics);

        // Gán quyền cho các vai trò
        $auth->addChild($administrator, $detailProject);
        $auth->addChild($administrator, $indexProject);
        $auth->addChild($administrator, $createProject);
        $auth->addChild($administrator, $updateProject);
        $auth->addChild($administrator, $deleteProject);

        $auth->addChild($administrator, $detailUser);
        $auth->addChild($administrator, $indexUser);
        $auth->addChild($administrator, $createUser);
        $auth->addChild($administrator, $updateUser);
        $auth->addChild($administrator, $deleteUser);

        $auth->addChild($administrator, $updatePassword);
        $auth->addChild($administrator, $statistics);


        $auth->addChild($projectManagement, $detailProject);
        $auth->addChild($projectManagement, $indexProject);
        $auth->addChild($projectManagement, $createProject);
        $auth->addChild($projectManagement, $updateProject);
        $auth->addChild($projectManagement, $deleteProject);

        $auth->addChild($projectManagement, $updatePassword);


        $auth->addChild($staff, $detailProject);
        $auth->addChild($staff, $detailUser);
        $auth->addChild($staff, $updateUser);

        $usersWithAdminRole = User::find()->where(['role' => 1])->all();
        foreach ($usersWithAdminRole as $user) {
            $auth->assign($administrator, $user->id);
        }

        $usersWithProjectManagerRole = User::find()->where(['role' => 2])->all();
        foreach ($usersWithProjectManagerRole as $user) {
            $auth->assign($projectManagement, $user->id);
        }

        $usersWithStaffRole = User::find()->where(['role' => 3])->all();
        foreach ($usersWithStaffRole as $user) {
            $auth->assign($staff, $user->id);
        }
        
        echo "RBAC roles and permissions have been initialized.\n";
    }
}
