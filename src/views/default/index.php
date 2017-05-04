<?php
use yii\helpers\Url;

$this->title = yii::t('order', 'Orders');

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="model-index">
    <table class="table">
        <tr>
            <th><?=yii::t('order', 'Orders');?></th>
            <td>
                <a href="<?=Url::toRoute(['/order/order/index']);?>" class="btn btn-default"><i class="glyphicon glyphicon-eye-open" /></i></a>
                <a href="<?=Url::toRoute(['/order/operator/index']);?>" class="btn btn-default"><i class="glyphicon glyphicon-eye-open" /></i></a>
                <a href="<?=Url::toRoute(['/order/order/create']);?>" class="btn btn-default"><i class="glyphicon glyphicon-plus" /></i></a>
            </td>
        </tr>
        <tr>
            <th><?=yii::t('order', 'Fields');?></th>
            <td>
                <a href="<?=Url::toRoute(['/order/field/index']);?>" class="btn btn-default"><i class="glyphicon glyphicon-eye-open" /></i></a>
                <a href="<?=Url::toRoute(['/order/field/create']);?>" class="btn btn-default"><i class="glyphicon glyphicon-plus" /></i></a>
            </td>
        </tr>
        <tr>
            <th><?=yii::t('order', 'Shipping types');?></th>
            <td>
                <a href="<?=Url::toRoute(['/order/shipping-type/index']);?>" class="btn btn-default"><i class="glyphicon glyphicon-eye-open" /></i></a>
                <a href="<?=Url::toRoute(['/order/shipping-type/create']);?>" class="btn btn-default"><i class="glyphicon glyphicon-plus" /></i></a>
            </td>
        </tr>
        <tr>
            <th><?=yii::t('order', 'Payment types');?></th>
            <td>
                <a href="<?=Url::toRoute(['/order/payment-type/index']);?>" class="btn btn-default"><i class="glyphicon glyphicon-eye-open" /></i></a>
                <a href="<?=Url::toRoute(['/order/payment-type/create']);?>" class="btn btn-default"><i class="glyphicon glyphicon-plus" /></i></a>
            </td>
        </tr>
        <tr>
            <th><?=yii::t('order', 'Order statistics');?></th>
            <td>
                <a href="<?=Url::toRoute(['/order/stat/index']);?>" class="btn btn-default"><i class="glyphicon glyphicon-eye-open" /></i></a>
            </td>
        </tr>
    </table>
</div>
