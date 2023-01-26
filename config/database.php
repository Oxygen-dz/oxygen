<?php

/**
 * database.php
 *
 * Database Access
 *
 * @category   E-Wallet
 * @package    Oxygen
 * @author     Redwan Aouni <aouniradouan@gmail.com>
 * @copyright  2021 - Oxygen
 * @version    1.0.0
 * @since      File available since Release 1.0.0
 */



$DATABASE     =    [

    'default' => $_ENV['DB_CONNECTION'],

    'connections' => [

        'mysql' => [
            'driver' => 'mysql',
            'url' => null,
            'dsn' => 'mysql:host='.$_ENV['DB_HOST'].';dbname='.$_ENV['DB_DATABASE'],
            'host' => 'localhost',
            'port' => '3306',
            'database' => '',
            'username' => $_ENV['DB_USERNAME'],
            'password' => $_ENV['DB_PASSWORD'],
            'prefix'   => ''
        ]

    ]


];

$DefaultDriver      =   $DATABASE['default'];

$database           =   new Nette\Database\Connection($DATABASE['connections'][$DefaultDriver]['dsn'], $DATABASE['connections'][$DefaultDriver]['username'], $DATABASE['connections'][$DefaultDriver]['password']);
