<?php
namespace dvizh\order\assets;

use yii\web\AssetBundle;

class BuyByCodeAsset extends AssetBundle
{
    public $depends = [
        'dvizh\order\assets\Asset'
    ];

    public $js = [
        'js/buybycode.js',
        'js/createorder.js',
    ];

    public $css = [
        
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
