<?php
namespace dvizh\order\services;

class OrderElement extends \yii\base\Object implements \dvizh\dic\interfaces\order\OrderElementService
{
    public function getById($id) : \dvizh\dic\interfaces\order\Element
    {
        return \dvizh\order\models\Element::findOne($id);
    }
}