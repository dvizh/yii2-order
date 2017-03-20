<?php
namespace dvizh\order\services;

use dvizh\app\interfaces\services\singletons\User;
use dvizh\app\interfaces\services\Order as OrderService;

class UserOrder extends \yii\base\Object implements \dvizh\app\interfaces\services\singletons\UserOrder
{
    protected $user, $order;

    public function __construct(User $user, OrderService $order, $config = [])
    {
        $this->user = $user;
        $this->order = $order;

        parent::__construct($config);
    }

    public function getTotalSum() : int
    {
        return $this->order->getUserTotalSum($this->user);
    }
}