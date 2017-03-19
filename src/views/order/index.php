<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use dvizh\order\widgets\Informer;
use kartik\export\ExportMenu;
use nex\datepicker\DatePicker;
use dvizh\order\assets\Asset;
use dvizh\order\assets\OrdersListAsset;
use dvizh\order\widgets\AssigmentToOrder;

$this->title = yii::t('order', 'Orders');
$this->params['breadcrumbs'][] = $this->title;


Asset::register($this);
OrdersListAsset::register($this);

if($dateStart = yii::$app->request->get('date_start')) {
    $dateStart = date('Y-m-d', strtotime($dateStart));
}

if($dateStop = yii::$app->request->get('date_stop')) {
    $dateStop = date('Y-m-d', strtotime($dateStop));
}

$timeStart = yii::$app->request->get('time_start');
$timeStop = yii::$app->request->get('time_stop');

$columns = [];

$columns[] = [
    'class' => \yii\grid\SerialColumn::className(),
];

// $columns[] = [
//     'attribute' => 'id',
//     'options' => ['style' => 'width: 49px;'],
//     'contentOptions' => [
//         'class' => 'show-details'
//     ],
// ];

$columns[] = [
    'attribute' => 'count',
    'label' => yii::t('order', 'Cnt'),
    'contentOptions' => [
        'class' => 'show-details'
    ],
    'content' => function($model) {
        return $model->count;
    }
];

$columns[] = [
    'attribute' => 'base_cost',
    'label' => yii::$app->getModule('order')->currency,
    'contentOptions' => [
        'class' => 'show-details'
    ],
];

$columns[] = [
    'attribute' => 'cost',
    'label' => '%',
    'contentOptions' => [
        'class' => 'show-details'
    ],
    'content' => function($model) {
        $total = $model->cost;
        if($model->promocode) {
            $total .= Html::tag('div', $model->promocode, ['style' => 'color: orange; font-size: 80%;', yii::t('order', 'Promocode')]);
        }

        return $total;
    },
];

if(Yii::$app->getModule('order')->showPaymentColumn){
    $columns[] = [
        'attribute' => 'payment',
        'filter' => Html::activeDropDownList(
            $searchModel,
            'payment',
            ['yes' => yii::t('order', 'yes'), 'no' => yii::t('order', 'no')],
            ['class' => 'form-control', 'prompt' => Yii::t('order', 'Paid')]
        ),
        'value' => function($model){
            return yii::t('order', $model->payment);
        }
    ];
 }

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
            'contentOptions' => [
                'class' => 'show-details'
            ],
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
            'contentOptions' => [
                'class' => 'show-details'
            ],
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
            'contentOptions' => [
                'class' => 'show-details'
            ],
            'value' => function($model) use ($column) {
                return $model->getField($column['field']);
            }
        ];
    }

    if (gettype($column) === 'string') {
        $column = [
            'attribute' => $column,
            'contentOptions' => [
                'class' => 'show-details'
            ],
        ];
    }

    $columns[] = $column;
}

$columns[] = [
    'attribute' => 'date',
    'filter' => false,
    'contentOptions' => [
        'class' => 'show-details'
    ],
    'value' => function($model) {
        return date(yii::$app->getModule('order')->dateFormat, $model->timestamp);
    }
];

$columns[] = [
    'attribute' => 'status',
    'filter' => Html::activeDropDownList(
        $searchModel,
        'status',
        yii::$app->getModule('order')->orderStatuses,
        ['class' => 'form-control', 'prompt' => Yii::t('order', 'Status')]
    ),
    'format' => 'raw',
    'value' => function($model) use ($module) {
        if(!$model->status) {
            return null;
        }

        return Yii::$app->getModule('order')->orderStatuses[$model->status];
    }
];

if($module->elementToOrderUrl) {
    $columns[] = [
        'label' => yii::t('order', 'Add to order'),
        'content' => function($model) use ($module) {
            return '<a href="'.Url::toRoute([$module->elementToOrderUrl, 'order_id' => $model->id]).'" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i></a>';
        }
    ];
}

$columns[] = ['class' => 'yii\grid\ActionColumn', 'template' => '{view} {delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 100px;']];


$order = yii::$app->order;
?>


