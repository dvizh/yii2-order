<?php
namespace dvizh\order\interfaces;

interface OrderElement
{
    public function getOrderId();
    public function getAssigment();
    public function getModelName();
    public function getName();
    public function getItemId();
    public function getCount();
    public function getBasePrice();
    public function getPrice();
    public function getOptions();
    public function getDescription();
    
    public function setOrderId($orderId);
    public function setAssigment($isAssigment);
    public function setModelName($modelName);
    public function setName($name);
    public function setItemId($itemId);
    public function setCount($count);
    public function setBasePrice($basePrice);
    public function setPrice($price);
    public function setOptions($options);
    public function setDescription($description);
    public function saveData();
}
