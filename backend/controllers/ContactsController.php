<?php

namespace backend\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\ForbiddenHttpException;
use backend\models\ContactsItemSearch;
use common\components\rbac\ContactsPermissions;
use common\models\ContactsItem;
use backend\models\Map as MapModel;
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
        if ( !Yii::$app->user->can(ContactsPermissions::INDEX)) {
            throw new ForbiddenHttpException('Access denied');
        }

        if (Yii::$app->request->post('hasEditable')) {
            if ( !Yii::$app->user->can(ContactsPermissions::UPDATE)) {
                throw new ForbiddenHttpException('Access denied');
            }

            $modelId = Yii::$app->request->post('editableKey');
            $model = ContactsItem::findOne($modelId);

            $out = Json::encode(['output' => '', 'message' => '']);

            $posted = current($_POST['ContactsItem']);
            $post = ['ContactsItem' => $posted];

            if ($model->load($post)) {
                $model->save();
                $output = '';
                if (isset($posted['status'])) {
                    $output = ContactsItem::getStatusName($model->status);
                }

                $out = Json::encode(['output' => $output, 'message' => '']);
            }
            echo $out;

            return;
        }

        $tabIndex = 0;
        $searchModel = new ContactsItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $seoInfo = SEOInformation::findModel('contacts', 'index');
        if (is_null($seoInfo)) {
            $seoInfo = new SEOInformation();
            $seoInfo->controller_id = 'contacts';
            $seoInfo->action_id = 'index';
        }

        if (isset($_POST['saveSEO'])) {
            if ($seoInfo->load(Yii::$app->request->post())) {
                if ($seoInfo->save()) {
                    Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));
                }

                $tabIndex = 1;
            }
        }

        $mapModel = MapModel::findModel('contacts', 'index');
        if (is_null($mapModel)) {
            $mapModel = new MapModel();
            $mapModel->vendor = MapModel::VENDOR_YANDEX;
            $mapModel->controller_id = 'contacts';
            $mapModel->action_id = 'index';
            $mapModel->type = MapModel::TYPE_YANDEX_MAP;
            $mapModel->zoom_control = MapModel::CONTROL_ON;
            $mapModel->type_selector = MapModel::CONTROL_ON;
            $mapModel->ruler_control = MapModel::CONTROL_ON;
            $mapModel->status = MapModel::STATUS_ACTIVE;
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'seoInfo' => $seoInfo,
            'mapModel' => $mapModel,
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
        if ( !Yii::$app->user->can(ContactsPermissions::CREATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $model = new ContactsItem();
        $contactItems = ContactsItem::find()->orderBy(['weight' => SORT_ASC])->all();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveContact'])) {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->render('create', [
                    'model' => $model,
                    'contactItems' => $contactItems,
                ]);
            }
        } else {
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
        if ( !Yii::$app->user->can(ContactsPermissions::UPDATE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $id = intval($id);
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $trans = $model->getDb()->beginTransaction();
            try {
                $model->changeOrder($model->weight, false);
                $saveResult = $model->save();
                $trans->commit();
            }
            catch (\Exception $e) {
                $saveResult = false;
                $trans->rollBack();
            }

            if ($saveResult) {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['saveContact'])) {
                    return $this->redirect(['index']);
                } else {
                    return $this->redirect(['update', 'id' => $model->id]);
                }
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', '<strong> Error! </strong> An error occurred while saving the data.'));

                return $this->redirect(['index']);
            }
        } else {
            $contactItems = ContactsItem::find()->where(['<>', 'id', $model->id])->orderBy(['weight' => SORT_ASC])->all();

            return $this->render('update', [
                'model' => $model,
                'contactItems' => $contactItems,
            ]);
        }
    }

    public function actionUpdateName()
    {
        if (Yii::$app->request->post('hasEditable')) {
            if ( !Yii::$app->user->can(ContactsPermissions::UPDATE)) {
                throw new ForbiddenHttpException('Access denied');
            }

            $modelId = Yii::$app->request->post('editableKey');
            $model = ContactsItem::findOne($modelId);

            $out = Json::encode(['output' => '', 'message' => '']);

            $posted = current($_POST['ContactsItem']);
            $post = ['ContactsItem' => $posted];

            if ($model->load($post)) {
                $model->save();
                $output = '';
                if (isset($posted['name'])) {
                    $output = $model->name;
                }

                $out = Json::encode(['output' => $output, 'message' => '']);
            }
            echo $out;
        }

        return;
    }

    public function actionUpdateValue()
    {
        if (Yii::$app->request->post('hasEditable')) {
            if ( !Yii::$app->user->can(ContactsPermissions::UPDATE)) {
                throw new ForbiddenHttpException('Access denied');
            }

            $modelId = Yii::$app->request->post('editableKey');
            $model = ContactsItem::findOne($modelId);

            $out = Json::encode(['output' => '', 'message' => '']);

            $posted = current($_POST['ContactsItem']);
            $post = ['ContactsItem' => $posted];

            if ($model->load($post)) {
                $model->save();
                $output = '';
                if (isset($posted['value'])) {
                    $output = $model->name;
                }

                $out = Json::encode(['output' => $output, 'message' => '']);
            }
            echo $out;
        }

        return;
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
        if ( !Yii::$app->user->can(ContactsPermissions::DELETE)) {
            throw new ForbiddenHttpException('Access denied');
        }

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Changes filed
     *
     * @param integer $id model Id
     * @return string
     * @throws ForbiddenHttpException
     * @throws NotFoundHttpException
     */
    public function actionChangeField($id = null)
    {
        if ( !Yii::$app->user->can(ContactsPermissions::CREATE) && !Yii::$app->user->can(ContactsPermissions::UPDATE)) {
            throw new ForbiddenHttpException('Access denied');;
        }

        $result = ['status' => 'not_success', 'rslt' => null, 'msg' => 'Method is not Post'];
        if (Yii::$app->request->isPost) {
            $fieldType = Yii::$app->request->post('fieldType', null);

            $model = is_null($id) ? new ContactsItem() : $this->findModel($id);

            $msg = 'The input field was created successfully';
            switch ($fieldType) {
                case ContactsItem::TYPE_FAX:
                case ContactsItem::TYPE_PHONE:
                    $template = '_phoneField';
                    break;

                case ContactsItem::TYPE_EMAIL:
                    $template = '_emailField';
                    break;

                case ContactsItem::TYPE_ADDRESS:
                    $template = '_textArea';
                    break;

                case ContactsItem::TYPE_OTHER:
                    $template = '_wysiwygEditor';
                    break;

                default:
                    $template = '_textField';
                    $msg = 'The input field was created by default';
            }

            $result = [
                'status' => 'success',
                'rslt' => $this->renderAjax($template, ['model' => $model, 'form' => new \yii\widgets\ActiveForm()]),
                'msg' => $msg,
            ];
        }

        if (Yii::$app->request->isPost && Yii::$app->request->isAjax) {
            return Json::encode($result);
        }
    }

    public function actionChangeOrder()
    {
        $counter = 0;
        $status = 'no_data_found';
        if (isset($_POST['sortData'])) {
            $sortData = Yii::$app->request->post('sortData');
            if (is_array($sortData) && count($sortData) > 0) {
                foreach ($sortData as $index => $id) {
                    $counter += (ContactsItem::updateAll(['weight' => $index], ['id' => intval($id)])) ? 1 : 0;
                }
            }

            $status = ($counter > 0) ? 'success' : 'no_updated';
        }

        echo Json::encode(['status' => $status, 'rslt' => $counter]);
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
