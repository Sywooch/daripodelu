<?php


namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\components\rbac\UserRoleRule;
use common\components\rbac\UserProfileOwnerRule;
use common\components\rbac\ArticlePermissions;
use common\components\rbac\BlockPermissions;
use common\components\rbac\CataloguePermissions;
use common\components\rbac\ContactsPermissions;
use common\components\rbac\DefaultPermissions;
use common\components\rbac\ImagePermissions;
use common\components\rbac\MenuPermissions;
use common\components\rbac\NewsPermissions;
use common\components\rbac\OrderPermissions;
use common\components\rbac\PagePermissions;
use common\components\rbac\ProductPermissions;
use common\components\rbac\SettingsPermissions;
use common\components\rbac\UserPermissions;
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

        $articleIndex = $authManager->createPermission(ArticlePermissions::INDEX);
        $articleView = $authManager->createPermission(ArticlePermissions::VIEW);
        $articleCreate = $authManager->createPermission(ArticlePermissions::CREATE);
        $articleUpdate = $authManager->createPermission(ArticlePermissions::UPDATE);
        $articleDelete = $authManager->createPermission(ArticlePermissions::DELETE);

        $blockIndex = $authManager->createPermission(BlockPermissions::INDEX);
        $blockView = $authManager->createPermission(BlockPermissions::VIEW);
        $blockCreate = $authManager->createPermission(BlockPermissions::CREATE);
        $blockUpdate = $authManager->createPermission(BlockPermissions::UPDATE);
        $blockDelete = $authManager->createPermission(BlockPermissions::DELETE);
        $blockChangeOrder = $authManager->createPermission(BlockPermissions::CHANGE_ORDER);

        $catalogueIndex = $authManager->createPermission(CataloguePermissions::INDEX);
        $catalogueView = $authManager->createPermission(CataloguePermissions::VIEW);
        $catalogueCreate = $authManager->createPermission(CataloguePermissions::CREATE);
        $catalogueUpdate = $authManager->createPermission(CataloguePermissions::UPDATE);
        $catalogueDelete = $authManager->createPermission(CataloguePermissions::DELETE);

        $contactsIndex = $authManager->createPermission(ContactsPermissions::INDEX);
        $contactsView = $authManager->createPermission(ContactsPermissions::VIEW);
        $contactsCreate = $authManager->createPermission(ContactsPermissions::CREATE);
        $contactsUpdate = $authManager->createPermission(ContactsPermissions::UPDATE);
        $contactsDelete = $authManager->createPermission(ContactsPermissions::DELETE);
        $contactsChangeOrder = $authManager->createPermission(ContactsPermissions::CHANGE_ORDER);

        $imageIndex = $authManager->createPermission(ImagePermissions::INDEX);
        $imageView = $authManager->createPermission(ImagePermissions::VIEW);
        $imageCreate = $authManager->createPermission(ImagePermissions::CREATE);
        $imageUpdate = $authManager->createPermission(ImagePermissions::UPDATE);
        $imageDelete = $authManager->createPermission(ImagePermissions::DELETE);
        $imageSetMain = $authManager->createPermission(ImagePermissions::SET_MAIN);
        $imageChangeOrder = $authManager->createPermission(ImagePermissions::CHANGE_ORDER);

        $menuIndex = $authManager->createPermission(MenuPermissions::INDEX);
        $menuView = $authManager->createPermission(MenuPermissions::VIEW);
        $menuCreate = $authManager->createPermission(MenuPermissions::CREATE);
        $menuUpdate = $authManager->createPermission(MenuPermissions::UPDATE);
        $menuDelete = $authManager->createPermission(MenuPermissions::DELETE);

        $newsIndex = $authManager->createPermission(NewsPermissions::INDEX);
        $newsView = $authManager->createPermission(NewsPermissions::VIEW);
        $newsCreate = $authManager->createPermission(NewsPermissions::CREATE);
        $newsUpdate = $authManager->createPermission(NewsPermissions::UPDATE);
        $newsDelete = $authManager->createPermission(NewsPermissions::DELETE);

        $orderIndex = $authManager->createPermission(OrderPermissions::INDEX);
        $orderView = $authManager->createPermission(OrderPermissions::VIEW);
