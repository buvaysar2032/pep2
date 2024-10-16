<?php
//\yii\base\Event::on(
//    \thamtech\scheduler\console\SchedulerController::className(),
//    \thamtech\scheduler\events\SchedulerEvent::EVENT_AFTER_RUN,
//    function ($event) {
//        if (!$event->success) {
//            foreach($event->exceptions as $exception) {
//                throw $exception;
//            }
//        }
//    }
//);

use common\components\UserUrlManager;
use console\controllers\UserAdminController;
use yii\console\controllers\FixtureController;
use yii\log\FileTarget;
use yii\rbac\DbManager;

$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'controllerMap' => [
        'fixture' => [
            'class' => FixtureController::class,
            'namespace' => 'common\fixtures',
        ],
        'admin' => [
            'class' => UserAdminController::class,
        ],
    ],
    'modules' => [
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => DbManager::class,
        ],
        'urlManager' => [
            'class' => UserUrlManager::class,
            'baseUrl' => '/api/v1',
            'hostInfo' => 'Invitro.loc',
            'root' => '/htdocs',
            'hideRoot' => true,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
//                '' => 'site/index',
            ],
        ],
    ],
    'params' => $params,
];
