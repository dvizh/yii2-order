<?php

use yii\helpers\Html;

$this->title = Yii::t('order', 'Create shipping type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/order/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Shipping types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('order', 'Create');
?>
<div class="field-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
