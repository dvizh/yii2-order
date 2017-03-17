<?php
namespace dvizh\order\assets;

use yii\web\AssetBundle;

class AssigmentToOrder extends AssetBundle
{
    public $depends = [
        'dvizh\order\assets\Asset'
    ];

    public $js = [
        'js/assigment-to-order.js',
    ];

    public $css = [
        
    ];

    public function init()
    {
        $this->sourcePath = __DIR__ . '/../web';
        parent::init();
    }

}
