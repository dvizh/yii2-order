<?php
namespace dvizh\order\widgets;

use yii\helpers\Url;
use dvizh\order\models\Order;
use dvizh\order\models\PaymentType;
use dvizh\order\models\ShippingType;
use dvizh\order\models\Field;
use dvizh\order\models\FieldValue;
use yii;

class OrderForm extends \yii\base\Widget
{
    
    public $view = 'order-form/form';
    public $elements = [];
    
    public function init()
    {
        \dvizh\order\assets\OrderFormAsset::register($this->getView());
        
        return parent::init();
    }
    
    public function run()
    {
        $shippingTypesList = ShippingType::find()->orderBy('order DESC')->all();

        $shippingTypes = ['' => yii::t('order', 'Choose shipping type')];
        
        foreach($shippingTypesList as $sht) {
            if($sht->cost > 0) {
                $currency = yii::$app->getModule('order')->currency;
                $name = "{$sht->name} ({$sht->cost}{$currency})";
            } else {
                $name = $sht->name;
            }
            $shippingTypes[$sht->id] = $name;
        }
        
        $paymentTypes = ['' => yii::t('order', 'Choose payment type')];
        $paymentTypesList = PaymentType::find()->orderBy('order DESC')->all();
        
        foreach($paymentTypesList as $pt) {
            $paymentTypes[$pt->id] = $pt->name;
        }
        
        $fieldFind = Field::find()->orderBy('order DESC');
        
        $fieldValueModel = new FieldValue;
    
        $orderModel = new Order;
        
        if(empty($orderModel->shipping_type_id) && $orderShippingType = yii::$app->session->get('orderShippingType')) {
            if($orderShippingType > 0) {
                $orderModel->shipping_type_id = (int)$orderShippingType;
            }
        }
        
        $this->getView()->registerJs("dvizh.orderForm.updateShippingType = '".Url::toRoute(['/order/tools/update-shipping-type'])."';");
        
        return $this->render($this->view, [
            'orderModel' => $orderModel,
            'fieldFind' => $fieldFind,
            'paymentTypes' => $paymentTypes,
            'elements' => $this->elements,
            'shippingTypes' => $shippingTypes,
            'shippingTypesList' => $shippingTypesList,
            'fieldValueModel' => $fieldValueModel,
        ]);
    }

}
