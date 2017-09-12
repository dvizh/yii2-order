<?php
namespace dvizh\order\models;

use yii;
use dvizh\order\models\tools\OrderQuery;
use dvizh\order\interfaces\Order as OrderInterface;

class Order extends \yii\db\ActiveRecord implements OrderInterface
{
    public $sessionId;

    public static function tableName()
    {
        return '{{%order}}';
    }

    public static function find()
    {
        $query = new OrderQuery(get_called_class());
        
        return $query->with('elementsRelation');
    }

    public function rules()
    {
        return [
            [['client_name'], 'required'],
            [['phone', 'email'], 'emailAndPhoneValidation', 'skipOnEmpty' => false],
            [['date', 'payment', 'comment', 'delivery_time', 'address'], 'string'],
            ['status', 'in', 'range' => array_keys(yii::$app->getModule('order')->orderStatuses)],
            ['email', 'email'],
            [['phone'], 'udokmeci\yii2PhoneValidator\PhoneValidator', 'country' => yii::$app->getModule('order')->countryCode],
            [['status', 'date', 'payment', 'client_name', 'phone', 'email', 'comment', 'delivery_time_date', 'delivery_type', 'address'], 'safe'],
            [['seller_user_id', 'cost', 'base_cost', 'organization_id', 'shipping_type_id', 'payment_type_id', 'delivery_time_hour', 'delivery_time_min', 'is_deleted', 'is_assigment'], 'integer'],
        ];
    }

    public function emailAndPhoneValidation($attribute, $params)
    {
        if(empty($this->phone) && empty($this->email)) {
            $this->addError($attribute, yii::t('order', 'Phone or email is required'));
        }
    }

    public function attributeLabels()
    {
        return [
            'id' => yii::t('order', 'ID'),
            'client_name' => yii::t('order', 'Client name'),
            'shipping_type_id' => yii::t('order', 'Delivery'),
            'delivery_time_date' => yii::t('order', 'Delivery date'),
            'delivery_time_hour' => yii::t('order', 'Delivery hour'),
            'delivery_time_min' => yii::t('order', 'Delivery minute'),
            'delivery_type' => yii::t('order', 'Delivery time'),
            'payment_type_id' => yii::t('order', 'Payment type'),
            'comment' => yii::t('order', 'Comment'),
            'phone' => yii::t('order', 'Phone'),
            'promocode' => yii::t('order', 'Promocode'),
            'date' => yii::t('order', 'Date'),
            'email' => yii::t('order', 'Email'),
            'payment' => yii::t('order', 'Paid'),
            'status' => yii::t('order', 'Status'),
            'time' => yii::t('order', 'Time'),
            'user_id' => yii::t('order', 'User ID'),
            'count' => yii::t('order', 'Count'),
            'cost' => yii::t('order', 'Cost'),
            'base_cost' => yii::t('order', 'Base cost'),
            'seller_user_id' => yii::t('order', 'Seller'),
            'address' => yii::t('order', 'Address'),
            'organization_id' => yii::t('order', 'organization'),
            'is_assigment' => yii::t('order', 'Assigment'),
            'is_deleted' => yii::t('order', 'Deleted'),
        ];
    }

    public function scenarios()
    {
        return [
            'customer' => ['promocode', 'comment', 'client_name', 'shipping_type_id', 'payment_type_id', 'phone', 'email', 'delivery_time_date', 'delivery_time_hour', 'delivery_time_min', 'delivery_type', 'address'],
            'admin' => array_keys($this->attributeLabels()),
            'default' => array_keys($this->attributeLabels()),
        ];
    }

    public function setDeleted($deleted)
    {
        $this->is_deleted = $deleted;

        return $this;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;
        
        return $this;
    }

    public function cancel()
    {
        $this->is_deleted = 1;

        return $this->save(false);
    }

    public function restore()
    {
        $this->is_deleted = 0;

        return $this->save(false);
    }

