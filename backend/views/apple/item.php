<?php

use yii\bootstrap\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Apple */
/* @var $index int */

$timeToBad = $model->getTimeToBad();

$downloadState = $model->canFall() ? 'enabled-tool-button' : 'disabled-tool-button';

$eatState =  $model->canEat() ? 'enabled-tool-button' : 'disabled-tool-button';

?>

<div class = "apple-cell col-lg-2 col-md-3 col-sm-6">
  <div class="apple-container shadow" data-key="<?= $model->Id?>" time-to-bad="<?=$timeToBad?>">
	  <div class="apple-download <?=$downloadState?>" data-key="<?= $model->Id?>" >
  		<div class="fa fa-download <?=$downloadState?>"></div>
	  </div>
      <div class="apple-eat <?=$eatState?>" data-key="<?= $model->Id?>">
  		<div class="fab fa-apple <?=$eatState?>" data-key="<?= $model->Id?>"></div>
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
      <div class="apple-state" >
            <?=$model->getStateName() ?>
      </div>
  </div>
</div>
