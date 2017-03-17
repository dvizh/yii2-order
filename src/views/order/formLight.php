<div class="custom-order-form-light-container" style="max-width: 320px;">
    <?php if (Yii::$app->service->splitOrderPerfome) {
            if (isset(yii::$app->worksess->soon()->users)) {
                $staffers = yii::$app->worksess->soon()->getUsers();
                $staffers = $staffers->all();
            } else {
                $staffers = null;
            }
        } else {
            $staffers = null;
        }
    ?>

    <?= \dvizh\order\widgets\OrderFormLight::widget([
        'useAjax' => $useAjax,
        'staffer' => $staffers
    ]); ?>
</div>
