<?php
use yii\helpers\Url;
use dvizh\order\assets\Asset;
Asset::register($this);

$currency = yii::$app->getModule('order')->currency;

$daysCount = cal_days_in_month(CAL_GREGORIAN, $m, $y);

$days = range(1, $daysCount);

$this->title = $month.' - '.yii::t('order', 'Order statistics per month');

$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Order statistics'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $month;

?>
<div class="order-stat">
    <div class="row">
        <div class="col-lg-2">
            
        </div>
        <div class="col-lg-10">

        </div>
    </div>
    <div class="container">
        <h1><?=$month;?> <?=$y;?></h1>
        <?php
        $prevMonth = strtotime(date("$y-$m-01"))-864000;
        $nextMonth = strtotime(date("$y-$m-27"))+864000;
        ?>
        <p>
            <a href="<?=Url::toRoute(['/order/stat/month/', 'y' => date('Y', $prevMonth), 'm' => date('m', $prevMonth)]);?>">&larr; <?=yii::t('order', 'Previous');?></a>
            |
            <?php if(date('Ymd', $nextMonth) < date('Ymd')) { ?>
                <a href="<?=Url::toRoute(['/order/stat/month/', 'y' => date('Y', $nextMonth), 'm' => date('m', $nextMonth)]);?>"> <?=yii::t('order', 'Next');?> &rarr;</a>
            <?php } ?>
            
        </p>
        <table class="table table-hover table-responsive">
            <tr>
                <th><?=yii::t('order', 'Day');?></th>
                <th><?=yii::t('order', 'Turnover');?></th>
                <th><?=yii::t('order', 'Orders count');?></th>
                <th><?=yii::t('order', 'Average check');?></th>
            </tr>
            <?php $prevStat = false; ?>
            <?php foreach($days as $d) { ?>
                <?php
                if($d <= 9) {
                    $fd = "0$d";
                } else {
                    $fd = $d;
                }
                ?>
                <tr>
                    <td class="month">
                        <a href="<?=Url::toRoute(['/order/order/index', 'date_start' => "$y-$m-$fd"]);?>">
                            <strong><?=$d;?></strong>
                            <?=yii::t('order', 'dayname_'.date("w", strtotime("$y-$m-$fd")));?>
                        </a>
                    </td>
                    <?php
                    $stat = yii::$app->order->getStatByDate("$y-$m-$fd");
                    
                    if($stat['count_orders']) {
                    ?>
                        <td>
                            <?=$stat['total'];?>

                            <?php
                            if($prevStat && date('Ymd') > "$y$m$fd") {
                                $cssClass = '';
                                $sum = '';
                                if($prevStat['total'] < $stat['total']) {
                                    $cssClass = 'good-result';
                                    $sum = '+'.($stat['total']-$prevStat['total']);
                                } elseif($prevStat['total'] > $stat['total']) {
                                    $cssClass = 'bad-result';
                                    $sum = '-'.($prevStat['total']-$stat['total']);
                                }
                                if($sum) {
                                ?>
                                    <span class="result <?=$cssClass;?>"><?=$sum;?></span>
                                <?php
                                }
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            $cssClass = '';
                            if($prevStat && date('Ymd') > "$y$m$fd") {
                                if($prevStat['count_orders'] > $stat['count_orders']) {
                                    $cssClass = 'bad-result';
                                } elseif($prevStat['count_orders'] < $stat['count_orders']) {
                                    $cssClass = 'good-result';
                                }
                            }
                            ?>
                            <span class="<?=$cssClass;?>"><?=$stat['count_orders'];?></span>
                        </td>
                        <td><?=round($stat['total']/$stat['count_orders'], 2);?></td>
                    <?php
                    } else {
                        echo '<td colspan="4" align="center">-</td>';
                    }
                    $prevStat = $stat;
                    ?>
                </tr>
            <?php } ?>
        </table>
    </div>
</div>

<style>
.order-stat {

}

.order-stat .bad-result {
    padding: 2px;
    font-size: 70%;
    background-color: #BB3D3D;
    color: white;
}

.order-stat .good-result {
    padding: 2px;
    font-size: 70%;
    background-color: #96B796;
    color: white;
}
</style>