<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\PostCategory;
use common\models\Post;
use vova07\imperavi\Widget;

/** @var yii\web\View $this */
/** @var common\models\Post $model */
/** @var yii\widgets\ActiveForm $form */
if (!empty($model->errors)) {
    echo print_r($model->errors, true);
}
?>


<div class="post-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'user_id')->textInput(['maxlength' => true]) ?>
    <br>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <br>
        <?php
        echo $form->field($model, 'text')->widget(Widget::className(), [
            'settings' => [
                'lang' => 'ru',
                'minHeight' => 100,
                'plugins' => [
                    'clips',
                    'fullscreen',
                ],
                'clips' => [
                    ['Lorem ipsum...', 'Lorem...'],
                    ['red', '<span class="label-red">red</span>'],
                    ['green', '<span class="label-green">green</span>'],
                    ['blue', '<span class="label-blue">blue</span>'],
                ],
            ],
        ]);
        ?>
    <br>

    <?= $form->field($model, 'post_category_id')->dropDownList(PostCategory::getList(), ['prompt' => 'Выберите категорию']) ?>
    <br>

    <?= $form->field($model, 'status')->dropDownList(Post::getList(), ['prompt' => 'Выберите статус']) ?>
    <br>

    <?= $form->field($model, 'imageFile')->fileInput() ?>
    <br>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
