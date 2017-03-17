<?php
namespace dvizh\order\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class ChangeStatus extends \yii\base\Widget
{
    public $model = null;

    public function init()
    {
        parent::init();

        \dvizh\order\assets\ChangeStatusAsset::register($this->getView());

        return true;
    }
    
    public function run()
    {
        $select = Html::dropDownList('status', $this->model->status, yii::$app->getModule('order')->orderStatuses, ['data-link' => Url::toRoute(['/order/order/update-status']), 'data-id' => $this->model->id, 'class' => 'form-control dvizh-change-order-status']);
        
        return $select;
    }
}
