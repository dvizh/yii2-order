<div class="order-menu">
    <div class="menu-container">
        <ul class="nav-pills nav">
<?php
use yii\helpers\Html;

$menu = [
    'payment' => ['url' => '/order/payment/index', 'name' => ' '.Yii::t('order', 'Payments')],
    'payment-type' => ['url' => '/order/payment-type/index', 'name' => ' '.Yii::t('order', 'Payment types')],
    'shipping-type' => ['url' => '/order/shipping-type/index', 'name' => ' '.Yii::t('order', 'Shipping types')],
    'field' => ['url' => '/order/field/index', 'name' => ' '.Yii::t('order', 'Fields')],
    'operator' => ['url' => '/order/operator/index', 'name' => ' '.Yii::t('order', 'Operator area')],
    'orders' => ['url' => '/order/order/index', 'name' => ' '.Yii::t('order', 'Orders')],
];

if(yii::$app->user->can(current(yii::$app->getModule('order')->adminRoles))) {
    $menu['statistics'] = ['url' => '/order/stat/index', 'name' => ' '.Yii::t('order', 'Order statistics')];
}

if(!isset($active)) {
    $active = false;
}

foreach($menu as $key => $params) {
    if(in_array($key, yii::$app->getModule('order')->adminMenu)) {
        if($active == $key) {
            $class = 'active';
        } else {
            $class = '';
        }
        echo Html::tag('li', Html::a($params['name'], [$params['url']], ['class' => $class]), ['class' => 'nav-link ']);
    }    
}
?>
        </ul>
    </div>
</div>