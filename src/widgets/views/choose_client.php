<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<div class="choose-client row">
    <div class="col-md-12">
        <label class="control-label" for="order-user_id"><?=Yii::t('order', 'Buyer');?></label>

		<div class="row">
			<div class="col-md-4"><?= Html::input('text', 'Order[user_id]', '', ['id' => 'order-user_id', 'class' => 'form-control', 'data-info-service' => Url::toRoute(['/order/tools/user-info'])]) ?></div>
			<div class="col-md-8"><p><?=Html::a('<i class="glyphicon glyphicon-search"></i> Найти покупателя', '#usersModal', ['id' => 'choose-user-id', 'data-toggle' => "modal", 'data-target' => "#usersModal"]);?></p></div>
		</div>

        <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-md-12">
        <?= $form->field($model, 'client_name')->textInput(['maxlength' => true]) ?>
    </div>
</div>
