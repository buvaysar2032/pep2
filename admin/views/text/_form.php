<?php

use yii\bootstrap5\{ActiveForm, Html};

/**
 * @var $this  yii\web\View
 * @var $model common\models\Text
 * @var $form  yii\widgets\ActiveForm
 */
?>

<div class="text-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'key')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'value')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