<h1><?=yii::t('order', 'Orders');?></h1>

<div class="informer-widget">
    <div class="panel panel-primary">
        <div class="panel-heading">
            <h3 class="panel-title"><a href="#" onclick="$('.order-statistics-body').toggle(); return false;"><?=yii::t('order', 'Statistics');?>...</a></h3>
        </div>
        <div class="order-statistics-body" style="display: none;">
            <div class="panel-body">
                <?=Informer::widget();?>
            </div>
        </div>
    </div>
</div>

<div class="order-index">
    <div class="box">
        <div class="box-body">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title"><a href="#" onclick="$('.order-search-body').toggle(); return false;"><?=yii::t('order', 'Search');?>...</a></h3>
                </div>
                <div class="order-search-body" <?php if(!yii::$app->request->get('OrderSearch') && !yii::$app->request->get('time_start')) { ?> style="display: none;"<?php } ?>>
                    <div class="panel-body">
                        <?php if(true) { ?>
                            <form action="<?=Url::toRoute(['/order/order/index']);?>" class="row search">
                                <input type="hidden" name="tab" value="<?=$tab;?>" />
                                <?php
                                if($module->orderColumns) {
                                    echo '<div class="col-md-2">';
                                    foreach($module->orderColumns as $column) {
                                        if(is_array($column) && isset($column['field'])) {
                                            ?>
                                            <div>
                                                <label for="custom-field-<?=$column['field'];?>"><?=$column['label'];?></label>
                                                <input class="form-control" type="text" name="order-custom-field[<?=$column['field'];?>]" value="<?=Html::encode(yii::$app->request->get('order-custom-field')[$column['field']]);?>" id="custom-field-<?=$column['field'];?>" />
                                            </div>
                                            <?php
                                        }
                                    }
                                    echo '</div>';
                                }
                                if(Yii::$app->getModule('order')->showPaymentColumn){
                                    ?>
                                    <div class="col-md-2">
                                        <label><?=yii::t('order', 'Paid');?></label>
                                        <select class="form-control" name="OrderSearch[payment]">
                                            <option value="">Все</option>
                                            <?php foreach(['yes' => yii::t('order', 'yes'), 'no' => yii::t('order', 'no')] as $status => $statusName) { ?>
                                                <option <?php if($status == yii::$app->request->get('OrderSearch')['payment']) echo ' selected="selected"';?> value="<?=$status;?>"><?=$statusName;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="col-md-4">
                                    <label><?=yii::t('order', 'Date');?></label>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <?= DatePicker::widget([
                                                'name' => 'date_start',
                                                'addon' => false,
                                                'value' => $dateStart,
                                                'size' => 'sm',
                                                'language' => 'ru',
                                                'placeholder' => yii::t('order', 'Date from'),
                                                'clientOptions' => [
                                                    'format' => 'L',
                                                    'minDate' => '2015-01-01',
                                                    'maxDate' => date('Y-m-d'),
                                                ],
                                                'dropdownItems' => [
                                                    ['label' => 'Yesterday', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('-1 day')],
                                                    ['label' => 'Tomorrow', 'url' => '#', 'value' => \Yii::$app->formatter->asDate('+1 day')],
                                                    ['label' => 'Some value', 'url' => '#', 'value' => 'Special value'],
                                                ],
                                            ]);?>
                                            <?php if($timeStart && !yii::$app->request->get('OrderSearch')) { ?>
                                                <input type="hidden" name="time_start" value="<?=Html::encode($timeStart);?>" />
                                                <p><small><?=yii::t('order', 'Date from');?>: <?=Html::encode($timeStart);?></small></p>
                                            <?php } ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?= DatePicker::widget([
                                                'name' => 'date_stop',
                                                'addon' => false,
                                                'value' => $dateStop,
                                                'size' => 'sm',
                                                'placeholder' => yii::t('order', 'Date to'),
                                                'language' => 'ru',
                                                'clientOptions' => [
                                                    'format' => 'L',
                                                    'minDate' => '2015-01-01',
                                                    'maxDate' => date('Y-m-d'),
                                                ],
                                                'dropdownItems' => [
                                                    ['label' => yii::t('order', 'Yesterday'), 'url' => '#', 'value' => \Yii::$app->formatter->asDate('-1 day')],
                                                    ['label' => yii::t('order', 'Tomorrow'), 'url' => '#', 'value' => \Yii::$app->formatter->asDate('+1 day')],
                                                    ['label' => yii::t('order', 'Some value'), 'url' => '#', 'value' => 'Special value'],
                                                ],
                                            ]);?>
                                            <?php if($timeStop && !yii::$app->request->get('OrderSearch')) { ?>
                                                <input type="hidden" name="time_stop" value="<?=Html::encode($timeStop);?>" />
                                                <p><small><?=yii::t('order', 'Date to');?>: <br /><?=Html::encode($timeStop);?></small></p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                                <?php if($module->orderStatuses) { ?>
                                    <div class="col-md-2">
                                        <label><?=yii::t('order', 'Status');?></label>
                                        <select class="form-control" name="OrderSearch[status]">
                                            <option value="">Все</option>
                                            <?php foreach($module->orderStatuses as $status => $statusName) { ?>
                                                <option <?php if($status == yii::$app->request->get('OrderSearch')['status']) echo ' selected="selected"';?> value="<?=$status;?>"><?=$statusName;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>

                                <?php if($sellers = yii::$app->getModule('order')->getSellerList()) { ?>
                                    <div class="col-md-2">
                                        <select class="form-control" name="OrderSearch[seller_user_id]">
                                            <option value=""><?=yii::t('order', 'Seller');?></option>
                                            <?php foreach($sellers as $seller) { ?>
                                                <option <?php if($seller->id == yii::$app->request->get('OrderSearch')['seller_user_id']) echo ' selected="selected"';?> value="<?=$seller->id;?>"><?=$seller->username;?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                                
                                <div class="col-md-2">
                                    <input type="checkbox" <?php if(yii::$app->request->get('promocode')) echo ' checked="checked"'; ?> name="promocode" value="1" id="order-promocode" />
                                    <label for="order-promocode"><?=yii::t('order', 'Promocode');?></label>
                                    <input class="btn btn-success form-control" type="submit" value="<?=Yii::t('order', 'Search');?>"  />
                                    <a href="<?=Url::toRoute(['/order/order/index', 'tab' => $tab]);?>" class="btn btn-default form-control"><i class="glyphicon glyphicon-remove-sign"></i> <?=Yii::t('order', 'Reset');?></a>
                                </div>
                            </form>
                        <?php } ?>
                    </div>
                </div>
            </div>

            <div class="tabs row">
                <div class="col-md-6">
                    <ul class="nav nav-tabs" role="tablist">
                        <li <?php if($tab == 'orders') { ?>class="active"<?php } ?>><a href="<?=Url::toRoute(['/order/order/index', 'tab' => 'orders']);?>"><?=yii::t('order', 'Orders');?></a></li>
                        <li <?php if($tab == 'assigments') { ?>class="active"<?php } ?>><a href="<?=Url::toRoute(['/order/order/index', 'tab' => 'assigments']);?>"><?=yii::t('order', 'Assigments');?></a></li>
                    </ul>
                </div>
            </div>
            
            <div class="summary row">
                <div class="col-md-4">
                    <h3>
                        <?=number_format($dataProvider->query->sum('cost'), 2, ',', '.');?>
                        <?=$module->currency;?>
                    </h3>
                </div>
                <div class="col-md-4">
                    <?php
                    if(Yii::$app->getModule('order')->showPaymentColumn){
                        ?>
                        <h3>
                            <?php
                            echo yii::t('order', 'Paid') . ": ";
                            $query = clone $dataProvider->query;
                            echo number_format($query->where('payment <> \'no\'')->sum('cost'), 2, ',', '.') . $module->currency;
                            ?>
                        </h3>
                    <?php
                    }
                    ?>
                </div>
                <div class="col-md-4 export">
                    <?php
                    $gridColumns = $columns;
                    echo ExportMenu::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => $gridColumns
                    ]);
                    ?>
                </div>
            </div>
            
            <div class="order-list">
                <?=  \kartik\grid\GridView::widget([
                    'export' => false,
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => $columns,
                ]); ?>
            </div>
        </div>
    </div>
</div>