    public function saveData()
    {
        return $this->save(false);
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getCost()
    {
        return $this->cost;
    }
    
    function setPaymentStatus($status)
    {
        $this->payment = $status;
        
        return $this;
    }
    
    public function getTotal()
    {
        return floatVal($this->hasMany(Element::className(), ['order_id' => 'id'])->sum('price*count'));
    }

    public function getTotalFormatted()
    {
        $priceFormat = yii::$app->getModule('order')->priceFormat;
        $price = number_format($this->getPrice(), $priceFormat[0], $priceFormat[1], $priceFormat[2]);
        $currency = yii::$app->getModule('order')->currency;
        if (yii::$app->getModule('order')->currencyPosition == 'after') {
            return "$price $currency";
        } else {
            return "$currency $price";
        }
    }

    public function getField($fieldId = null)
    {
        if($field = FieldValue::find()->where(['order_id' => $this->id, 'field_id' => $fieldId])->one()) {
            return $field->value;
        }

        return null;
    }

    public function setField($fieldId, $fieldValue)
    {
        if ($field = FieldValue::find()->where(['order_id' => $this->id, 'field_id' => $fieldId])->one()) {
            $field->value = $fieldValue;
            return $field->save();
        }
        $field = new FieldValue();

        $field->field_id = $fieldId;
        $field->value = $fieldValue;
        $field->order_id = $this->id;
        return $field->save();
    }

    public function getPaymentType()
    {
        return $this->hasOne(PaymentType::className(), ['id' => 'payment_type_id']);
    }
    
    public function getUser()
    {
        $userModel = yii::$app->getModule('order')->userModel;
        if($userModel && class_exists($userModel)) {
            return $this->hasOne($userModel::className(), ['id' => 'seller_user_id']);
        }
        
        return null;
    }
    
    public function getClient()
    {
        return $this->getUser();
    }
    
    public function getSeller()
    {
        $userModel = yii::$app->getModule('order')->sellerModel;
        if($userModel && class_exists($userModel)) {
            return $this->hasOne($userModel::className(), ['id' => 'seller_user_id']);
        }
        
        return null;
    }
    
    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['order_id' => 'id']);
    }
    
    public function getShipping()
    {
        return $this->hasOne(ShippingType::className(), ['id' => 'shipping_type_id']);
    }
    
    public function getCount()
    {
        return intval($this->hasMany(Element::className(), ['order_id' => 'id'])->sum('count'));
    }

    public function getFields()
    {
        return $this->hasMany(FieldValue::className(), ['order_id' => 'id']);
    }
    
    public function getAllFields()
    {
        return Field::find()->all();
    }
    
    public function getElementsRelation()
    {
        return $this->hasMany(Element::className(), ['order_id' => 'id'])->where('({{%order_element}}.is_deleted IS NULL OR {{%order_element}}.is_deleted != 1)');
    }
    
    public function getElements($withModel = true)
    {
        $returnModels = [];
        $elements = $this->getElementsRelation()->all();
        foreach ($elements as $element) {
            if (is_string($element->model) && $withModel && class_exists($element->model)) {
                $model = '\\'.$element->model;
                $productModel = new $model();
                if ($productModel = $productModel::findOne($element->item_id)) {
                    $element->model = $productModel;
                }
            }
            $returnModels[$element->id] = $element;
        }
        
        return $returnModels;
    }

    public function getElementById($id)
    {
        return $this->hasMany(Element::className(), ['order_id' => 'id'])->andWhere(['id' => $id])->one();
    }

    public function haveModelElements($modelName)
    {
        if ($this->hasMany(Element::className(), ['order_id' => 'id'])->andWhere(['model' => $modelName])->one()) {
            return true;
        } else {
            return false;
        }
    }
    
    public function beforeSave($insert)
    {
        if(empty($this->timestamp)) {
            $this->timestamp = time();
        }
        
        if($this->isNewRecord) {
            if(empty($this->date)) {
                $this->date = date('Y-m-d H:i:s');
            } elseif (empty($this->timestamp)) {
                $this->timestamp = strtotime($this->date);
            }
        }
        
        return parent::beforeSave($insert);
    }
}
