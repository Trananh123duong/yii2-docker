<?php

namespace app\rbac;

use yii\rbac\Rule;

class StaffRule extends Rule
{
    public $name = 'isStaff';

    public function execute($user, $item, $params)
    {
        // Kiểm tra xem người dùng có role là "staff" không
        return isset($params['user']) ? $params['user']->role == 3 : false;
    }
}
