<?php

namespace dvizh\order\models;

use yii;
use dvizh\order\interfaces\OrderElement as ElementInterface;

class Element extends \yii\db\ActiveRecord implements ElementInterface
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

    public function setOrderId($orderId)
    {
        $this->order_id = $orderId;
        
        return $this;
    }
    
    public function setAssigment($isAssigment)
    {
        $this->is_assigment = $isAssigment;
        
        return $this;
    }
    
    public function setModelName($modelName)
    {
        $this->model = $modelName;
        
        return $this;
    }
    
    public function setName($name)
    {
        $this->name = $name;
        
        return $this;
    }
    
    public function setItemId($itemId)
    {
        $this->item_id = $itemId;
        
        return $this;
    }
    
    public function setCount($count)
    {
        $this->count = $count;
    }
    
    public function setBasePrice($basePrice)
    {
        $this->base_price = $basePrice;
        
        return $this;
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
        return $this->save();
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
    
    public function getProduct()
    {
        $modelStr = $this->model;
        $productModel = new $modelStr();
        
        return $this->hasOne($productModel::className(), ['id' => 'item_id'])->one();
    }
    
    public function getOrder()
    {
        return $this->hasOne(Order::className(), ['id' => 'order_id']);
    }

    public function getModel($withCartElementModel = true)
    {
        if(!$withCartElementModel) {
            return $this->model;
        }

        if(is_string($this->model)) {
            if(class_exists($this->model)) {
                $model = '\\'.$this->model;
                $productModel = new $model();
                if ($productModel = $productModel::findOne($this->item_id)) {
                    $model = $productModel;
                } else {
                    throw new \yii\base\Exception('Element model do not found');
                }
            } else {
                //throw new \yii\base\Exception('Unknow element model');
            }
        } else {
            $model = $this->model;
        }
        
        return $model;
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
