<?php
namespace dvizh\order\models;

use yii;

class FieldValueVariant extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_field_value_variant}}';
    }

    public function rules()
    {
        return [
            [['field_id'], 'required'],
            [['value'], 'string'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => yii::t('order', 'ID'),
            'field_id' => yii::t('order', 'Field'),
            'value' => yii::t('order', 'Value'),
        ];
    }
    
    public static function editField($id, $name, $value)
    {
        $setting = FieldValueVariant::findOne($id);
        $setting->$name = $value;
        $setting->save();
    }
}
