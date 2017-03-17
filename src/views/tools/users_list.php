<?php
use yii\helpers\Html;
use yii\grid\GridView;

$columns = [
    ['attribute' => 'id', 'options' => ['style' => 'width: 49px;']],
    'name',
    'email',
    'phone',
];

if($fields = yii::$app->getModule('order')->userModelCustomFields) {
    $columns = array_merge($columns, $fields);
}

$columns[] = [
    'label' => yii::t('order', 'Choose'),
    'content' => function($user) {
        return Html::a(yii::t('order', 'Choose'), "#", ['class' => 'btn btn-success', 'onclick' => 'window.parent.dvizh.createorder.chooseUser('.$user->id.')']);
    }
];

?>
<div class="user-list">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $columns,
    ]); ?>

</div>
