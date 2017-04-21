<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<?php if($mode == 'popup') { ?>
<div class="modal fade" id="fastOrder<?=$model->id;?>" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><h4 class="modal-title"><?=Yii::t('order', 'Create order');?></h4>
            </div>
            <div class="modal-body">
                
<?php } ?>
                <div class="dvizh_order_oneclick_form">
                    <?php $form = ActiveForm::begin(['action' => Url::toRoute(['/order/tools/one-click'])]); ?>
                        <input type="hidden" name="id" value="<?=$model->id;?>" />
                        <input type="hidden" name="model" value="<?=$model::className();?>" />
                        
                        <div class="row">
                            <div class="col-md-6"><?= $form->field($orderModel, 'client_name')->label(yii::t('order', 'How call you?'))->textInput(['required' => true]) ?></div>
                            <div class="col-md-6"><?= $form->field($orderModel, 'phone')->textInput(['required' => true]) ?></div>
                        </div>
                        
                        <?php if($showComment) { ?>
                            <div class="row">
                                <div class="col-md-12"><?= $form->field($orderModel, 'comment')->textArea() ?></div>
                            </div>
                        <?php } ?>

                        <div class="row">
                            <div class="col-md-12">
                                <?= Html::submitButton(Yii::t('order', 'Create order'), ['class' => 'btn btn-success']) ?>
                            </div>
                        </div>
                    <?php ActiveForm::end(); ?>
                </div>
<?php if($mode == 'popup') { ?> 
            </div>
        </div>
    </div>
</div>
<?php } ?>