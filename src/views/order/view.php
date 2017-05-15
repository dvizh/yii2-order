<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use yii\widgets\DetailView;
use dvizh\order\assets\Asset;

$this->title = Yii::t('order', 'Order').' #'.$model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

Asset::register($this);
?>
<div class="order-view">
    <?php if(Yii::$app->session->hasFlash('reSendDone')) { ?>
        <script>
        alert('<?= Yii::$app->session->getFlash('reSendDone') ?>');
        </script>
    <?php } ?>

    <p>
        <?= Html::a(Yii::t('order', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('order', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('order', 'Realy?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="row">
        <div class="col-md-6">
            <?=dvizh\order\widgets\ChangeStatus::widget(['model' => $model]);?>
        </div>
        <div class="col-md-6">

        </div>

    </div>


    <hr />

    <?php
    $detailOrder = [
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'date',
                'value'        => date(yii::$app->getModule('order')->dateFormat, $model->timestamp),
            ],
        ],
    ];
    
    if($model->client_name) {
        $detailOrder['attributes'][] = 'client_name';
    }
    
    if($model->phone) {
        $detailOrder['attributes'][] = 'phone';
    }

    if($model->email) {
        $detailOrder['attributes'][] = 'email:email';
    }
    
    if($model->promocode) {
        $detailOrder['attributes'][] = 'promocode';
    }

    if($model->comment) {
        $detailOrder['attributes'][] = 'comment';
    }
    
    if($model->payment_type_id && isset($paymentTypes[$model->payment_type_id])) {
        $detailOrder['attributes'][] = [
            'attribute' => 'payment_type_id',
            'value'        => @$paymentTypes[$model->payment_type_id],
        ];
    }
    
    if($model->shipping_type_id && isset($shippingTypes[$model->shipping_type_id])) {
		$detailOrder['attributes'][] = [
			'attribute' => 'shipping_type_id',
			'value'        => $shippingTypes[$model->shipping_type_id],
		];
    }

    if($model->delivery_type == 'totime') {
        $detailOrder['attributes'][] = 'delivery_time_date';
        $detailOrder['attributes'][] = 'delivery_time_hour';
        $detailOrder['attributes'][] = 'delivery_time_min';
    }

    if($fields = $fieldFind->all()) {
        foreach($fields as $fieldModel) {
            $detailOrder['attributes'][] = [
                'label' => $fieldModel->name,
                'value'        => Html::encode($fieldModel->getValue($model->id)),
            ];
        }
    }

    if($model->seller) {
        $detailOrder['attributes'][] = [
            'label' => yii::t('order', 'Seller'),
            'value' => Html::encode($model->seller->name),
        ];
    }

    echo DetailView::widget($detailOrder);
    ?>

    <h2><?=Yii::t('order', 'Order list'); ?></h2>

    <?= \kartik\grid\GridView::widget([
        'export' => false,
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'item_id',
                'filter' => false,
                'content' => function($model) {
                    if($productModel = $model->product) {
                        return $productModel->getCartId().'. '.$productModel->getCartName();
                    } else {
                        return Yii::t('order', 'Unknow product');
                    }
                }
            ],
            [
                'attribute' => 'description',
                'filter' => false,
                'content' => function($model) {
                    $elementOptions = json_decode($model->options);
                    $productOptions = $model->getModel()->getCartOptions();

                    $return = $model->description;
                    
                    if($elementOptions) { 
                        foreach($productOptions as $optionId => $optionData) {
                            foreach($elementOptions as $id => $value) {
                                if($optionId == $id) {
                                    if(!$variantValue = $optionData['variants'][$value]) {
                                        $variantValue = 'deleted';
                                    }
                                    $return .= Html::tag('p', Html::encode($optionData['name']).': '.Html::encode($variantValue).';');
                                }
                            }
                            
                        }
                    }
                    
                    return $return;
                }
            ],
            'count',
            'base_price',
            ['attribute' => 'price', 'label' => yii::t('order', 'Price').' %'],
            [
                'label' => yii::t('order', 'Write-off'),
                'content' => function($elementModel) use ($model) {
                    $return = '';
                    
                    $product = $elementModel->getModel();
                    
                    $style = '';
                    $class = '';
                    
                    if(yii::$app->has('stock')) {
                        $stockService = yii::$app->stock;
                        
                        if($stocks = $stockService->getAvailable()) {
                            foreach($stocks as $stock) {
                                $amount = $stock->getAmount($elementModel->item_id);
                                
                                $return .= "<strong>{$stock->name}</strong> (<span class=\"amount\">{$amount}</span>) ";

                                $count = $elementModel->count;
                                
                                if($count > $amount) {
                                    $count = $amount;
                                }
                                
                                if($count < 0) {
                                    $count = 0;
                                }
                                
                                $input = Html::input('text', 'count', $count, ['class' => 'form-control', 'data-order-id' => $model->id, 'data-stock-id' => $stock->id, 'data-product-id' => $product->id]);
                                $button = Html::tag('span', Html::button('<i class="glyphicon glyphicon-ok"></i>', ['class' => 'btn btn-success promo-code-enter-btn']), ['class' => 'input-group-btn']);

                                $return .= Html::tag('div', $input.$button, ['class' => 'input-group', 'style' => 'width: 100px;']);
                            }
                        }
                        
                        if($stockService->getOutcomingsByOrder($model->id, $product->id)) {
                            $style = 'text-decoration: line-through;';
                            $class = 'write-offed';
                        }
                    }
                    
                    return Html::tag('div', $return, ['class' => 'outcomingWidget '.$class, 'style' => $style]);
                }
            ],
            ['class' => 'yii\grid\ActionColumn', 'controller' => '/order/element', 'template' => '{delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 75px;']],
        ],
    ]); ?>
    
    <?php
    if($elementToOrderUrl = yii::$app->getModule('order')->elementToOrderUrl) {
        echo '<div style="text-align: right;"><a href="'.Url::toRoute([$elementToOrderUrl, 'order_id' => $model->id]).'" class="btn btn-success"><i class="glyphicon glyphicon-plus"></i></a></div>';
    }
    ?>

    <h3 align="right">
        <?=Yii::t('order', 'In total'); ?>:
        <?=$model->count;?> <?=Yii::t('order', 'on'); ?>
        <?php if($model->base_cost > $model->cost) { ?>
            <s><?=$model->base_cost;?></s>
        <?php } ?>
        <?=$model->cost;?>
        <?=Yii::$app->getModule('order')->currency;?>
        <?php if($model->promocode) { ?>
            (<?=yii::t('order', 'Discount');?> <?php if(yii::$app->has('promocode') && $code = yii::$app->promocode->checkExists($model->promocode)) { echo " {$code->discount}%"; } ?>)
        <?php } else {
            
        } ?>
    </h3>

</div>
