<?php
namespace dvizh\order\logic;

class CountCalculate
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

        $order->count = 0;
        
        foreach($order->elements as $element) {
            $order->count += $element->count;
        }

        $order->save();
        
        return true;
    }
}