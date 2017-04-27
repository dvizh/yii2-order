<?php

use yii\helpers\Html;

$this->title = Yii::t('order', 'Create payment type');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Payment types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="field-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
