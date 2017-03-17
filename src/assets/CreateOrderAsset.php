<?php
namespace dvizh\order\assets;

use yii\web\AssetBundle;

class CreateOrderAsset extends AssetBundle
{
    public $depends = [
        'dvizh\order\assets\Asset'
    ];

    public $js = [
        'js/createorder.js',
    ];

    public $css = [
        'css/createorder.css',
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
