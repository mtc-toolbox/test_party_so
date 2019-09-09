<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="apple-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Color')->textInput() ?>

    <?= $form->field($model, 'IntegrityPercent')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'CreatedAt')->textInput() ?>

    <?= $form->field($model, 'FalledAt')->textInput() ?>

    <?= $form->field($model, 'DeletedAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
