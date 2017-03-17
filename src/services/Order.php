<?php
namespace dvizh\order\services;

class Order extends \yii\base\Object implements \dvizh\dic\interfaces\order\OrderService
{
    public function getById($id) : \dvizh\dic\interfaces\order\Order
    {
        return \dvizh\order\models\Order::find()->where(['id' => $id])->one();
    }
}