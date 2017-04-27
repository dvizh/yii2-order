<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model dvizh\order\models\Payment */

$this->title = 'Create Payment';
$this->params['breadcrumbs'][] = ['label' => 'Payments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
