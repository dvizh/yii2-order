<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\grid\GridView;
use dvizh\order\models\FieldValueVariant;
use dvizh\order\models\tools\FieldValueVariantSearch;
use dvizh\order\models\FieldType;
use yii\helpers\ArrayHelper;
?>

<div class="field-form">

    <?php $form = ActiveForm::begin(); ?>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'name')->textInput() ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'type_id')->dropDownList(ArrayHelper::map(FieldType::find()->all(), 'id', 'name')) ?>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'required')->radioList(['no' => Yii::t('order', 'no'), 'yes' => Yii::t('order', 'yes')]); ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'order')->textInput(['type' => 'number']) ?>
            </div>
        </div>
        
    
        
        <?= $form->field($model, 'description')->textArea(['maxlength' => true]) ?>
    
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('order', 'Create') : Yii::t('order', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

    
    <?php if(!$model->isNewRecord && $model->type->have_variants == 'yes') { ?>
    
        <?php
        $variantModel = new FieldValueVariant();
        
        $searchModel = new FieldValueVariantSearch();
        
        $params = Yii::$app->request->queryParams;
        if(empty($params['FieldValueVariantSearch'])) {
            $params = ['FieldValueVariantSearch' => ['field_id' => $model->id]];
        }

        $dataProvider = $searchModel->search($params); 
        ?>
        <div class="dvizh-variants">
            <h3><?=Yii::t('order', 'Variants');?></h3>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'class' => \dosamigos\grid\columns\EditableColumn::className(),
                        'attribute' => 'value',
                        'url' => ['/order/field-value-variant/editable'],
                        'filter' => false,
                        'editableOptions' => [
                            'mode' => 'inline',
                        ]
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'controller' => '/order/field-value-variant', 'template' => '{delete}',  'buttonOptions' => ['class' => 'btn btn-default'], 'options' => ['style' => 'width: 75px;']],
                ],
            ]); ?>
            
            <h3><?=Yii::t('order', 'New variant');?></h3>
            <?php $form = ActiveForm::begin(['action' =>['/order/field-value-variant/create'], 'id' => 'forum_post', 'method' => 'post',]); ?>
                <?= $form->field($variantModel, 'field_id')->hiddenInput(['value' => $model->id])->label(false) ?>
            
                <?= $form->field($variantModel, 'value')->textInput() ?>

                <div class="form-group">
                    <?= Html::submitButton(Yii::t('order', 'Create'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    <?php } ?>
    
</div>
