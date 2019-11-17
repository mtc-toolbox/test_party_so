<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;

echo Html::beginTag('div', ['class' => 'site-error']);

    echo Html::tag('h1', Html::encode($this->title));

    echo Html::beginTag('div', ['class' => 'alert alert-danger']);
        echo nl2br(Html::encode($message));
    echo Html::endTag('div');

    echo Html::tag('p', Yii::t('app', 'The above error occurred while the Web server was processing your request.'));
    echo Html::tag('p', Yii::t('app', 'Please contact us if you think this is a server error. Thank you.'));

echo Html::endTag('div');
