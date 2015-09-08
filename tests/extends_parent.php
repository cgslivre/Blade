<?php

require '../src/Blade.php';
require '../src/BladeCache.php';

$view = new Dijix\Blade(array(
	'view_path' => __DIR__.'/views',
	'cache_path' => __DIR__.'/views/cache',
	'cache_mode' => 'dev',
));

echo $view->render('extends_parent');