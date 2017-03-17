<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<?php if(Yii::$app->session->hasFlash('orderError')) { ?>
    <script>
    alert('<?= Yii::$app->session->getFlash('orderError') ?>');
    </script>
<?php } ?>
<div class="dvizh_order_form_button">
    <?php $form = ActiveForm::begin(['action' => Url::toRoute(['/order/order/fast-create'])]); ?> 
        <?= $form->field($model, 'is_assigment')->label(false)->textInput(['type' => 'hidden']) ?>
        <?= Html::submitButton(Yii::t('order', 'Create order'), ['class' => 'btn btn-success']) ?>
    <?php ActiveForm::end(); ?>
</div>