<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=db;dbname=app', // Sử dụng tên dịch vụ 'db' (tên container của MySQL trong docker-compose.yml)
    'username' => 'devs',
    'password' => 'devs123!',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    //'enableSchemaCache' => true,
    //'schemaCacheDuration' => 60,
    //'schemaCache' => 'cache',
];
