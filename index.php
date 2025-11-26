<?php

// check the vendor/autoload.php file
if (file_exists(__DIR__ . '/' . 'vendor/autoload.php')) {

	$app = require_once __DIR__ . '/' . 'server.php';
	$app->run();

} else {
	echo "Please run <code>composer install </code> - <a target='_blank' href='https://getcomposer.org/doc/03-cli.md#install-i'>Read more from composer</a>";
	exit();
}

?>