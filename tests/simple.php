<?php

require '../src/Blade.php';
require '../src/BladeCache.php';

$view = new Dijix\Blade(array(
	'strip_whitespace' => true,
	'view_path' => __DIR__.'/views',
	'cache_path' => __DIR__.'/views/cache',
	'cache_mode' => 'dev',
	'commands' => array(
		"/@lang\((.+?)\)/i" => "<?php echo t($1) ?>",
		"/@choice\((.+?),\s*(.+?)\)/i" => "<?php echo tc($1, $2) ?>"
	),
));

echo $view->render('simple', array(
	'title' => 'Simple test page',
	'night' => 'tonight',
	'day' => 'tomorrow',
	'posts' => array(),
));

function t($str) {
	return $str;
}
function tc($str, $value) {
	return $str;
}