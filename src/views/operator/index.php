<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use dvizh\order\widgets\Informer;
use \yii\widgets\Pjax;

$this->title = yii::t('order', 'Operator area');
$this->params['breadcrumbs'][] = $this->title;

use dvizh\order\assets\Asset;
Asset::register($this);

if($dateStart = yii::$app->request->get('date_start')) {
    $dateStart = date('Y-m-d', strtotime($dateStart));
}

if($dateStop = yii::$app->request->get('date_stop')) {
    $dateStop = date('Y-m-d', strtotime($dateStop));
}

$columns = [];

$columns[] = ['attribute' => 'id', 'options' => ['style' => 'width: 49px;']];
$columns[] = [
    'attribute' => 'count',
    'label' => yii::t('order', 'Cnt'),
    'content' => function($model) {
        return $model->count;
    }
];

$columns[] = [
    'attribute' => 'cost',
    'label' => yii::$app->getModule('order')->currency,
    'content' => function($model) {
        $total = Html::tag('span', $model->cost, ['class' => 'view-url', 'data-href' => Url::toRoute(['/order/operator/view', 'id' => $model->id])]);
        if($model->promocode) {
            $total .= Html::tag('div', $model->promocode, ['style' => 'color: orange; font-size: 80%;', yii::t('order', 'Promocode')]);
        }

        return $total;
    },
];

            
foreach(Yii::$app->getModule('order')->orderColumns as $column) {
    if($column == 'payment_type_id') {
        $column = [
            'attribute' => 'payment_type_id',
            'filter' => Html::activeDropDownList(
                $searchModel,
                'payment_type_id',
                $paymentTypes,
                ['class' => 'form-control', 'prompt' => Yii::t('order', 'Payment type')]
            ),
            'value' => function($model) use ($paymentTypes) {
                if(isset($paymentTypes[$model->payment_type_id])) {
                    return $paymentTypes[$model->payment_type_id];
                }
            }
        ];
    } elseif($column == 'shipping_type_id') {
        $column = [
            'attribute' => 'shipping_type_id',
            'filter' => Html::activeDropDownList(
                $searchModel,
                'shipping_type_id',
                $shippingTypes,
                ['class' => 'form-control', 'prompt' => Yii::t('order', 'Shipping type')]
            ),
            'value' => function($model) use ($shippingTypes) {
                if(isset($shippingTypes[$model->shipping_type_id])) {
                    return $shippingTypes[$model->shipping_type_id];
                }
            }
        ];
    } elseif(is_array($column) && isset($column['field'])) {
        $column = [
            'attribute' => 'field',
            'label' => $column['label'],
            'value' => function($model) use ($column) {
                return $model->getField($column['field']);
            }
        ];
    }
    
    $columns[] = $column;
}

$columns[] = [
    'attribute' => 'date',
    'filter' => false,
    'format' => 'html',
    'value' => function($model) {
        return '<span title="'.date('d.m.Y H:i:s', $model->timestamp).'">'.date('H:i:s', $model->timestamp).'</span>';
    }
];
        
$columns[] = [
    'attribute' => 'status',
    'format' => 'html',
    'filter' => Html::activeDropDownList(
        $searchModel,
        'status',
        yii::$app->getModule('order')->orderStatuses,
        ['class' => 'form-control', 'prompt' => Yii::t('order', 'Status')]
    ),
    'value'	=> function($model) {
        return  Html::tag('span', Yii::$app->getModule('order')->orderStatuses[$model->status], ['class' => 'status_'.$model->status]);
    }
];
?>

<div class="order-index">
    <style>
        .operatorka tr {
            font-size: 13px;
            min-height: 60px;
            cursor: pointer;
        }
        
        .operatorka tr:hover td {
            box-shadow: 0 0 6px rgba(195, 198, 236, 0.5);
        }
        
        .operatorka .status_new {
            padding: 4px;
            background-color: #CFE3DE;
        }
        
        .operatorka .status_process {
            padding: 4px;
            background-color: #CCCCCC;
        }
        
        .operatorka .status_done {
            padding: 4px;
            background-color: #6D948A;
        }
        
        .operatorka .cancel {
            padding: 4px;
            background-color: #C9B9BB;
        }
        
        #operatorkaModal .modal-body {
            height: 100vh;
        }
        
        #operatorkaModal .modal-body iframe {
            border: 0px;
            width: 100%;
            height: 100%;
        }
    </style>

    <script>
    window.onload = function() {
        setInterval(function() {
            if($('.pagination .active a').html() == '1' | !$('.pagination .active a').html()) {
                $('.operator-update').click();
            }
            
        }, 5000);
        
        $(document).on('click', '.operatorka tbody td', function() {
            $('#operatorkaModal .modal-body').html('<iframe src="'+$(this).parent('tr').find('.view-url').data('href')+'"></iframe>');
            $('#operatorkaModal').modal().show();
        });
    }
    </script>

    <div class="box">
        <div class="box-body">
            <div class="order-list operatorka">
                <?php Pjax::begin(); ?>
                    <a href="<?=Url::toRoute(['/order/operator/index']);?>" class="operator-update"> <i class="glyphicon glyphicon-refresh"></i> Обновить</a>
                    <?=  \kartik\grid\GridView::widget([
                        'export' => false,
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => $columns,
                    ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>


<!-- Default bootstrap modal example -->
<div class="modal fade" id="operatorkaModal" tabindex="-1" role="dialog" aria-labelledby="operatorkaModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel"><?=yii::t('order', 'Order'); ?></h4>
      </div>
      <div class="modal-body">
        
      </div>
    </div>
  </div>
</div>
