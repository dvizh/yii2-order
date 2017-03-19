<?php
namespace dvizh\order\models;

use yii;

class Element extends \yii\db\ActiveRecord implements \dvizh\app\interfaces\entities\OrderElement
{
    public static function tableName()
    {
        return '{{%order_element}}';
    }

    public function rules()
    {
        return [
            [['order_id', 'model', 'item_id'], 'required'],
            [['description', 'model', 'options', 'name'], 'string'],
            [['price'], 'double'],
            [['item_id', 'count', 'is_deleted'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => yii::t('order', 'ID'),
            'name' => yii::t('order', 'Name'),
            'price' => yii::t('order', 'Price'),
            'base_price' => yii::t('order', 'Base price'),
            'description' => yii::t('order', 'Description'),
            'options' => yii::t('order', 'Options'),
            'model' => yii::t('order', 'Model name'),
            'order_id' => yii::t('order', 'Order ID'),
            'item_id' => yii::t('order', 'Product'),
            'count' => yii::t('order', 'Count'),
            'is_assigment' => yii::t('order', 'Assigment'),
            'is_deleted' => yii::t('order', 'Deleted'),
        ];
    }

    public function setOrder(\dvizh\app\interfaces\entities\Order $order)
    {
        $this->order_id = $order->getId();
    }
    
    public function setAssigment($isAssigment)
    {
        $this->is_assigment = $isAssigment;
    }
    
    public function setModelName($modelName)
    {
        $this->model = $modelName;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    public function setItemId($itemId)
    {
        $this->item_id = $itemId;
    }
    
    public function setCount($count)
    {
        $this->count = $count;
    }
    
    public function setPrice($price)
    {
        $this->price = $price;
        
        return $this;
    }
    
    public function setOptions($options)
    {
        $this->options = $options;
        
        return $this;
    }
    
    public function setDescription($description)
    {
        $this->description = $description;
        
        return $this;
    }

    public function getOrderId()
    {
        return $this->order_id;
    }
    
    public function getAssigment()
    {
        return $this->is_assigment;
    }
    
    public function getModelName()
    {
        return $this->model;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function getItemId()
    {
        return $this->item_id;
    }
    
    public function getBasePrice()
    {
        return $this->base_price;
    }
    
    public function getOptions()
    {
        return $this->options;
    }
    
    public function getDescription()
    {
        return $this->description;
    }
    
    public function saveData()
    {
        return $this->save(false);
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getPrice()
    {
        return $this->price;
    }
    
    public function getCount()
    {
        return $this->count;
    }

    public function getOrder() : \dvizh\app\interfaces\entities\Order
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getProduct() : \dvizh\app\interfaces\entities\Goods
    {
        $modelStr = $this->model;
        $productModel = new $modelStr();

        return $this->hasOne($productModel::className(), ['id' => 'item_id'])->one();
    }
    
    public static function editField($id, $name, $value)
    {
        $setting = Element::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        
        return true;
    }
    
    public function beforeDelete()
    {
        parent::beforeDelete();

        return true;
    }
}
