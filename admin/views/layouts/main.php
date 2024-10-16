<?php

/**
 * @var $this    yii\web\View
 * @var $content string
 */

use admin\assets\AppAsset;
use common\widgets\Alert;
use yii\bootstrap5\{Breadcrumbs, Html, Nav, NavBar};
use kartik\icons\{FontAwesomeAsset, Icon};

AppAsset::register($this);
FontAwesomeAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Yii::$app->name ?> | Панель администратора</title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => [
                'navbar',
                'navbar-light',
                'bg-light',
                'blue-grey',
                'tint-color-5',
                'navbar-fixed-top',
                'navbar-expand-lg'
            ]
        ]
    ]);

    $menuItems = [];

    if (!Yii::$app->user->isGuest) {
        $menuItems = [
            ['label' => 'Категории', 'url' => ['/post-category']],
            ['label' => 'Посты', 'url' => ['/post']],
            ['label' => 'Пользователи', 'url' => ['/user']],
            [
                'label' => 'Контент',
                'items' => [
                    ['label' => 'Тексты', 'url' => ['/text']],
                ]
            ],
            [
                'label' => 'Управление',
                'items' => [
                    ['label' => 'Настройки', 'url' => ['/setting']],
                    ['label' => 'Администраторы', 'url' => ['/user-admin']],
                    ['label' => 'Информация о хостинге', 'url' => ['/site/info']],
                ]
            ],
            '<li class="divider-vertical"></li>',
        ];
        $menuItems[] = Html::tag(
            'li',
            Html::a(
                sprintf('%sВыйти (%s) ', Icon::show('sign-out-alt'), Yii::$app->user->identity->username),
                ['/site/logout'],
                ['class' => 'nav-link', 'data-method' => 'POST']
            ),
            ['class' => 'nav-item skip-search']
        );
    } else {
        $menuItems[] = ['label' => Icon::show('sign-in-alt') . 'Войти', 'url' => ['/site/login']];
    }
    echo Nav::widget([
        'options' => ['class' => 'nav navbar-nav ms-auto d-flex nav-pills justify-content-between'],
        'items' => $menuItems,
    ]);
    NavBar::end();

    ?>

    <div class="container">
        <?= Breadcrumbs::widget(['links' => $this->params['breadcrumbs'] ?? [],]) ?>

        <?= Alert::widget() ?>

        <?= $content ?>
    </div>
</div>


<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

