<?php

namespace Dijix;

use Exception;
use Dijix\BladeCache;

class Blade {
	
	# user data
	protected $data;
	# path to views folder
	protected $view_path;
	# strip white space on output, true or false
	protected $strip_whitespace;
	# holds section content
	protected $sections;
	# holds additional commands like: expr, replace
	protected $commands;
	# cache driver
	protected $cache;

	
    public function __construct($settings=array())
    {
		# set cache driver
		$this->cache = new BladeCache($settings);
		# configure
		$this->configure($settings);
		
	}
	
	public function configure($settings=array())
	{
		# set defaults
		$this->view_path = null;
		$this->strip_whitespace = false;
		$this->sections = array();
		$this->commands = array();
		
		# apply user settings
		foreach ($settings as $k=>$v) {
			if (property_exists($this, $k)) {
				$this->$k = $v;
			}
		}

		# tidy up
		$this->view_path = rtrim($this->view_path, '/');
		
	}
	
	public function render($filename, $data=array())
	{
		try {
			# get view file as array of lines
			# which we will step through to match blade commands
			$__blade_view = $this->load($filename);
			
			# compile the template to a unique var name which we
			# won't clobber with user data
			$__blade_view = $this->compile($__blade_view);
			
			# cache the template file
			$this->cache->store($filename, $__blade_view);
			
			# render the template with the user data
			# extract all data to current scope
			extract($data);
			ob_start();
			include($this->cache->filepath($filename));
			ob_end_clean();
			
			# return the template as a string
			return $this->stripWhitespace($__blade_view);
			
		} catch (Exception $e) {
			throw $e;
			
		}
		
	}
	
	public function renderString($str, $data=array())
	{
		try {
			# compile the template from string, useful when
			# rendering text for emails etc from a database
			
			
		} catch (Exception $e) {
			throw $e;
				
		}
		
	}
	
	private function compile($view)
	{
		# capture section content
		foreach ($this->getTags('/\@section\(\'(.+?)\'\)/i', $view) as $match) {
			$match = trim($match);
			$this->sections[$match] = $this->getContent($match, $view);
		}
		
		# load extended template
		foreach ($this->getTags('/\@extends\(\'(.+?)\'\)/i', $view) as $match) {
			$view = $this->compile($this->load($match));
		}
		
		# merge sections
		foreach ($this->sections as $section=>$content) {
			if (isset($this->section_replace[$section])) {
				$view = $this->replaceSection($section, $content, $view);
			} else {
				$view = $this->replaceTag("@yield('$section')", $content, $view);
			}
		}
		
		# render includes
		# TODO handle arrays passed to preg_replace, always return array of lines
		foreach ($this->getTags('/\@include\(\'(.+?)\'\)/i', $view) as $include) {
			$view = $this->replaceTag("@include('$include')", $this->compile($this->load($include)), $view);
		}
		
		# convert blade commands to php equivalents
		$view = preg_replace(array_keys($this->getCommands()), array_values($this->getCommands()), $view);

		return $view;
		
	}
	
	private function getCommands()
	{
		return array_merge(array(
			# turn Blade comment to PHP comment
			'/(\s*){{--\s*(.+?)\s*--}}/i' => '$1<?php /* $2 */ ?>',
			# echo an escaped variable 
			'/(\s*){{{\s*(.+?)\s*}}}/i' => '$1<?php echo html_entities($2); ?>',
			# echo a variable
			'/(\s*){{\s*(.+?)\s*}}/i' => '$1<?php echo $2; ?>',
			# echo with a default
			'/(\s*){{\s*(.+?)\s*or\s*(.+?)}}/i' => '$1<?php isset($2) ? echo $2 : $3; ?>',
			# set and unset statements
			"/(\s*)@set\('(.*?)'\,(.*)\)/i" => '$1<?php $$2 = $3; ?>',
			"/(\s*)@unset\((.*?)\)/i" => '$1<?php unset($2); ?>',
			# unless statement
			'/(\s*)@unless\((.+?)\)/i' => '$1<?php if ( ! $2): ?>',
			'/(\s*)@endunless(\s*)/i' => '$1<?php endif; ?>',
			# handle forelse
			'/(\s*)@forelse\s*\(\s*(\S*)\s*as\s*(\S*)\s*\)(\s*)/i' => "$1<?php if ( ! empty($2)): ?>$1<?php foreach ($2 as $3): ?>$4",
			'/(\s*)@empty(\s*)/' => "$1<?php endforeach; ?>$1<?php else: ?>$2",
			'/(\s*)@endforelse(\s*)/' => '$1<?php endif; ?>$2',
			# handle loops
			'/(?(R)\((?:[^\(\)]|(?R))*\)|(?<!\w)(\s*)@(if|elseif|foreach|for|while)(\s*(?R)+))/i' => '$1<?php $2$3: ?>',
			'/(\s*)@(else)(\s*)/i' => '$1<?php else: ?>',
			'/(\s*)@(endif|endforeach|endfor|endwhile)(\s*)/' => '$1<?php $2; ?>$3',
			
		), $this->commands);
	}
	
	private function getContent($name, $view)
	{
		preg_match("/@section\(\'$name\'\)(.*?)@(stop|show)/is", $view, $match);
		if (isset($match[1])) {
			# check for merge with parent
			$content = $match[1];
			if (isset($this->sections[$name])) {
				# kludge to replace @section...@show
				$this->section_replace[$name] = true;
				if (stristr($this->sections[$name], '@parent')) {
					$content = str_replace('@parent', $content, $this->sections[$name]);
				} else {
					$content = $this->sections[$name].$content;
				}
			} 
			
			return $content;
	
		}
		
	}
	
	private function getTags($expr, $view)
	{
		if (preg_match_all($expr, $view, $matches)) {
			return $matches[1];
		}
				
		return array();
		
	}
	
	private function load($filename)
	{
		# swap meaningless dotted notation
		$filename = str_replace('.', '/', $filename);
		# we're always looking for .blade.php files
		$filename = "$filename.blade.php";
		# read file into string
		if (file_exists("$this->view_path/$filename")) {
			return file_get_contents("$this->view_path/$filename");
		}
		
		throw new Exception('Failed to load view template');
		
	}
	
	private function replaceSection($name, $replace, $view)
	{
		return preg_replace("/@section\(\'$name\'\)(.*?)@(stop|show)/is", $replace, $view);

	}
	
	private function replaceTag($expr, $replace, $view)
	{
		return str_replace($expr, $replace, $view);

	}
	
	private function stripWhitespace($str)
	{
		if ($this->strip_whitespace) {
			$str = trim($str);
			$str = preg_replace('/>(\s*?)</is', '><', $str);
		}
		
		return $str;
	}
	

}