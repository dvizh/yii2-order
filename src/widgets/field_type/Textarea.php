<?php
namespace dvizh\order\widgets\field_type;

use dvizh\order\models\FieldValue;

class Textarea extends \yii\base\Widget
{
    public $fieldModel = null;
    public $form = null;
    public $defaultValue = '';
    
    public function run()
    {
        $fieldValueModel = new FieldValue;
        $fieldValueModel->value = $this->defaultValue;

        return $this->form->field($fieldValueModel, 'value['.$this->fieldModel->id.']')->label($this->fieldModel->name)->textArea(['value' => $this->defaultValue, 'required' => ($this->fieldModel->required == 'yes')]);
    }
}
