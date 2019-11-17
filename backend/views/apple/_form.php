<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */
/* @var $form yii\widgets\ActiveForm */

Modal::begin([
    'header' => 'Поедание',
    'id' => 'currency-modal',
],
    [
        'htmlOptions' => [
            'style' => 'width: 100%; margin-left: -50%',
        ],
    ]);

    echo Html::beginTag('div', ['class' => 'apple-form']);

    $form = ActiveForm::begin(['id' => 'eat-form']);

        echo Html::hiddenInput('eat-id', '', ['id' => 'eat-id']);

        echo $form->field($model, 'IntegrityPercent')->textInput(['maxlength' => true, 'id' => 'eat-percent']);

        echo Html::beginTag('div', ['class' => 'form-group']);

            echo Html::tag("span", Yii::t('app', 'Съесть'), ['class' => 'btn btn-success', 'id' => 'eat-button']);

        echo Html::endTag('div');

    ActiveForm::end();

    echo Html::endTag('div');

Modal::end();
