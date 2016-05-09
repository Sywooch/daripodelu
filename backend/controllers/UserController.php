<?php

namespace backend\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use common\models\User;
use common\components\rbac\UserPermissions;
use backend\models\UserSearch;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionIndex()
    {
        if ( !Yii::$app->user->can(UserPermissions::INDEX))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        if (Yii::$app->request->post('hasEditable'))
        {
            if ( ! Yii::$app->user->can(UserPermissions::UPDATE_OWN_PROFILE, ['profileId' => Yii::$app->user->id]))
            {
                throw new ForbiddenHttpException('Access denied');
            }

            $userId = Yii::$app->request->post('editableKey');
            $model = User::findOne($userId);

            $out = Json::encode(['output' => '', 'message' => '']);

            $posted = current($_POST['User']);
            $post = ['User' => $posted];

            if ($model->load($post))
            {
                $model->save();
                $output = '';
                if (isset($posted['role']))
                {
                    $output = $model->roleName;
                }
                elseif (isset($posted['status']))
                {
                    $output = $model->statusName;
                }

                $out = Json::encode(['output' => $output, 'message' => '']);
            }
            echo $out;

            return;
        }

        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws \Exception
     */
    public function actionCreate()
    {
        if ( !Yii::$app->user->can(UserPermissions::CREATE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = new User();
        $model->scenario = User::SCENARIO_REGISTER;

        if ($model->load(Yii::$app->request->post()))
        {
            $model->setPasswordHash($model->password);
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The user added successfully.'));

                if (isset($_POST['saveUser']))
                {
                    return $this->redirect(['index']);
                }
                else
                {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        }
        else
        {
            $model->status = User::STATUS_ACTIVE;

            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionUpdate($id)
    {
        if ( ! Yii::$app->user->can(UserPermissions::UPDATE_OWN_PROFILE, ['profileId' => $id]))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = $this->findModel($id);
        $oldUserName = $model->username;

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->username != $oldUserName)
            {
                $model->username = $oldUserName;
            }

            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveUser']))
                {
                    return $this->redirect(['index']);
                }
                else
                {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     * @throws \Exception
     */
    public function actionDelete($id)
    {
        if ( !Yii::$app->user->can(UserPermissions::DELETE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
