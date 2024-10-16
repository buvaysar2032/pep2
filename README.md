# Yii2 Backend Template

Основа для backend проекта основанная на Yii2 Framework

[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](https://www.yiiframework.com/)
![Packagist PHP Version](https://img.shields.io/packagist/dependency-v/phpunit/phpunit/php)

## Требования к ПО

1. [PHP](https://www.php.net/downloads.php) версии 8.1 или новее (рекомендуется)
    
    Для установки чистого PHP на Windows необходимо:
    1. Распаковать архив
    2. Добавить распакованную папку (где находится php.exe) в [PATH](https://www.php.net/manual/ru/faq.installation.php#faq.installation.addtopath)
    3. Переименовать `php.ini-development` в `php.ini`
    4. Включить дополнительные расширения в php.ini:
        - bz2
        - curl
        - mbstring
        - exif
        - fileinfo
        - gd
        - imap
        - intl
        - mysqli
        - odbc
        - openssl
        - pdo_mysql
        - soap
        - sockets
        - sodium
        - xml или xsl
2. MySQL от версии 5.5 или MariaDB


## Разворачивание проекта на сервере

1. Слить содержимое в корень сайта
    - Вариант 1: скачать архив, загрузить файлы на хостинг
    - Вариант 2: напрямую копировать содержимое мастер-ветки репозитория на хостинг

2. Прописать webroot на htdocs

    2.1 Проверить что в Apache включен modrewrite и AllowOverwriteAll

    2.2 Если проект устанавливается на Nginx, то нужно настроить редирект всех неизвестных путей в index.php для локаций:
    - `/`
    - `/admin`
    - `/api`

3. Инициализировать приложение:

    ```shell
    php init
    ```

    Возможные окружения:
    - **peppers** - Песочница PEPPERS
    - **dev** - DEV-Сервер Клиента
    - **prod** - PROD-Сервер Клиента

4. Установить зависимости:

    ```
    composer update
    ```

5. Применить миграции:

    ```
    yii migrate
    ```
   
6. Создать админа:

    ```
    php yii user-admin/create
    ```

-------------------
После указанных выше действий панель администратора будет доступна по адресу `/admin/`, а точка входа для api - `/api/v1/`

Для отправки почты нужно внести настройки соединения в разделе "Настройки" в Админ.панели
