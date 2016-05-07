<?php


namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\components\rbac\UserRoleRule;
use common\components\rbac\DefaultPermissions;
use common\models\User;


class RbacController extends Controller
{
    public function actionInit()
    {
        $authManager = Yii::$app->authManager;
        $authManager->removeAll();

        //Create roles
        $guest = $authManager->createRole(User::getRoleStringId(User::ROLE_GUEST));
        $moderator = $authManager->createRole(User::getRoleStringId(User::ROLE_MODERATOR));
        $admin = $authManager->createRole(User::getRoleStringId(User::ROLE_ADMIN));

        // Create simple, based on action{$NAME} permissions
        $login  = $authManager->createPermission(DefaultPermissions::LOGIN);
        $logout = $authManager->createPermission(DefaultPermissions::LOGOUT);
        $error  = $authManager->createPermission(DefaultPermissions::ERROR);
        $signUp = $authManager->createPermission(DefaultPermissions::SIGNUP);
        $index  = $authManager->createPermission(DefaultPermissions::INDEX);
        $view   = $authManager->createPermission(DefaultPermissions::VIEW);
        $update = $authManager->createPermission(DefaultPermissions::UPDATE);
        $delete = $authManager->createPermission(DefaultPermissions::DELETE);

        // Add permissions in Yii::$app->authManager
        $authManager->add($login);
        $authManager->add($logout);
        $authManager->add($error);
        $authManager->add($signUp);
        $authManager->add($index);
        $authManager->add($view);
        $authManager->add($update);
        $authManager->add($delete);


        // Add rule, based on UserExt->role === $user->role
        $userRoleRule = new UserRoleRule();
        $authManager->add($userRoleRule);

        // Add rule "UserRoleRule" in roles
        $guest->ruleName  = $userRoleRule->name;
        $moderator->ruleName  = $userRoleRule->name;
        $admin->ruleName  = $userRoleRule->name;

        // Add roles in Yii::$app->authManager
        $authManager->add($guest);
        $authManager->add($moderator);
        $authManager->add($admin);

        // Add permission-per-role in Yii::$app->authManager
        // Guest
        $authManager->addChild($guest, $login);
        $authManager->addChild($guest, $logout);
        $authManager->addChild($guest, $error);
        $authManager->addChild($guest, $signUp);
        $authManager->addChild($guest, $index);
        $authManager->addChild($guest, $view);

        // Moderator
        $authManager->addChild($moderator, $update);
        $authManager->addChild($moderator, $guest);


        // Admin
        $authManager->addChild($admin, $delete);
        $authManager->addChild($admin, $moderator);
    }
}