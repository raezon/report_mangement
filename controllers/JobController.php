<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Category;
use app\models\Job;

class JobController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['create', 'edit', 'delete'],
                'rules' => [
                    // deny all POST requests
                    // [
                    //     'allow' => false,
                    //     'verbs' => ['POST']
                    // ],
                    // allow authenticated users
                    [
                        'actions' => ['create', 'edit', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // everything else is denied
                ],
            ],
        ];
    }

    public function actionCreate()
    {
        $job = new Job();

        if ($job->load(Yii::$app->request->post())) {
            if ($job->validate()) {
                // form inputs are valid, do something here
                $job->save();

                Yii::$app->getSession()->setflash('sucess', 'Job Added');

                return $this->redirect('index.php?r=job');
            }
        }

        return $this->render('create', [
            'job' => $job,
        ]);
    }

    public function actionDelete($id)
    {
        $job = Job::findOne($id);

        //check user id
        if (Yii::$app->user->identity->id != $job->user_id) {
            return $this->redirect('index.php?r=job');
        }

        $job->delete();

        Yii::$app->getSession()->setflash('sucess', 'Job Delete');

        return $this->redirect('index.php?r=job');
    }

    public function actionEdit($id)
    {
        $job = Job::findOne($id);

        //check user id
        if (Yii::$app->user->identity->id != $job->user_id) {
            return $this->redirect('index.php?r=job');
        }

        if ($job->load(Yii::$app->request->post())) {
            if ($job->validate()) {
                // form inputs are valid, do something here
                $job->save();

                Yii::$app->getSession()->setflash('sucess', 'Job Updated');

                return $this->redirect('index.php?r=job');
            }
        }

        return $this->render('edit', [
            'job' => $job,
        ]);
    }

    public function actionIndex()
    {
        //create query
        $query = Job::find();

        $pagination = new Pagination([
            'defaultPageSize' => 20,
            'totalCount' => $query->count(),
        ]);

        $jobs = $query->orderBy('create_date DESC')
                    ->offset($pagination->offset)
                    ->limit($pagination->limit)
                    ->all();

        return $this->render('index', [
            'jobs' => $jobs, 
            'pagination' => $pagination
        ]);
    }

    public function actionDetails($id)
    {
        //detail query
        $job = Job::find()
                    ->where([ 'id' => $id ])
                    ->one();

        return $this->render('details', [ 'job' => $job ]);
    }

}
