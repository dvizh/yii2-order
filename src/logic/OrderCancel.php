<?php
namespace dvizh\order\logic;

class OrderCancel
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

        $order->setDeleted(1);
        $order->saveData();

        return true;
    }
}