<?php
use yii\helpers\Url;
?>
<div class="assigment-to-order">
    <form action="<?=Url::toRoute(['/order/order/to-order']);?>" method="post" class="assigment-to-order-form">
        <input type="hidden" name="id" value="<?=$model->id;?>" />
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <?php if ($staffers) { ?>
            <?php foreach ($staffers as $key => $worker) { ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" value="<?= $worker->id ?>" name="staffers[]" /><?= $worker->name ?> <?php if($worker->category) { ?>(<?= $worker->category->name ?>)<?php } ?>
                    </label>
                </div>
            <?php } ?>
        <?php } ?>
        
        <input class="btn btn-success" type="submit" value="<?=yii::t('order', 'To order');?>" /> 
    </form>
</div>