<?php
namespace dvizh\order\controllers;

use yii;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use dvizh\order\models\Order;

class StatController  extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
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
        $model = new Order;

        return $this->render('index', [
            'model' => $model,
        ]);
    }
    
    public function actionMonth($y = null, $m = null)
    {
        $m = Html::encode($m);
        $y = Html::encode($y);

        $model = new Order;

        return $this->render('month', [
            'm' => $m,
            'y' => $y,
            'month' => yii::t('order', "month_$m"),
            'model' => $model,
        ]);
    }
}
