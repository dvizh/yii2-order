<?php
namespace dvizh\order\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\filters\AccessControl;

class ToolsController  extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['ajax-elements-list'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => $this->module->adminRoles,
                    ]
                ]
            ],
        ];
    }

    public function actionUpdateShippingType()
    {
        $shippingTypeId = (int)yii::$app->request->post('shipping_type_id');
        yii::$app->session->set('orderShippingType', $shippingTypeId);
        
        die(json_encode([
            'total' => yii::$app->cart->cost,
        ]));
    }

    public function actionAjaxElementsList()
    {
        $model = yii::$app->order->get(yii::$app->request->post('orderId'));

        $elements = Html::ul($model->elements, ['item' => function($item, $index) {
            return Html::tag(
                'li',
                "{$item->getModel()->getCartName()} - {$item->base_price} {$this->module->currency}x{$item->count}",
                ['class' => 'post']
            );
        }]);

        die(json_encode([
            'elementsHtml' => $elements,
        ]));
    }
}
