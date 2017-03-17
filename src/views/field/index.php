<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;

use dvizh\order\assets\Asset;
Asset::register($this);

$this->title = Yii::t('order', 'Fields');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/order/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="row">
        <div class="col-lg-2">
            <?= Html::a(Yii::t('order', 'Create field'), ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div class="col-lg-10">
            <?= $this->render('/parts/menu.php', ['active' => 'field']); ?>
        </div>
    </div>

    <hr />
    
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'options' => ['style' => 'width: 55px;']],
			'name',
			[
				'attribute' => 'type_id',
                'filter' => Html::activeDropDownList(
                    $searchModel,
                    'type_id',
                    $fieldTypes,
                    ['class' => 'form-control', 'prompt' => Yii::t('order', 'Type')]
				),
				'value' => function($model) use ($fieldTypes) {
					return  $fieldTypes[$model->type_id];
				}
			],
            'description',
            ['class' => 'yii\grid\ActionColumn', 'template' => '{update} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 145px;']],
        ],
    ]); ?>

</div>
