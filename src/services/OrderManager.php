<?php
namespace dvizh\order\services;

use \dvizh\app\interfaces\entities\User;
use \dvizh\app\interfaces\entities\Order as OrderEntity;
use \dvizh\app\interfaces\services\singletons\UserCart;
use \dvizh\app\interfaces\entities\OrderElement;

class OrderManager extends \yii\base\Object implements \dvizh\app\interfaces\data\OrderManager
{
    protected $cart, $element, $order;

    public function __construct(OrderEntity $order, UserCart $cart, OrderElement $element, $config = [])
    {
        $this->cart = $cart;
        $this->element = $element;
        $this->order = $order;

        parent::__construct($config);
    }

    public function getUserTotalSum(User $user) : int
    {
        $order = $this->order;

        return $order::find()->where(['user_id' => $user->getId()])->sum('cost');
    }

    public function getById(int $id) : \dvizh\app\interfaces\entities\Order
    {
        $order = $this->order;
        
        return $order::find()->where(['id' => $id])->one();
    }

    public function putElement(\dvizh\app\interfaces\entities\CartElement $element) : int
    {
        $elementModel = $this->element;
        $elementModel = new $elementModel;

        $elementModel->setOrder($this->order);
        $elementModel->setAssigment($this->order->getAssigment());
        $elementModel->setModelName($element->getModelName());
        $elementModel->setName($element->getName());
        $elementModel->setItemId($element->getItemId());
        $elementModel->setCount($element->getCount());
        $elementModel->setPrice($element->getPrice());
        $elementModel->setOptions(json_encode($element->getOptions()));
        $elementModel->setDescription('');

        $elementModel->saveData();

        return $elementModel->getId();
    }

    public function cancelElement(\dvizh\app\interfaces\entities\CartElement $element)
    {
        $element->setDeleted(1);

        $element->order->reCalculate();

        return $element->saveData();
    }

    public function setStatus($status)
    {
        $this->order->setStatus($status);

        return $this->order->saveData();
    }

    public function filling()
    {
        foreach($this->cart->getElements() as $element) {
            $this->putElement($element);
        }

        $this->reCalculate();
        $this->cart->truncate();

        return true;
    }

    public function cancel()
    {
        $this->order->setDeleted(1);

        return $this->order->saveData();
    }

    public function reCalculate()
    {
        $count = 0;
        $baseCost = 0;
        $cost = 0;

        foreach ($this->order->elements as $element) {
            $count += $element->getCount();
            $baseCost += ($element->getBasePrice()*$element->getCount());
            $cost += ($element->getPrice()*$element->getCount());
        }

        $this->order->setCount($count);
        $this->order->setCost($cost);

        return $this->order->saveData();
    }
}
