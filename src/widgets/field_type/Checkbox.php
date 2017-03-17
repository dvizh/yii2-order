<?php
namespace dvizh\order\widgets\field_type;

use dvizh\order\models\FieldValue;

class Checkbox extends \yii\base\Widget
{
    public $fieldModel = null;
    public $form = null;
    public $defaultValue = '';
    
    public function run()
    {
        $fieldValueModel = new FieldValue;
        $fieldValueModel->value = $this->defaultValue;
        
        return $this->form->field($fieldValueModel, 'value['.$this->fieldModel->id.']')->label($this->fieldModel->name)->checkbox(['required' => ($this->fieldModel->required == 'yes'),'label' => $this->fieldModel->name]);
    }
}
