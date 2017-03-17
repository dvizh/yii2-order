<?php
namespace dvizh\order\logic;

use yii;

class OrderFilling
{
    public $orderId;

    protected $cart;
    protected $element;
    
    public function __construct(\dvizh\dic\interfaces\order\OrderService $order, \dvizh\dic\interfaces\order\OrderElement $element, \dvizh\dic\interfaces\cart\Cart $cart, $config = [])
    {
        $this->cart = $cart;
        $this->order = $order;
        $this->element = $element;
    }
    
    public function execute()
    {
        $order = $this->order->getById($this->orderId);

        foreach($this->cart->elements as $element) {
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
        
        $this->cart->truncate();
        
        yii::createObject(['class' => CountCalculate::class, 'orderId' => $this->orderId])->execute();
        yii::createObject(['class' => CostCalculate::class, 'orderId' => $this->orderId])->execute();
        
        return true;
    }
}