<?php

use yii\helpers\Html;

$this->title = Yii::t('order', 'Create shipping type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Shipping types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('order', 'Create');
?>
<div class="field-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
