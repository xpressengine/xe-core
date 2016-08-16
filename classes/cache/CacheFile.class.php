<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * Cache class for file
 *
 * Filedisk Cache Handler
 *
 * @author NAVER (developers@xpressengine.com)
 */
class CacheFile extends CacheBase
{
	/**
	 * Path that value to stored
	 * @var string
	 */
	var $cache_dir = 'files/cache/store/';
	/**
	 * default target name
	 * @var string
	 */
	var $target = 'default';

	/**
	 * cache name
	 * @var string	$name
	 */
	var $name = __CLASS__;

	/**
	 * cache version
	 * @var string	$version
	 */
	var $version = '0.7';

	/**
	 * absolute cache_dir
	 * @var string
	 */
	var $cache_path;

	/**
	 * Get instance of CacheFile
	 *
	 * @return CacheFile instance of CacheFile
	 */
	function getInstance($target = 'default')
	{
		if(!$GLOBALS['__CacheFile__'][$target])
		{
			$GLOBALS['__CacheFile__'][$target] = new CacheFile($target);
		}
		return $GLOBALS['__CacheFile__'][$target];
	}

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function CacheFile($target = 'default')
	{
		$this->target = $target;
		$this->cache_path = _XE_PATH_ . $this->cache_dir;
		FileHandler::makeDir($this->cache_path);
	}

	/**
	 * Get cache file name by key
	 *
	 * @param string $key The key that will be associated with the item.
	 * @return string Returns cache file path
	 */
	function getCacheFileName($key, $absolute = TRUE)
	{
		if(in_array($this->target, array('object', 'template')))
		{
			$type = '.php';
			$prefix = __XE_VERSION__ . ':' . $this->target;
		}
		else
		{
			$type = '';
			$prefix = $this->target;
		}
		$key = $prefix . ':' . $key;
		if($absolute)
		{
			return $this->cache_path . str_replace(':', DIRECTORY_SEPARATOR, $key) . $type;
		}
		return $this->cache_dir . str_replace(':', DIRECTORY_SEPARATOR, $key) . $type;
	}

	/**
	 * Return whether support or not support cache
	 *
	 * @return true
	 */
	function isSupport()
	{
		return true;
	}

	/**
	 * Return cache type
	 *
	 * @return string file
	 */
	function getType()
	{
		return 'file';
	}

	/**
	 * Cache a variable in the data store
	 *
	 * @param string $key Store the variable using this name.
	 * @param mixed $obj The variable to store
	 * @param int $valid_time Not used
	 * @return void
	 */
	function put($key, $obj, $valid_time = 0, $params = null)
	{
		// set mtime or ttl
		if($valid_time > 0)
		{
			$mtime_or_ttl = $valid_time;
		}
		else
		{
			$mtime_or_ttl = time();
		}

		$depends = '';
		if(!empty($params['depends']))
		{
			$depend = array();
			$depend[] = '/**';
			foreach($params['depends'] as $dep)
			{
				$depend[] = ' * @depends '.$dep;
			}
			$depend[] = ' */';

			$depends = implode(PHP_EOL, $depend);
		}

		if(in_array($this->target, array('object')) || is_array($obj) || is_object($obj))
		{
			$cache_file = $this->getCacheFileName($key);
			$content = array();
			$content[] = '<' . '?php /* ' . $this->name . ' ' . $this->version . ' ' . $mtime_or_ttl . ' ' . $key . ' */';
			if(!empty($depends))
			{
				$content[] = $depends;
			}
			$content[] = 'if(!defined(\'__XE__\')) { exit(); }';
			$content[] = 'return \'' . addslashes(serialize($obj)) . '\';';
			FileHandler::writeFile($cache_file, implode(PHP_EOL, $content));
			if(function_exists('opcache_invalidate'))
			{
				@opcache_invalidate($cache_file, true);
			}
		}
		else
		{
			$cache_file = $this->getCacheFileName($key);

			$header = '';
			if($obj[1] == '?' && $obj[0] == '<' && strpos($obj, 'php ') == 2)
			{
				$header = '<' . '?php /* ' . $this->name . ' ' . $this->version . ' ' .
						$mtime_or_ttl . ' ' . $key . ' */';
				if(!empty($depends))
				{
					$header .= PHP_EOL . $depends;
				}
				$header .= ' ?' . '>' . PHP_EOL;
			}
			FileHandler::writeFile($cache_file, $header . $obj);
		}
	}

