<?php
namespace dvizh\order\assets;

use yii\web\AssetBundle;

class OrderFormAsset extends AssetBundle
{
    public $depends = [
        'dvizh\order\assets\Asset'
    ];
    public $js = [
        'js/order-form.js',
    ];
    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }
}