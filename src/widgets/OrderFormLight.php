<?php

namespace dvizh\order\widgets;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use dvizh\order\models\Order;
use dvizh\order\models\PaymentType;
use dvizh\order\models\ShippingType;
use dvizh\order\models\Field;
use dvizh\order\models\FieldValue;

class OrderFormLight extends \yii\base\Widget
{

    public $view = 'order-form/light';
    public $useAjax = false;
    public $nextStep = false;
    public $staffer = false;

    public function init()
    {
        \dvizh\order\assets\OrderFormLightAsset::register($this->getView());
        \dvizh\order\assets\CreateOrderAsset::register($this->getView());

        return parent::init();
    }

    public function run()
    {
        $paymentTypes = ArrayHelper::map(PaymentType::find()->orderBy('order DESC')->all(), 'id', 'name');

        $orderModel = Order;
        $model = new $orderModel;

        $this->getView()->registerJs("dvizh.createorder.updateCartUrl = '".Url::toRoute(['tools/cart-info'])."';");

        return $this->render($this->view, [
            'model' => $orderModel,
            'paymentTypes' => $paymentTypes,
            'useAjax' => $this->useAjax,
            'nextStep' => $this->nextStep,
            'staffer' => $this->staffer,
        ]);
    }

}
