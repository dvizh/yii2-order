<?php
namespace dvizh\order\logic;

class OrderChangeStatus
{
    private $order;
    public $orderId;
    public $status;

    public function __construct(\dvizh\dic\interfaces\order\OrderService $order, $config = [])
    {
        $this->order = $order;
    }

    public function execute()
    {
        $order = $this->order->getById($this->orderId);

        $order->setStatus($status);
        $order->saveData();

        return true;
    }
}