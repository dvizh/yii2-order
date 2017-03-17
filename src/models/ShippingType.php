<?php

namespace dvizh\order\models;

use yii;

class ShippingType extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_shipping_type}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['order'], 'integer'],
            [['cost', 'free_cost_from'], 'double'],
            [['description'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'name' => yii::t('order', 'Name'),
            'order' => yii::t('order', 'Sort'),
            'cost' => yii::t('order', 'Cost'),
            'free_cost_from' => yii::t('order', 'Free cost from'),
        ];
    }
}