	/**
	 * Return whether cache is valid or invalid
	 *
	 * @param string $key Cache key
	 * @param int $mtime or ttl
	 * @return bool Return true on valid or false on invalid.
	 */
	function isValid($key, $mtime_or_ttl = 0)
	{
		$cache_file = $this->getCacheFileName($key);

		if(file_exists($cache_file))
		{
			$cache_mtime = filemtime($cache_file);
			if(filemtime(__FILE__) > $cache_mtime)
			{
				return false;
			}

			// check cache header
			$fp = fopen($cache_file, 'r');
			if(!is_resource($fp))
			{
				return false;
			}
			$header = fgets($fp, 4096);

			// get some params
			while(($line = fgets($fp, 4096)) !== false)
			{
				if($line[1] == '*')
				{
					if($line[2] != ' ' || $line[3] != '@')
					{
						continue;
					}
					$line = rtrim($line);
					if(($pos = strpos($line, ' ', 4)) !== false)
					{
						$key = substr($line, 4, $pos - 4);
						$val = substr($line, $pos + 1);

						// check depends mtime
						if($key == 'depends')
						{
							if(!file_exists($val) || filemtime($val) > $cache_mtime)
							{
								fclose($fp);
								return false;
							}
						}
					}
					continue;
				}
				break;
			}
			fclose($fp);

			if($header[1] == '?' && $header[0] == '<' && strpos($header, 'php ') == 2)
			{
				// <?php /* CacheFile 0.x 1234 xyz */
				$chunks = explode(' ', $header);

				// mtime or ttl metadata ?
				if(isset($chunks[4]) && is_numeric($chunks[4]))
				{
					// mtime > cache_mtime case (always true)
					// ttl > cache_ttl case
					if($mtime_or_ttl > 0 && $mtime_or_ttl > $chunks[4])
					{
						if($mtime_or_ttl <= 31536000)
						{
							$mtime_or_ttl = $chunks[4];
						}
					}
					else if($chunks[4] <= 31536000)
					{
						$mtime_or_ttl = $chunks[4];
					}
				}
			}

			if($mtime_or_ttl > 0)
			{
				// less than 60*60*24*365(1 year) means TTL
				if($mtime_or_ttl <= 31536000)
				{
					$modified = (time() - $cache_mtime) > $mtime_or_ttl;
				}
				else
				{
					$modified = $cache_mtime < $mtime_or_ttl;
				}
				if($modified)
				{
					FileHandler::removeFile($cache_file);
					return false;
				}
			}

			return $cache_file;
		}

		return false;
	}

	/**
	 * Fetch a stored variable from the cache
	 *
	 * @param string $key The $key used to store the value.
	 * @param int $mtime_or_ttl
	 * @return false|mixed Return false on failure. Return the string associated with the $key on success.
	 */
	function get($key, $mtime_or_ttl = 0, $raw_key = FALSE)
	{
		if(($cache_file = $this->isValid($key, $mtime_or_ttl)) === false)
		{
			return false;
		}
		if($raw_key)
		{
			return $this->getCacheFileName($key, FALSE);
		}

		// check header
		$fp = fopen($cache_file, 'r');
		if(!is_resource($fp))
		{
			return false;
		}
		$line = fgets($fp, 4096);
		fclose($fp);
		if($line[1] == '?' && $line[0] == '<' && strpos($line, 'php ') == 2)
		{
			$content = include($cache_file);
			return unserialize(stripslashes($content));
		}

		$content = file_get_contents($cache_file);
		return $content;
	}

	/**
	 * Delete variable from the cache(private)
	 *
	 * @param string $_key Used to store the value.
	 * @return void
	 */
	function _delete($_key)
	{
		$cache_file = $this->getCacheFileName($_key);
		if(function_exists('opcache_invalidate'))
		{
			@opcache_invalidate($cache_file, true);
		}
		FileHandler::removeFile($cache_file);
	}

	/**
	 * Delete variable from the cache
	 *
	 * @param string $key Used to store the value.
	 * @return void
	 */
	function delete($key)
	{
		$this->_delete($key);
	}

	/**
	 * Truncate all existing variables at the cache
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	function truncate()
	{
		if(in_array($this->target, array('object', 'template')))
		{
			$prefix = __XE_VERSION__ . ':' . $this->target;
		}
		else
		{
			$prefix = $this->target;
		}
		FileHandler::removeFilesInDir($this->cache_path . $prefix);
	}

}
/* End of file CacheFile.class.php */
/* Location: ./classes/cache/CacheFile.class.php */
