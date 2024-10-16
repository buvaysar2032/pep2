<?php

use yii\bootstrap5\Html;

/**
 * @var $this  yii\web\View
 * @var $model common\models\Text
 */

$this->title = Yii::t('app', 'Create Text');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Texts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="text-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
