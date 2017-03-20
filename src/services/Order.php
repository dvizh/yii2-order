<?php
namespace dvizh\order\services;

use \dvizh\app\interfaces\entities\User;
use \dvizh\app\interfaces\entities\Order as OrderEntity;
use \dvizh\app\interfaces\services\singletons\UserCart;
use \dvizh\app\interfaces\entities\OrderElement;
use yii\db\Query;

class Order extends \yii\base\Object implements \dvizh\app\interfaces\services\Order
{
    protected $cart, $element, $order, $organization_id, $is_assigment;

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


    private function buildQuery($query)
    {
        if($this->organization_id) {
            $query->andWhere('(organization_id IS NULL OR organization_id = :organization_id)', [':organization_id' => $this->organization_id]);
        }

        $query->andWhere('(order.is_deleted IS NULL OR order.is_deleted != 1)');

        if($this->is_assigment) {
            $query->andWhere('order.is_assigment = 1');
        } else {
            $query->andWhere('(order.is_assigment IS NULL OR order.is_assigment != 1)');
        }

        return $query;
    }

    private function orderQuery()
    {
        $return = (new Query())->from('order');
        return $this->buildQuery($return);
    }

    private function orderFinder()
    {
        $return = OrderModel::find();

        return $this->buildQuery($return);
    }
    public function resetConditions()
    {
        $this->organization_id = null;
        $this->is_assigment = false;

        return $this;
    }

    public function setOrganization($organization_id)
    {
        $this->organization_id = $organization_id;

        return $this;
    }

    public function assigment()
    {
        $this->is_assigment = true;

        return $this;
    }

    public function get($id)
    {
        $order = $this->order;

        return $order::findOne($id);
    }

    public function getOrdersByDatePeriod($dateStart, $dateStop, $where = null)
    {
        if($dateStop == '0000-00-00 00:00:00' | empty($dateStop)) {
            $dateStop = date('Y-m-d H:i:s');
        }

        $query = $this->orderFinder()
            ->andWhere('date >= :dateStart AND date <= :dateStop', [':dateStart' => $dateStart, ':dateStop' => $dateStop])
            ->orderBy('timestamp ASC');

        if($where) {
            $query->andWhere($where);
        }
        $this->resetConditions();
        return $query->all();
    }

    public function getStatInMoth($month = null, $where = null)
    {
        if(!$month) {
            $month = date('Y-m');
        }

        $query = $this->orderQuery();
        $query->addSelect(['sum(cost) as total, sum(count) as count_elements, COUNT(DISTINCT id) as count_orders'])
            ->from(['order'])
            ->andWhere('DATE_FORMAT(date, "%Y-%m") = :date', [':date' => $month]);
        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();

        $this->resetConditions();

        return array_map('intval', $result);
    }
    public function getStatByDate($date, $where = null)
    {
        $query = $this->orderQuery();
        $query->addSelect(['sum(cost) as total, sum(count) as count_elements, COUNT(DISTINCT id) as count_orders'])
            ->from(['order'])
            ->andWhere(' DATE_FORMAT(date, "%Y-%m-%d") = :date', [':date' => $date]);
        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();

        $this->resetConditions();

        return array_map('intval', $result);
    }

    public function getStatByDatePeriod($dateStart, $dateStop = null, $where = null)
    {
        if($dateStop == '0000-00-00 00:00:00' | empty($dateStop)) {
            $dateStop = date('Y-m-d H:i:s');
        }
        $query = $this->orderQuery();
        $query->addSelect(['sum(cost) as total, sum(count) as count_elements, COUNT(DISTINCT id) as count_orders'])
            ->from(['order']);

        if($dateStart == $dateStop) {
            $query->andWhere('DATE_FORMAT(date,\'%Y-%m-%d\') = :date', [':date' => $dateStart]);
        } else {
            $query->andWhere('date >= :dateStart AND date <= :dateStop', [':dateStart' => $dateStart, ':dateStop' => $dateStop]);
        }
        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();

        $this->resetConditions();

        return array_map('intval', $result);
    }
    public function getStatByModelAndDatePeriod($model, $dateStart, $dateStop, $where = null)
    {
        if($dateStop == '0000-00-00 00:00:00' | empty($dateStop)) {
            $dateStop = date('Y-m-d H:i:s');
        }
        $query = $this->orderQuery();
        $query->addSelect(['sum(e.count*e.price) as total, sum(e.count) as count_elements, COUNT(DISTINCT order_id) as count_orders'])
            ->from (['order_element e'])
            ->leftJoin('order', 'order.id = e.order_id')
            //->groupBy('order.id')
            ->andWhere('order.is_assigment != 1')
            ->andWhere('order.date >= :dateStart AND order.date <= :dateStop', [':dateStart' => $dateStart, ':dateStop' => $dateStop])
            ->andWhere(['e.model' => $model]);
        if($where) {
            $query->andWhere($where);
        }

        $result = $query->one();
        $this->resetConditions();

        if(!$result) {
            return ['total' => 0, 'count_elements' => 0, 'count_orders' => 0];
        }

        return array_map('intval', $result);
    }
}