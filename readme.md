# Blade view renderer

A very basic standalone view template renderer using the excellent Blade syntax from Taylor Otwells' excellent Laravel 4 framework. 

Check out the documentation here <http://laravel.com/docs/4.2/templates>

**NB: 2015-09-08:**
**NB: This project is not yet complete and I cannot recommend you use it on a live system.**
**NB: The cache class is currently incomplete**


This class supports only a subset of the Laravel v4.2 Blade syntax, these are the missing features for which I'll add support:

- View location package::path/to/view syntax
- @overwrite - I'll add support for this
	
	
The language translation features need to reference your translation class so can't be set out of the box. However you can pass custom commands to the constructor like this:

```php
$view = new Dijix\Blade(array(
	'commands' => array(
		'/@lang(\'(.*)\')/i' => '<?php echo t(\'$1\') ?>',
		'/@choice(\'(.*),\s*'(.*)'\')/i' => '<?php echo tc(\'$1\', \'$2\') ?>'
	),
));
```	

This class has a couple of additional features not included with Laravel Blade v4.2	

- A 'development' mode which will not hash the filenames so you can quickly see where the errors are.
- It will optionally strip whitespace, set strip_whitespace = true
- Supports the @set command - the brainchild of @alexdover
- Supports an @unset command

### Installation

```bash
composer require dijitaltrix/blade
```

### Usage

Create a Blade instance passing the settings to the constructor, this example uses the 'dev' cache mode which does not hash your view filenames and also strips all unnecessary whitespace from the output.

```php

use Dijix\Blade;

$view = new Dijix\Blade(array(
	'view_path' => 'app/views',
	'cache_path' => 'cache/views',
	'cache_mode' => 'dev',
	'strip_whitespace' => true,
));

$view->render('path/to/view/file', array(
	'name' => 'Foo',
	'email' => 'foo@bar.com'
));

```

You can also render output from a string using the renderString method

```php

use Dijix\Blade;

$blade_string = "<p>Hello {{ $name }}</p>";

$view = new Dijix\Blade(array(
	'view_path' => 'app/views',
	'cache_path' => 'cache/views',
	'cache_mode' => 'dev',
	'strip_whitespace' => true,
));

$view->renderString($blade_string, array(
	'name' => 'Foo'
));

```

### Using with Slim Framework 3

I've included a compatibility class for use with Slim Framework 3. 


## Why?

I had a requirement to port a Laravel 4.2 application to Slim Framework, I was too lazy to rewrite all my views so I had to keep Blade but I didn't want to have to install a load of other dependencies and get stuck in an eternal dependency upgrade cycle, so here it is - a standalone dependency free Blade renderer! 
I imagine it would be useful to anyone on a low powered device, Raspberry Pi etc.. 