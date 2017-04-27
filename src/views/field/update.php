<?php

use yii\helpers\Html;

$this->title = Yii::t('order', 'Update field');
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Orders'), 'url' => ['/order/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('order', 'Fields'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('order', 'Update');
?>
<div class="field-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
