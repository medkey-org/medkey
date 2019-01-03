<?php
/**
 * @copyright 2012-2019 Medkey
 */

return [
    'class' => \app\common\db\Connection::class,
    'dsn' => getenv('APP_DB_CONNECTION') . ':host=' . getenv('APP_DB_HOST') . ';port=' . getenv('APP_DB_PORT') . ';dbname=' . getenv('APP_DB_NAME'),
    'username' => getenv('APP_DB_USERNAME'),
    'password' => getenv('APP_DB_PASSWORD'),
    'charset' => 'utf8',
];
