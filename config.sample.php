<?php
use Monolog\Logger;
use Oforge\Engine\Modules\Core\Helper\Statics;

return [
    'mode'       => 'development', // production|development
    // Monolog settings
    'logger'     => [
        [
            'name'  => 'system',
            'level' => Logger::DEBUG,
        ],
        [
            'name'  => 'plugins',
            'level' => Logger::DEBUG,
        ],
    ],
    //Doctrine Settings
    'db'         => [
        // if true, metadata caching is forcefully disabled
        'dev_mode'      => true,

        // path where the compiled metadata info will be cached. make sure the path exists and it is writable
        'cache_dir'     => ROOT_PATH . '/' . Statics::DB_CACHE_DIR,

        // you should add any other path containing annotated entity classes
        'metadata_dirs' => [ROOT_PATH . '/Engine', ROOT_PATH . '/Plugins'],

        'connection' => [
            'driver'   => 'pdo_mysql',
            'host'     => 'localhost',
            'port'     => 3306,
            'dbname'   => '', // your database name here
            'user'     => '', // your database user name here
            'password' => '', // your database user password here
            'charset'  => 'utf8' // we expect to use utf8 charset everywhere (webserver, mysql, php, etc)
        ],
    ],
    'jwt_salt'   => 'my awesome salt', // Change this salt for security
    // config for db value encrypt/decrypt
    'encryption' => [
        // 'method' => 'aes-128-gcm', // Default: aes-128-gcm
        'key' => 'my awesome salt',
    ],
];
