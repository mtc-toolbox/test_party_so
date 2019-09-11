<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */
/* @var $index int */

$downloadState = $model->canFall() ? 'enabled-tool-button' : 'disabled-tool-button';

$eatState =  $model->canEat() ? 'enabled-tool-button' : 'disabled-tool-button';

?>

<div class = "apple-cell col-lg-2 col-md-3 col-sm-6">
  <div class="apple-container shadow">
	  <div class="apple-download">
  		<div class="fa fa-download <?=$downloadState?>"></div>
	  </div>
      <div class="apple-eat">
  		<div class="fab fa-apple <?=$eatState?>"></div>
	  </div>
	  <div class="apple-image <?=$model->getColorName() ?>">
  		<div class="fa fa-apple-alt"></div>
	  </div>
      <?php
        if (!$model->isBad())
        {
            echo Html::tag('div',$model->IntegrityPercent.'%',['class' => 'apple-eated']);
        }
      ?>
      <div class="apple-state">
            <?=$model->getStateName() ?>
      </div>
  </div>
</div>
