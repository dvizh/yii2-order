<?php
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\DetailView;
?>
<h1><?=Yii::t('order', 'New order'); ?> #<?=$model->id;?></h1>

<!--p><?=Html::a(yii::t('order', 'View'), Url::to(['/order/order/view', 'id' => $model->id], true));?></p-->

<ul>
    <?php if($model->client_name) { ?>
        <li><?=$model->client_name;?></li>
    <?php } ?>

    <?php if($model->phone) { ?>
        <li><?=$model->phone;?></li>
    <?php } ?>
    
     <?php if($model->email) { ?>
        <li><?=Html::a($model->email, 'mailto:'.$model->email);?></li>
    <?php } ?>
 
    <?php if($model->comment) { ?>
        <li><?=$model->comment;?></li>
    <?php } ?>

    <li><?=$model->date;?> <?=$model->time;?></li>

    <?php if($model->paymentType) { ?>
        <li><?=$model->paymentType->name;?></li>
    <?php } ?>

    <?php if($model->shipping) { ?>
        <li><?=$model->shipping->name;?></li>
    <?php } ?>

    <?php if($model->delivery_type == 'totime') { ?>
        <?=Yii::t('order', 'Delivery to time'); ?>
        <?=$model->delivery_time_date;?> <?=$model->delivery_time_hour;?>:<?=$model->delivery_time_min;?>
    <?php } ?>
        
    <?php
    if($fields = $model->fields) {
        foreach($fields as $fieldModel) {
            echo "<li>{$fieldModel->field->name}: {$fieldModel->value}</li>";
        }
    }
    ?>
</ul>

<h2><?=Yii::t('order', 'Order list'); ?></h2>

<?php if($model->elements) { ?>
    <table width="100%">
        <?php foreach($model->elements as $element) { ?>
            <tr>
                <td>
                    <?=$element->product->getCartName(); ?>
                    <?php if($element->description) { echo "({$element->description})"; } ?>
                    <?php
                    if($options = json_decode($element->options)) {
                        foreach($options as $name => $value) {
                            $return .= Html::tag('p', Html::encode($name).': '.Html::encode($value));
                        }
                    }
                    ?>
                </td>
                <td>
                    <?=$element->count;?>
                </td>
                <td>
                    <?=$element->price;?><?=Yii::$app->getModule('order')->currency;?>
                </td>
            </tr>
        <?php } ?>
    </table>
<?php } ?>

<h3 align="right">
    <?=Yii::t('order', 'In total'); ?>:
    <?=$model->count;?>,
    <?=$model->total;?>
    <?=Yii::$app->getModule('order')->currency;?>
</h3>
