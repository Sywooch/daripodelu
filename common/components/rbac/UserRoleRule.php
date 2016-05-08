<?php


namespace common\components\rbac;

use Yii;
use yii\rbac\Rule;
use common\models\User;

class UserRoleRule extends Rule
{
    public $name = 'userRole';

    public function execute($user, $item, $params)
    {
        if ( ! Yii::$app->user->isGuest)
        {
            $role = Yii::$app->user->identity->role;
            if ($item->name === User::getRoleStringId(User::ROLE_ADMIN))
            {
                return $role == User::ROLE_ADMIN;
            }
            elseif ($item->name === User::getRoleStringId(User::ROLE_MODERATOR))
            {
                //moderator является потомком admin, который получает его права
                return $role == User::ROLE_ADMIN || $role == User::ROLE_MODERATOR;
            }
        }

        return true;
    }
}