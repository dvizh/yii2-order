<?php
namespace dvizh\order\logic;

class OrderRecovery
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

        $order->setDeleted(0);
        $order->saveData();

        return true;
    }
}