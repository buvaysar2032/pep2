<?php

use yii\bootstrap5\{Html, ActiveForm};

/**
 * @var $this  yii\web\View
 * @var $model common\models\Setting
 * @var $form  yii\widgets\ActiveForm
 */
?>

<div class="email-settings-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'parameter')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
