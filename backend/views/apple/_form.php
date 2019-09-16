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

?>

<div class="apple-form">

    <?php $form = ActiveForm::begin(
        ['id' => 'eat-form']
    ); ?>

    <input type="hidden" name="eat-id" value="" id="eat-id"/>

    <?= $form->field($model, 'IntegrityPercent')->textInput(['maxlength' => true, 'id' => 'eat-percent']) ?>

    <div class="form-group">
        <?= Html::tag("span", Yii::t('app', 'Съесть'), ['class' => 'btn btn-success', 'id' => 'eat-button']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

Modal::end();
