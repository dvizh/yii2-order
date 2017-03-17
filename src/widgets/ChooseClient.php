<?php
namespace dvizh\order\widgets;

use yii\helpers\Html;
use yii\helpers\Url;
use yii;

class ChooseClient extends \yii\base\Widget
{
    public $model = null;
    public $form = null;
    
    public function init()
    {
        parent::init();

        \dvizh\order\assets\CreateOrderAsset::register($this->getView());

        return true;
    }
    
    public function run()
    {
        $view = $this->getView();
        $view->on($view::EVENT_END_BODY, function($event) {
            echo $this->render('choose_client_modal', ['model' => $this->model, 'form' => $this->form]);
        });
        
        return $this->render('choose_client', ['model' => $this->model, 'form' => $this->form]);
    }
}
