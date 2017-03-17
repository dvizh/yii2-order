<?php
namespace dvizh\order\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class BuyByCode extends \yii\base\Widget
{
    public function init()
    {
        parent::init();

        \dvizh\order\assets\BuyByCodeAsset::register($this->getView());

        return true;
    }

    public function run()
    {
        $input = Html::input('text', 'code', '', ['class' => 'form-control buy-by-code-input', 'placeholder' => yii::t('order', 'Code')]);
        
        $error = Html::tag('div', '', ['class' => 'error-block', 'style' => 'color: red; position: absolute; top: -17px;']);
        
        $button = Html::tag('button', '<i class="glyphicon glyphicon-plus"></i>', ['data-href' => Url::toRoute(['tools/buy-product-by-code']), 'type' => 'submit', 'class' => 'btn btn-success promo-code-enter-btn']);
        $button = Html::tag('span', $button, ['class' => 'input-group-btn']);
        
        return Html::tag('div', $error.$input.$button, ['class' => 'input-group buy-by-code']);
    }
}