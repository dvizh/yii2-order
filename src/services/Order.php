<?php
namespace dvizh\order\services;

class Order extends \yii\base\Object implements \dvizh\dic\interfaces\services\Order
{
    protected $cart;

    public function __construct(\dvizh\dic\interfaces\services\Cart $cart, $config = [])
    {
        $this->cart = $cart;

        parent::__construct($config);
    }

    public function getById($id) : \dvizh\dic\interfaces\entity\Order
    {
        return \dvizh\order\models\Order::find()->where(['id' => $id])->one();
    }

    public function putElement(\dvizh\dic\interfaces\entity\Order $order, \dvizh\dic\interfaces\entity\CartElement $element)
    {
        $elementModel = $this->element;
        $elementModel = new $elementModel;

        $elementModel->setOrder($order);
        $elementModel->setAssigment($order->is_assigment);

        $elementModel->setModelName($element->getModelName());
        $elementModel->setName($element->getName());
        $elementModel->setItemId($element->getItemId());
        $elementModel->setCount($element->getCount());
        $elementModel->setBasePrice($element->getPrice(false));
        $elementModel->setPrice($element->getPrice());
        $elementModel->setOptions(json_encode($element->getOptions()));
        $elementModel->setDescription('');
        $elementModel->saveData();
    }

    public function cancelElement(\dvizh\dic\interfaces\entity\CartElement $element)
    {
        $element->setDeleted(1);
        $element->saveData();

        $element->order->reCalculate();
    }

    public function filling($order)
    {
        foreach($this->cart->getMyElements() as $element) {
            $order->putElement($order, $element);
        }

        $this->reCalculate($order);
        $this->cart->truncate();

        return true;
    }

    public function cancel(\dvizh\dic\interfaces\entity\Order $order)
    {
        $order->setDeleted(1);

        return $order->saveData();
    }

    public function reCalculate(\dvizh\dic\interfaces\entity\Order $order)
    {
        $count = 0;
        $baseCost = 0;
        $cost = 0;

        foreach ($order->elements as $element) {
            $count += $element->getCount();
            $baseCost += ($element->getBasePrice()*$element->getCount());
            $cost += ($element->getPrice()*$element->getCount());
        }

        $order->setCount($count);
        $order->setBaseCost($baseCost);
        $order->setCost($cost);

        return $order->saveData();
    }
}