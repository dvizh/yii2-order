<?php
namespace dvizh\order\logic;

class CostCalculate
{
    private $order;
    public $orderId;

    public function __construct(\dvizh\dic\interfaces\order\OrderService $order, $config = [])
    {
        $this->order = $order;
    }

    public function execute()
    {
        $order = $this->order->getById($this->orderId);

        $order->base_cost = 0;
        $order->cost = 0;
        
        foreach($order->elements as $element) {
            $order->base_cost += ($element->base_price*$element->count);
            $order->cost += ($element->price*$element->count);
        }

        $order->save();
        
        return true;
    }
}