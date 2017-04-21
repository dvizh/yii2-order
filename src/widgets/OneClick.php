<?php
namespace dvizh\order\widgets;

use yii\helpers\Html;
use dvizh\order\models\Order;
use yii;

class OneClick extends \yii\base\Widget
{
    
    public $view = 'oneclick/form';
    public $model = null;
    public $content = null;
    public $mode = 'popup'; //or inline
    public $showComment = true;
    
    public function init()
    {
        if(is_null($this->content)) {
            $this->content = yii::t('order', 'Fast order');
        }
        
        \dvizh\order\assets\OneClickAsset::register($this->getView());
        
        return parent::init();
    }
    
    public function run()
    {
        $model = new Order;
        
        if($this->mode == 'popup') {
            $view = $this->getView();
            $view->on($view::EVENT_END_BODY, function($event) use ($model) {
                echo $this->render($this->view, ['mode' => $this->mode, 'showComment' => $this->showComment, 'model' => $this->model, 'orderModel' => $model]);
            });

            return Html::a($this->content, '#fastOrder'.$this->model->id, ['title' => yii::t('order', 'Fast order'), 'data-toggle' => 'modal', 'data-target' => '#fastOrder'.$this->model->id, 'class' => 'btn btn-success']);
        } else {
            return $this->render($this->view, ['mode' => $this->mode, 'showComment' => $this->showComment, 'model' => $this->model, 'orderModel' => $model]);
        }
    }
}
