<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

use dvizh\order\assets\Asset;
Asset::register($this);

$this->title = Yii::t('order', 'Shipping types');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/default/index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="field-index">

    <div class="row">
        <div class="col-lg-2">
            <?= Html::a(Yii::t('order', 'Create shipping type'), ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-lg-10">

        </div>
    </div>

    <hr />
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'options' => ['style' => 'width: 55px;']],
			'name',
			'cost',
            'description',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}'],
        ],
    ]); ?>

</div>
