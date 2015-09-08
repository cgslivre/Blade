<?php

namespace Dijix;

use Exception;

class BladeCache {
	
	# in dev mode we use descriptive names
	# any other mode uses hased filenames
	protected $mode;
	
	protected $cache_path;
	
	
	
    public function __construct($settings=array())
    {
		# set cache driver
		$this->mode = $settings['cache_mode'];
		$this->cache_path = rtrim($settings['cache_path'], '/');
    
	}
	
	# check the cache file exists and is not stale
	# @return Boolean
	public function exists($filename, $timestamp)
	{
		$filepath = $this->filepath($filename);
		if (file_exists($filepath)) {
			# check cache file modification time
			# to see if it is 'stale' 
			if (filemtime($filepath) >= $timestamp) {
				return true;
			}
		}
		
		return false;
		
	}
	
	# return the path to the file
	public function filepath($filename)
	{
		if ($this->mode == 'dev')
		{
			# use descriptive path, helps with errors
			$path = str_replace('/', '-', $filename);
			return $this->cache_path.'/'.$path;
		}
		else
		{
			# use hashed path
			return $this->cache_path.'/'.md5($filename);
		}
			
	}
	
	public function store($filename, $contents)
	{
		$folder = dirname($this->filepath($filename));
		if ( ! is_dir($folder)) {
			if ( ! mkdir($folder, 0775, $recursive=true)) {
				throw new Exception('Could not create cache folder');
			}
		}
		
		$result = file_put_contents($this->filepath($filename), $contents);
		if ($result === false) {
			throw new Exception('Could not store cache file');
		}
		
		return true;
		
	}
	
}
