<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */

echo Html::beginTag('div', ['class' => 'apple-create']);
    echo $this->render('_form', ['model' => $model]);
echo Html::endTag('div');
