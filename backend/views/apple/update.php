<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */

$this->title = Yii::t('app', 'Update Apple: {name}', [
    'name' => $model->Id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Apples'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->Id, 'url' => ['view', 'id' => $model->Id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="apple-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
