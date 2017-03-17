<?php
namespace dvizh\order\logic;

class ElementCancel
{
    private $element;
    public $elementId;

    public function __construct(\dvizh\dic\interfaces\order\OrderElementService $element, $config = [])
    {
        $this->element = $element;
    }

    public function execute()
    {
        $element = $this->element->getById($this->elementId);

        $element->setDeleted(1);
        $element->saveData();

        yii::createObject(['class' => CountCalculate::class, 'orderId' => $element->getOrder()->id])->execute();
        yii::createObject(['class' => CostCalculate::class, 'orderId' => $element->getOrder()->id])->execute();
        
        return true;
    }
}