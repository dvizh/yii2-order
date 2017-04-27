<?php
use yii\helpers\Html;
use yii\grid\GridView;
use dvizh\order\models\OrderFieldType;
use yii\helpers\ArrayHelper;

use dvizh\order\assets\Asset;
Asset::register($this);

$this->title = Yii::t('order', 'Payment types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="field-index">

    <div class="row">
        <div class="col-lg-2">
            <?= Html::a(Yii::t('order', 'Create payment type'), ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-lg-10">

        </div>
    </div>

    <hr />
    
    <?= \kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'export' => false,
        'columns' => [
            ['attribute' => 'id', 'options' => ['style' => 'width: 55px;']],
			'name',
            'widget',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
        ],
    ]); ?>

</div>
