<?php
use yii\helpers\Html;
use yii\grid\GridView;
use dvizh\cart\widgets\BuyButton;
use dvizh\cart\widgets\ChangeCount;
use dvizh\cart\widgets\ChangeOptions;
$this->registerJs("$(document).on('renderCart', function() {
        window.parent.dvizh.createorder.updateCart();
    });");
?>

<style>
.to-order {
    padding: 2px;
    margin-top: 2px;
}
.dvizh-order-prodlist-widgets {
    font-size: 12px;
}

.dvizh-change-count, .dvizh-cart-buy-button {
    display: inline;
    width: 45%;
}

.products-list select {
    padding: 1px;
    margin: 1px;
    font-size: 12px;
    height: auto;
}
</style>
<div class="products-list">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['attribute' => 'id', 'options' => ['style' => 'width: 45px;']],
            [
                'attribute' => 'name',
                'content' => function($product) {
                    $name = Html::tag('div', $product->name, ['style' => 'clear: both;']);

                    $count = ChangeCount::widget(['model' => $product->sellModel]);
                    
                    if($product->getCartOptions()) {
                        $options = ChangeOptions::widget(['model' => $product]);
                    } else {
                        $options = null;
                    }
                    
                    $btn = BuyButton::widget([
                        'model' => $product->sellModel,
                        'text' => yii::t('order', 'To order'),
                        'htmlTag' => 'a',
                        'cssClass' => 'btn btn-success to-order'
                    ]);
                    
                    return Html::tag('div', $name.$options.$count.$btn, ['class' => 'dvizh-order-prodlist-widgets']);
                }
            ],
            'code',
			[
				'attribute' => 'category_id',
				'filter' => Html::activeDropDownList(
					$searchModel,
					'category_id',
					$categories,
					['class' => 'form-control', 'prompt' => 'Категория']
				),
				'value' => 'category.name'
			],
			[
				'label' => yii::t('order', 'Amount'),
				'value' => 'amount'
			],
			[
				'label' => yii::t('order', 'Price'),
				'value' => 'price',
			],
        ],
    ]); ?>
    
</div>
