<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<?php if(Yii::$app->session->hasFlash('orderError')) { ?>
    <?php
    $errors = Yii::$app->session->getFlash('orderError');
    $orderModel->addErrors(unserialize($errors));
    ?>
<?php } ?>
<div class="dvizh_order_form">
    <?php $form = ActiveForm::begin(['action' => Url::toRoute(['/order/order/create'])]); ?>
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />

        <div class="row">
            <div class="col-lg-6"><?= $form->field($orderModel, 'client_name')->textInput() ?></div>
            <div class="col-lg-3"><?= $form->field($orderModel, 'phone')->textInput() ?></div>
            <div class="col-lg-3"><?= $form->field($orderModel, 'email')->textInput() ?></div>
        </div>

        <div class="row">
            <?php if($shippingTypes) { ?>
                <div class="col-md-6 order-widget-shipping-type">
                    <?= $form->field($orderModel, 'shipping_type_id')->dropDownList($shippingTypes) ?>
                    <script>
                        shippintTypeCost = [];
                        <?php foreach($shippingTypesList as $sht) { ?>
                            shippintTypeCost[<?=$sht->id;?>] = <?=(int)$sht->cost;?>;
                        <?php } ?>
                    </script>
                </div>
            <?php } ?>

            <?php if($paymentTypes) { ?>
                <div class="col-md-6">
                    <?= $form->field($orderModel, 'payment_type_id')->dropDownList($paymentTypes) ?>
                </div>
            <?php } ?>
        </div>
    
        <?php if($fields = $fieldFind->all()) { ?>
            <div class="row order-custom-fields">
                <?php foreach($fields as $fieldModel) { ?>
                    <div class="col-lg-12 col-xs-12 order-custom-field-<?=$fieldModel->id;?>">
                        <?php
                        if($widget = $fieldModel->type->widget) {
                            echo $widget::widget(['form' => $form, 'fieldModel' => $fieldModel]);
                        }
                        else {
                            echo $form->field($fieldValueModel, 'value['.$fieldModel->id.']')->label($fieldModel->name)->textInput(['required' => ($fieldModel->required == 'yes')]);
                        }
                        ?>
                        <?php if($fieldModel->description) { ?>
                            <p><small><?=$fieldModel->description;?></small></p>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>

        <div class="row">
            <div class="col-lg-12"><?= $form->field($orderModel, 'comment')->textArea() ?></div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <?= Html::submitButton(Yii::t('order', 'Create order'), ['class' => 'btn btn-success']) ?>
                <?php if($referrer = Yii::$app->request->referrer) { ?>
                    <?= Html::a(Yii::t('order', 'Continue shopping'), Html::encode($referrer), ['class' => 'btn btn-default']) ?>
                <?php } ?>
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>