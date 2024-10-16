<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\PostCategory $model */

$this->title = Yii::t('app', 'Create Post Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Post Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
