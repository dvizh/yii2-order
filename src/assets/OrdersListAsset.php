<?php
namespace dvizh\order\assets;

use yii\web\AssetBundle;

class OrdersListAsset extends AssetBundle
{
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public $js = [
        'js/orders-list.js',
    ];

    public $css = [
        'css/orders-list.css',
    ];

    public function init()
    {
        $this->sourcePath = dirname(__DIR__).'/web';
        parent::init();
    }
}
