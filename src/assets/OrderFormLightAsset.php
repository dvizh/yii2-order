<?php
namespace dvizh\order\assets;

use yii\web\AssetBundle;

class OrderFormLightAsset extends AssetBundle
{
    public $depends = [
        'dvizh\order\assets\Asset'
    ];

    public $css = [
        'css/orderFormLight.css',
    ];

    public $js = [
        'js/orderFormLight.js',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
