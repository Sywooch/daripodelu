<?php

namespace backend\controllers;

use yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\ForbiddenHttpException;
use app\models\SettingsForm;
use common\components\rbac\SettingsPermissions;

class SettingsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        if ( !Yii::$app->user->can(SettingsPermissions::INDEX))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = new SettingsForm();
        if ($model->load(Yii::$app->request->post()))
        {
            if ( !Yii::$app->user->can(SettingsPermissions::UPDATE))
            {
                throw new ForbiddenHttpException('Access denied');
            }

            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
            }
            else
            {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
            }

            return $this->redirect(['index']);
        }
        else
        {
            $model = SettingsForm::fillAttributes();
        }


        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionUpdate()
    {
        if ( !Yii::$app->user->can(SettingsPermissions::UPDATE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        return $this->redirect(['index']);
    }
}
