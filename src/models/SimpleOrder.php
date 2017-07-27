<?php
namespace dvizh\order\models;

use yii;

class SimpleOrder extends \dvizh\order\models\Order
{

    public function rules()
    {
        return [
            [['phone', 'email'], 'string', 'skipOnEmpty' => true],
        ];
    }

    public function beforeValidate()
    {
        $this->client_name = "";
        $this->email = "";
        $this->phone = "";
        $this->time = time();

        return parent::beforeValidate();
    }
}