//        $orderCreate = $authManager->createPermission(OrderPermissions::CREATE);
        $orderUpdate = $authManager->createPermission(OrderPermissions::UPDATE);
        $orderDelete = $authManager->createPermission(OrderPermissions::DELETE);

        $pageIndex = $authManager->createPermission(PagePermissions::INDEX);
        $pageView = $authManager->createPermission(PagePermissions::VIEW);
        $pageCreate = $authManager->createPermission(PagePermissions::CREATE);
        $pageUpdate = $authManager->createPermission(PagePermissions::UPDATE);
        $pageDelete = $authManager->createPermission(PagePermissions::DELETE);

        $productIndex = $authManager->createPermission(ProductPermissions::INDEX);
        $productView = $authManager->createPermission(ProductPermissions::VIEW);
        $productCreate = $authManager->createPermission(ProductPermissions::CREATE);
        $productUpdate = $authManager->createPermission(ProductPermissions::UPDATE);
        $productDelete = $authManager->createPermission(ProductPermissions::DELETE);

        $settingsIndex = $authManager->createPermission(SettingsPermissions::INDEX);
        $settingsView = $authManager->createPermission(SettingsPermissions::VIEW);
        $settingsCreate = $authManager->createPermission(SettingsPermissions::CREATE);
        $settingsUpdate = $authManager->createPermission(SettingsPermissions::UPDATE);
        $settingsDelete = $authManager->createPermission(SettingsPermissions::DELETE);

        $userIndex = $authManager->createPermission(UserPermissions::INDEX);
        $userView = $authManager->createPermission(UserPermissions::VIEW);
        $userCreate = $authManager->createPermission(UserPermissions::CREATE);
        $userUpdate = $authManager->createPermission(UserPermissions::UPDATE);
        $userDelete = $authManager->createPermission(UserPermissions::DELETE);
        $updateOwnProfile = $authManager->createPermission(UserPermissions::UPDATE_OWN_PROFILE);

        // Add permissions in Yii::$app->authManager
        $authManager->add($login);
        $authManager->add($logout);
        $authManager->add($error);
        $authManager->add($signUp);
        $authManager->add($index);
        $authManager->add($view);
        $authManager->add($update);
        $authManager->add($delete);


        $authManager->add($articleIndex);
        $authManager->add($articleView);
        $authManager->add($articleCreate);
        $authManager->add($articleUpdate);
        $authManager->add($articleDelete);

        $authManager->add($blockIndex);
        $authManager->add($blockView);
        $authManager->add($blockCreate);
        $authManager->add($blockUpdate);
        $authManager->add($blockDelete);
        $authManager->add($blockChangeOrder);

        $authManager->add($catalogueIndex);
        $authManager->add($catalogueView);
        $authManager->add($catalogueCreate);
        $authManager->add($catalogueUpdate);
        $authManager->add($catalogueDelete);

        $authManager->add($contactsIndex);
        $authManager->add($contactsView);
        $authManager->add($contactsCreate);
        $authManager->add($contactsUpdate);
        $authManager->add($contactsDelete);
        $authManager->add($contactsChangeOrder);

        $authManager->add($imageIndex);
        $authManager->add($imageView);
        $authManager->add($imageCreate);
        $authManager->add($imageUpdate);
        $authManager->add($imageDelete);
        $authManager->add($imageSetMain);
        $authManager->add($imageChangeOrder);

        $authManager->add($menuIndex);
        $authManager->add($menuView);
        $authManager->add($menuCreate);
        $authManager->add($menuUpdate);
        $authManager->add($menuDelete);

        $authManager->add($newsIndex);
        $authManager->add($newsView);
        $authManager->add($newsCreate);
        $authManager->add($newsUpdate);
        $authManager->add($newsDelete);

        $authManager->add($orderIndex);
        $authManager->add($orderView);
