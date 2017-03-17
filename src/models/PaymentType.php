<?php
namespace dvizh\order\models;

use yii;

class PaymentType extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_payment_type}}';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['order'], 'integer'],
            [['widget'], 'validateWidget'],
        ];
    }

    public function validateWidget($attribute, $param)
    {
        $widget = $this->widget;
        
        if(trim($widget) == '') {
            return true;
        }
        
        if (class_exists($widget)) {
            $widgetEx = new $widget();
            if (!$widgetEx instanceof \yii\base\Widget) {
                $this->addError($attribute, 'Widget type error');
            }
        } else {
            $this->addError($attribute, 'Widget not exists');
        }
        
        return true;
    }

    public function attributeLabels()
    {
        return [
            'name' => yii::t('order', 'Name'),
            'order' => yii::t('order', 'Sort'),
            'widget' => yii::t('order', 'Widget'),
        ];
    }
}
