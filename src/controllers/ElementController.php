<?php
namespace dvizh\order\controllers;

use yii;
use dvizh\order\models\Element;
use dvizh\order\events\ElementEvent;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class ElementController extends Controller
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
                    'edittable' => ['post'],
                ],
            ],
        ];
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        
        $module = $this->module;
        $orderEvent = new ElementEvent(['model' => $model, 'orderModel' => $model->order, 'productModel' => $model->getModel()]);
        $this->module->trigger($module::EVENT_ELEMENT_DELETE, $orderEvent);
        
        yii::$app->order->cancelElement($model);

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionEditable()
    {
        $name = yii::$app->request->post('name');
        $value = yii::$app->request->post('value');
        $pk = unserialize(base64_decode(yii::$app->request->post('pk')));
        Element::editField($pk, $name, $value);
    }

    protected function findModel($id)
    {
        if (($model = Element::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
