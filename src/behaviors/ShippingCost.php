<?php
namespace dvizh\order\behaviors;

use yii\base\Behavior;
use dvizh\cart\Cart;
use dvizh\order\models\ShippingType;
use yii;

class ShippingCost extends Behavior
{
    public function events()
    {
        return [
            Cart::EVENT_CART_COST => 'addShippingCost'
        ];
    }

    public function addShippingCost($event)
    {
        if($orderShippingType = yii::$app->session->get('orderShippingType')) {
            if($orderShippingType > 0) {
                $shippingType = ShippingType::findOne($orderShippingType);

                if(($shippingType && $shippingType->cost > 0) && ((int)$shippingType->free_cost_from <= 0 | $shippingType->free_cost_from > $event->cost)) {
                    $event->cost = $event->cost+$shippingType->cost;
                }
            }
        }

        return $this;
    }
}
