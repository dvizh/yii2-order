<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\export\ExportMenu;
use nex\datepicker\DatePicker;

if($dateStart = yii::$app->request->get('date_start')) {
    $dateStart = date('Y-m-d', strtotime($dateStart));
}

if($dateStop = yii::$app->request->get('date_stop')) {
    $dateStop = date('Y-m-d', strtotime($dateStop));
}

$columns = [
    ['attribute' => 'id', 'filter' => false, 'options' => ['style' => 'width: 49px;']],
    [
        'label' => yii::t('order', 'Order'),
        'content' => function($model) {
            foreach($model->elements as $element) {
                if($productModel = $element->product) {
                    return $productModel->getCartId().'. '.$productModel->getCartName();
                } else {
                    return Yii::t('order', 'Unknow product');
                }
            }
        },
    ],
    [
        'attribute' => 'cost',
        'label' => yii::$app->getModule('order')->currency,
        'content' => function($model) {
            $total = $model->cost;
            if($model->promocode) {
                $total .= Html::tag('div', $model->promocode, ['style' => 'color: orange; font-size: 80%;', yii::t('order', 'Promocode')]);
            }

            return $total;
        },
    ],
    ['attribute' => 'count', 'filter' => false],
    'date',
    [
        'attribute' => 'status',
        'filter' => Html::activeDropDownList(
            $searchModel,
            'status',
            yii::$app->getModule('order')->orderStatuses,
            ['class' => 'form-control', 'prompt' => Yii::t('order', 'Status')]
        ),
        'value'	=> function($model) {
            return  Yii::$app->getModule('order')->orderStatuses[$model->status];
        }
    ],
    ['content' => function($model) {
            return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['/order/order/view', 'id' => $model->id], ['class' => 'btn btn-default']);
    }, 'options' => ['style' => 'width: 50px;']]
];
?>
<div class="worker-payments-widget">
    <?php Pjax::begin(); ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title"><?=yii::t('order', 'Search');?></h3>
        </div>
        <div class="panel-body">
            <form action="<?=Url::toRoute(['/client/client/view', 'id' => $clientId]);?>" class="row search">
                <input type="hidden" name="id" value="<?=$clientId;?>" />
                <div class="col-md-4">
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
                    </div>
                </div>

                <div class="col-md-2">
                    <select class="form-control" name="OrderSearch[status]">
                        <option value=""><?=yii::t('order', 'Status');?></option>
                        <?php foreach(yii::$app->getModule('order')->orderStatuses as $status => $statusName) { ?>
                            <option <?php if($status == yii::$app->request->get('OrderSearch')['status']) echo ' selected="selected"';?> value="<?=$status;?>"><?=$statusName;?></option>
                        <?php } ?>
                    </select>
                </div>

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
                </div>

                <div class="col-md-2">
                    <input class="form-control btn btn-success" type="submit" value="<?=Yii::t('order', 'Search');?>" />
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=yii::t('order', 'Total');?>:
            <?=number_format($dataProvider->query->sum('cost'), 2, ',', '.');?>
            <?=yii::$app->getModule('order')->currency;?>
        </div>
        <div class="col-md-6 export">
            <a href="#" class="btn btn-submit" onclick="CallPrint('orders-to-print'); return false;"><i class="glyphicon glyphicon-print"></i></a>
        </div>
    </div>
    <div id="orders-to-print">
        <?php echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => $columns]); ?>
        <?php Pjax::end(); ?>
    </div>
</div>

<style>
    .export {
        text-align: right;
        padding-right: 110px;
    }
</style>

<script language="javascript">
function CallPrint(strid) {
    var prtContent = document.getElementById(strid);
    var WinPrint = window.open('','','left=50,top=50,width=800,height=640,toolbar=0,scrollbars=1,status=0');
    WinPrint.document.write('<div id="print" class="contentpane">');
    WinPrint.document.write('<h2><?=$client->name;?></h2>');
    WinPrint.document.write('<h3><?=$dateStart;?> - <?=$dateStop;?></h3>');
    WinPrint.document.write(prtContent.innerHTML);
    WinPrint.document.write('</div>');
    WinPrint.document.close();
    WinPrint.focus();
    WinPrint.print();
    WinPrint.close();
}
</script>