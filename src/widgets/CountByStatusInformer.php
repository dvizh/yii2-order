<?php
namespace dvizh\order\widgets;

use yii;
use yii\helpers\Html;
use yii\helpers\Url;
use dvizh\order\models\Order;
use yii\helpers\ArrayHelper;

class CountByStatusInformer extends \yii\base\Widget
{
    public $customView = false; // для подключения своего view
    public $url = ['/order/order/index']; // url
    public $statuses = ['new']; // статусы, какие считаем
    public $aTagCssClass = ''; // css-class для тэга <a></a>
    public $iTagCssClass = 'fa fa-bell-o'; // css-class для тэга <i></i>
    public $spanTagCssClass = 'label label-warning'; // css-class для тэга <span></span>
    public $renderEmpty = false; // отображать если количество 0
    public $aTag = true; // оборачивать в ссыдку

    public function init()
    {
        parent::init();

        return true;
    }

    public function run()
    {

        $count = Order::find()->where(['status' => $this->statuses, 'is_deleted' => 0])->count();

        $return = '';

        if ($count === '0' && $this->renderEmpty === false) {
            return $return;
        }

        if ($this->customView) {
            return $this->render($this->customView, ['count' => $count]);
        }

        $return .= Html::tag('i', '', ['class' => $this->iTagCssClass]);
        $return .= Html::tag('span', $count, ['class' => $this->spanTagCssClass]);

        if ($this->aTag) {
            $return = Html::a($return, $this->url, ['class' => $this->aTagCssClass]);
        }

        return $return;

    }
}
