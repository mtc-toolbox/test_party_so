<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */
/* @var $index int */

$timeToBad = $model->getTimeToBad();

$downloadState = $model->canFall() ? 'enabled-tool-button' : 'disabled-tool-button';

$eatState = $model->canEat() ? 'enabled-tool-button' : 'disabled-tool-button';

$appleState = $model->getState();

echo Html::beginTag('div', ['class' => 'apple-cell col-2']);

    echo Html::beginTag('div', [
        'class'       => 'apple-container shadow',
        'data-key'    => $model->Id,
        'time-to-bad' => $timeToBad,
        'state'       => $appleState,
    ]);

        echo Html::beginTag('div', [
            'class'    => 'apple-download '.$downloadState,
            'data-key' => $model->Id,
        ]);
            echo Html::tag('div', '', ['class' => 'fa fa-download']);
        echo Html::endTag('div');

        echo Html::beginTag('div', [
            'class'    => 'apple-eat ' . $eatState,
            'data-key' => $model->Id,
        ]);

            echo Html::tag('div', '', [
                'class'    => 'fab fa-apple',
                'data-key' => $model->Id,
            ]);

        echo Html::endTag('div');

        echo Html::beginTag('div', [
            'class'    => 'apple-image ' . $model->getColorName(),
            'data-key' => $model->Id,
        ]);

            echo Html::tag('div', '', [
                'class' => 'fa fa-apple-alt',
            ]);

        echo Html::endTag('div');

        if (!$model->isBad()) {
            echo Html::tag('div', $model->IntegrityPercent . '%', ['class' => 'apple-eated']);
        }

        echo Html::tag('div', $model->getStateName(), [
            'class' => 'apple-state',
        ]);

    echo Html::endTag('div');
echo Html::endTag('div');
