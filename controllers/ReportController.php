<?php

namespace app\controllers;

use Yii;
use app\models\Report;
use app\models\UploadForm;
use yii\web\UploadedFile;
use app\models\ReportSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * ReportController implements the CRUD actions for Report model.
 */
class ReportController extends Controller
{
    /**
     * {@inheritdoc}
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
     * Lists all Report models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Report model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Report model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Report();
        $model1 = new UploadForm();
      
        if (($model->load(Yii::$app->request->post()))){
            echo $model->date;
            $model->date = date("Y-m-d", strtotime( $model->date));
            $model->file = UploadedFile::getInstance($model, 'file');
            $model1->file = UploadedFile::getInstance($model, 'file');            
            $model1->file->saveAs('uploads/' . $model1->file->baseName . '.' . $model1->file->extension);
            
        
            if($model->save()){

            }else{
                
                print_r($model->getErrors());
                die();
            }
        
            
            return $this->redirect(['view', 'id' => $model->id]);
        }
  
        return $this->render('create', [
            'model' => $model,
            'model1'=>$model1
        ]);
       
    }

    /**
     * Updates an existing Report model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Report model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Report model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Report the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Report::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
    public function actionPdf($id) {
        $model =Report::findOne($id);
        Yii::setAlias('@app', 'uploads/');
        
    
        // This will need to be the path relative to the root of your app.
        $filePath = '/web/uploads';
        // Might need to change '@app' for another alias
        $completePath = Yii::getAlias('@app/'.$model->file);
      echo $completePath;
       
    
        //return Yii::$app->response->sendFile($completePath, $model->file);
        $this->redirect((string)$completePath);
    }
}
