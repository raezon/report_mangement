<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use yii\data\Pagination;
use app\models\Category;

class CategoryController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'only' => ['create'],
                'rules' => [
                    // deny all POST requests
                    // [
                    //     'allow' => false,
                    //     'verbs' => ['POST']
                    // ],
                    // allow authenticated users
                    [
                        'actions' => ['create'],
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
    	$category = new Category();

    	if ($category->load(Yii::$app->request->post())) {
        if ($category->validate()) {
            // form inputs are valid, do something here
            $category->save(); //save record to database

            //send message
            Yii::$app->getSession()->setflash('sucess', 'Category Added');

        	return $this->redirect('index.php?r=category');
        }
    }

    	return $this->render('create', [
        	'category' => $category,
    	]);

    }

    public function actionIndex()
    {
    	//create query
    	$query = Category::find();

    	$pagination = new Pagination([
    		'defaultPageSize' => 20,
    		'totalCount' => $query->count(),
    	]);

    	$categories = $query->orderBy('name')
    				->offset($pagination->offset)
    				->limit($pagination->limit)
    				->all();

        return $this->render('index', [
        	'categories' => $categories, 
        	'pagination' => $pagination
        ]);
    }

}
