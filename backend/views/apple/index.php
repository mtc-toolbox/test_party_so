<?php

use common\models\Apple;
use yii\helpers\Url;
use yii\bootstrap\Html;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\AppleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Яблоки');

echo Html::beginTag('h2');
    echo Html::encode($this->title);
echo Html::endTag('h2');

echo Html::beginTag('div', ['class' => 'apple-list']);

    echo ListView::widget([
        'layout'       => "{items}",
        'dataProvider' => $dataProvider,
        'itemView' => 'item',
        'options' => [
            'tag' => false,
        ],
        'itemOptions'  => [
            'tag' => false,
        ],
        'emptyText' => '<div class="table-row row">Яблоки отсутствуют</div>',

    ]);

echo Html::endTag('div');

echo $this->render('create', [
    'model'   => new Apple(),
]);