//        $authManager->add($orderCreate);
        $authManager->add($orderUpdate);
        $authManager->add($orderDelete);

        $authManager->add($pageIndex);
        $authManager->add($pageView);
        $authManager->add($pageCreate);
        $authManager->add($pageUpdate);
        $authManager->add($pageDelete);

        $authManager->add($productIndex);
        $authManager->add($productView);
        $authManager->add($productCreate);
        $authManager->add($productUpdate);
        $authManager->add($productDelete);

        $authManager->add($settingsIndex);
        $authManager->add($settingsView);
        $authManager->add($settingsCreate);
        $authManager->add($settingsUpdate);
        $authManager->add($settingsDelete);

        $authManager->add($userIndex);
        $authManager->add($userView);
        $authManager->add($userCreate);
        $authManager->add($userUpdate);
        $authManager->add($userDelete);


        // Add rule, based on UserExt->role === $user->role
        $userRoleRule = new UserRoleRule();
        $authManager->add($userRoleRule);

        $userProfileOwnerRule = new UserProfileOwnerRule();
        $authManager->add($userProfileOwnerRule);

        // Add rule "UserRoleRule" in roles
        $guest->ruleName  = $userRoleRule->name;
        $moderator->ruleName  = $userRoleRule->name;
        $admin->ruleName  = $userRoleRule->name;
        $updateOwnProfile->ruleName = $userProfileOwnerRule->name;


        // Add roles in Yii::$app->authManager
        $authManager->add($guest);
        $authManager->add($moderator);
        $authManager->add($admin);
        $authManager->add($updateOwnProfile);

        // Add permission-per-role in Yii::$app->authManager
        // Guest
        $authManager->addChild($guest, $login);
        $authManager->addChild($guest, $logout);
        $authManager->addChild($guest, $error);
        $authManager->addChild($guest, $signUp);
        $authManager->addChild($guest, $index);
        $authManager->addChild($guest, $view);

        // Moderator
        $authManager->addChild($moderator, $articleIndex);
        $authManager->addChild($moderator, $articleView);
        $authManager->addChild($moderator, $articleCreate);
        $authManager->addChild($moderator, $articleUpdate);
        $authManager->addChild($moderator, $articleDelete);

        $authManager->addChild($moderator, $blockIndex);
        $authManager->addChild($moderator, $blockView);
        $authManager->addChild($moderator, $blockCreate);
        $authManager->addChild($moderator, $blockUpdate);
        $authManager->addChild($moderator, $blockDelete);
        $authManager->addChild($moderator, $blockChangeOrder);

        $authManager->addChild($moderator, $catalogueIndex);
        $authManager->addChild($moderator, $catalogueView);
        $authManager->addChild($moderator, $catalogueCreate);
        $authManager->addChild($moderator, $catalogueUpdate);
        $authManager->addChild($moderator, $catalogueDelete);

        $authManager->addChild($moderator, $contactsIndex);
        $authManager->addChild($moderator, $contactsView);
        $authManager->addChild($moderator, $contactsCreate);
        $authManager->addChild($moderator, $contactsUpdate);
        $authManager->addChild($moderator, $contactsDelete);
        $authManager->addChild($moderator, $contactsChangeOrder);

        $authManager->addChild($moderator, $imageIndex);
        $authManager->addChild($moderator, $imageView);
        $authManager->addChild($moderator, $imageCreate);
        $authManager->addChild($moderator, $imageUpdate);
        $authManager->addChild($moderator, $imageDelete);
        $authManager->addChild($moderator, $imageSetMain);
        $authManager->addChild($moderator, $imageChangeOrder);

        $authManager->addChild($moderator, $newsIndex);
        $authManager->addChild($moderator, $newsView);
        $authManager->addChild($moderator, $newsCreate);
        $authManager->addChild($moderator, $newsUpdate);
        $authManager->addChild($moderator, $newsDelete);

        $authManager->addChild($moderator, $orderIndex);
        $authManager->addChild($moderator, $orderView);
//        $authManager->addChild($moderator, $orderCreate);
        $authManager->addChild($moderator, $orderUpdate);

        $authManager->addChild($moderator, $pageIndex);
        $authManager->addChild($moderator, $pageView);
        $authManager->addChild($moderator, $pageCreate);
        $authManager->addChild($moderator, $pageUpdate);
        $authManager->addChild($moderator, $pageDelete);

        $authManager->addChild($moderator, $productIndex);
        $authManager->addChild($moderator, $productView);
        $authManager->addChild($moderator, $productCreate);
        $authManager->addChild($moderator, $productUpdate);
        $authManager->addChild($moderator, $productDelete);

        $authManager->addChild($moderator, $updateOwnProfile);
        $authManager->addChild($moderator, $guest);


        // Admin
        $authManager->addChild($admin, $update);
        $authManager->addChild($admin, $delete);

        $authManager->addChild($admin, $menuIndex);
        $authManager->addChild($admin, $menuView);
        $authManager->addChild($admin, $menuCreate);
        $authManager->addChild($admin, $menuUpdate);
        $authManager->addChild($admin, $menuDelete);

        $authManager->addChild($admin, $orderDelete);

        $authManager->addChild($admin, $settingsIndex);
        $authManager->addChild($admin, $settingsView);
        $authManager->addChild($admin, $settingsCreate);
        $authManager->addChild($admin, $settingsUpdate);
        $authManager->addChild($admin, $settingsDelete);

        $authManager->addChild($admin, $userIndex);
        $authManager->addChild($admin, $userView);
        $authManager->addChild($admin, $userCreate);
        $authManager->addChild($admin, $userUpdate);
        $authManager->addChild($admin, $userDelete);

        $authManager->addChild($admin, $moderator);
    }
}