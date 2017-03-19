<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

use dvizh\order\assets\Asset;
Asset::register($this);

$this->title = Yii::t('order', 'Shipping types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/order/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="shipping-type-index">

    <h1><?= Html::encode($this->title) ?></h1>
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'options' => ['style' => 'width: 55px;']],
			'name',
			'cost',
            'description',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 145px;']],
        ],
    ]); ?>

</div>
