<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Вход в систему';
$this->params['breadcrumbs'][] = $this->title;
echo Html::beginTag('div', ['class' => 'site-login']);
    echo Html::tag('h1', Html::encode($this->title));
    echo Html::tag('p', 'Пожалуйста, введите пароль:');
    echo Html::beginTag('div', ['class' => 'row']);
        echo Html::beginTag('div', ['class' => 'col-lg-5']);
            $form = ActiveForm::begin(['id' => 'login-form']);
            echo $form->field($model, 'username')->hiddenInput(['value' => 'admin'])->label(false);
            echo $form->field($model, 'password')->passwordInput();
            echo $form->field($model, 'rememberMe')->checkbox();
            echo Html::beginTag('div', ['class' => 'form-group']);
                echo Html::submitButton('Вход', ['class' => 'btn btn-primary', 'name' => 'login-button']);
            echo Html::endTag('div');
            ActiveForm::end();
        echo Html::endTag('div');
    echo Html::endTag('div');
echo Html::endTag('div');
