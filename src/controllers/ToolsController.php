<?php
namespace dvizh\order\controllers;

use yii;
use yii\web\Controller;
use yii\helpers\Html;
use yii\filters\AccessControl;

class ToolsController  extends Controller
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
        ];
    }
    public function actionAjaxElementsList()
    {
        $model = yii::createObject('\dvizh\order\services\Order')->getById(yii::$app->request->post('orderId'));

        $elements = '';

        if  ($model->promocode) {
            $promocode = yii::$app->promocode->checkExists($model->promocode);
            if  ($promocode->type === 'quantum') {
                $discountType = '';
            } else {
                $discountType = '%';
            }
            $elements .= Html::tag('div', '<strong>'.$promocode->code.'</strong>');
            $elements .= Html::tag('div', '<strong>'.$promocode->discount.'</strong> ' . $discountType);
        }

        $elements .= Html::ul($model->elements, ['item' => function($item, $index) {
            return Html::tag(
                'li',
                "{$item->getProduct()->getName()} - {$item->getPrice()}x{$item->count}",
                ['class' => 'post']
            );
        }]);

        die(json_encode([
            'elementsHtml' => $elements,
        ]));
    }
}