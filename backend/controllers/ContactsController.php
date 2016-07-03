<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use backend\models\ContactsItemSearch;
use common\components\rbac\ContactsPermissions;
use common\models\ContactsItem;
use common\models\SEOInformation;

/**
 * ContactsController implements the CRUD actions for ContactsItem model.
 */
class ContactsController extends Controller
{
    /**
     * @inheritdoc
     */
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
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ContactsItem models.
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionIndex()
    {
        if ( !Yii::$app->user->can(ContactsPermissions::INDEX))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $tabIndex = 0;
        $searchModel = new ContactsItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $seoInfo = SEOInformation::findModel('contacts', 'index');
        if (is_null($seoInfo))
        {
            $seoInfo = new SEOInformation();
            $seoInfo->controller_id = 'contacts';
            $seoInfo->action_id = 'index';
        }

        if (isset($_POST['saveSEO']))
        {
            if ($seoInfo->load(Yii::$app->request->post()))
            {
                if ($seoInfo->save())
                {
                    Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
                }
                else
                {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
                }

                $tabIndex = 1;
            }
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'seoInfo' => $seoInfo,
            'tabIndex' => $tabIndex,
        ]);
    }

    /**
     * Creates a new ContactsItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionCreate()
    {
        if ( !Yii::$app->user->can(ContactsPermissions::CREATE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = new ContactsItem();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $contactItems = ContactsItem::find()->orderBy(['weight' => SORT_ASC])->all();

            return $this->render('create', [
                'model' => $model,
                'contactItems' => $contactItems,
            ]);
        }
    }

    /**
     * Updates an existing ContactsItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionUpdate($id)
    {
        if ( !Yii::$app->user->can(ContactsPermissions::UPDATE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContactsItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws ForbiddenHttpException
     */
    public function actionDelete($id)
    {
        if ( !Yii::$app->user->can(ContactsPermissions::DELETE))
        {
            throw new ForbiddenHttpException('Access denied');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContactsItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContactsItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContactsItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
