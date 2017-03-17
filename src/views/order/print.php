<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = yii::t('order', 'Order').' №'.$model->id;
?>

<style>
body, td, th {
    padding: 1px;
    margin: 1px;
    font-family: 'Lucida Console';
}
</style>
<div class="order-print-view">
    <h3 align="center"><?=yii::$app->name;?></h3>
    <?php
    $detailOrder = [
        'model' => $model,
        'attributes' => [
            'id',
			[
				'attribute' => 'date',
				'value'		=> date(yii::$app->getModule('order')->dateFormat, $model->timestamp),
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
            'value'		=> @$paymentTypes[$model->payment_type_id],
        ];
    }
    
    if($model->shipping_type_id && isset($shippingTypes[$model->shipping_type_id])) {
			$detailOrder['attributes'][] = [
				'attribute' => 'shipping_type_id',
				'value'		=> $shippingTypes[$model->shipping_type_id],
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
				'value'		=> Html::encode($fieldModel->getValue($model->id)),
			];
        }
    }
    
    if($model->seller) {
        $detailOrder['attributes'][] = [
            'label' => yii::t('order', 'Seller'),
            'value'		=> Html::encode($model->seller->name),
        ];
    }

    echo DetailView::widget($detailOrder);
    ?>

	<h3><?=Yii::t('order', 'Order list'); ?></h3>

    <table class="table table-striped table-bordered detail-view">
        <?php foreach($model->elements as $element) { ?>
            <tr>
                <td><?=$element->getModel()->name;?></td>
                <td><?=$element->price;?>x<?=$element->count;?></td>
                <td><?=round($element->price*$element->count, 2);?> <?=$module->currency;?></td>
            </tr>
        <?php } ?> 
    </table>
    
    <h3 align="right">
        <?=Yii::t('order', 'In total'); ?>:
        <?=$model->count;?> <?=Yii::t('order', 'on'); ?>
        <?=$model->cost;?>
        <?=Yii::$app->getModule('order')->currency;?>
        <?php if($model->promocode) { ?>
            (<?=yii::t('order', 'Discount');?> <?=$model->promocode;?><?php if(yii::$app->has('promocode') && $code = yii::$app->promocode->checkExists($model->promocode)) { echo " {$code->discount}%"; } ?>)
        <?php } ?>
    </h3>
</div>

<script>
window.print();
</script>