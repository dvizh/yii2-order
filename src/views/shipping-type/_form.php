<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

use dvizh\order\models\OrderFieldType;
use yii\helpers\ArrayHelper;
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'name')->textInput() ?>
    
        <?= $form->field($model, 'order')->textInput() ?>

        <div class="row">
            <div class="col-md-6"><?= $form->field($model, 'cost')->textInput() ?></div>
            <div class="col-md-6"><?= $form->field($model, 'free_cost_from')->textInput() ?></div>
        </div>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('order', 'Create') : Yii::t('order', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>
