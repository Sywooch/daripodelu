<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use backend\models\PrintKind;
use backend\models\PrintLink;
use backend\models\PrintLinkSearch;

/**
 * PrintlinkController implements the CRUD actions for PrintLink model.
 */
class PrintlinkController extends Controller
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
     * Lists all PrintLink models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PrintLinkSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $printsKind = PrintKind::find()->all();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'printsKind' => $printsKind,
        ]);
    }

    /**
     * Creates a new PrintLink model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PrintLink();

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> The link added successfully.'));

                if (isset($_POST['savePrintLink']))
                {
                    return $this->redirect(['index']);
                }
                else
                {
                    return $this->redirect(['update', 'id' => $model->code]);
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

            $existsPrintLinks = PrintLink::find()->all();
            $existsPrintLinkCodes = ArrayHelper::getColumn($existsPrintLinks, 'code');

            $printsKind = PrintKind::find()->where(['not', ['name' => $existsPrintLinkCodes]])->all();

            return $this->render('create', [
                'model' => $model,
                'prints' => $printsKind,
            ]);
        }
    }

    /**
     * Updates an existing PrintLink model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->save())
            {
                Yii::$app->session->setFlash('success', Yii::t('app', '<strong>Saved!</strong> Changes saved successfully.'));

                if (isset($_POST['savePrintLink']))
                {
                    return $this->redirect(['index']);
                }
                else
                {
                    return $this->redirect(['update', 'id' => $model->code]);
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
            $existsPrintLinks = PrintLink::find()->where(['not', ['code' => $id]])->all();
            $existsPrintLinkCodes = ArrayHelper::getColumn($existsPrintLinks, 'code');

            $printsKind = PrintKind::find()->where(['not', ['name' => $existsPrintLinkCodes]])->all();

            return $this->render('update', [
                'model' => $model,
                'prints' => $printsKind,
            ]);
        }
    }

    /**
     * Deletes an existing PrintLink model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the PrintLink model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PrintLink the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PrintLink::find()->where(['code' => $id])->one()) !== null)
        {
            return $model;
        }
        else
        {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
