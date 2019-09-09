<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */

$this->title = Yii::t('app', 'Create Apple');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Apples'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="apple-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
