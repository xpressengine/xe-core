<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * Session class for PHP $_SESSION
 *
 * @author Won-Kyu Park (wkpar@gmail.com)
 * @date  2015/07/09
 */
class SessionCookieBasic extends SessionBase
{
	/**
	 * Get instance of SessionCookieBasic
	 *
	 * @param void $opt Not used
	 * @return SessionCookieBasic instance of SessionCookieBasic
	 */
	function getInstance($opt = null)
	{
		if(!$GLOBALS['__SessionCookieBasic__'])
		{
			$GLOBALS['__SessionCookieBasic__'] = new SessionCookieBasic();
		}
		return $GLOBALS['__SessionCookieBasic__'];
	}

	/**
	 * Constructor
	 *
	 * @return void
	 */
	function SessionCookieBasic()
	{
	}

	/**
	 * Return whether support or not support session
	 *
	 * @return bool Return true on support
	 */
	function isSupport()
	{
		return true;
	}

	function open()
	{
		$sessid = session_name();
		if($sess = $_POST[$sessid])
		{
			session_id($sess);
		}
		session_cache_limiter(''); // to control the cache-control header manually
		// always call session_start()
		if(!empty($_COOKIE[$sessid]))
		{
			session_start();
		}
	}

	function status()
	{
		$sessid = session_name();
		if(!empty($_COOKIE[$sessid]) || session_id() != '')
		{
			return PHP_SESSION_ACTIVE;
		}
		return PHP_SESSION_NONE;
	}

	function lazyStart()
	{
		$sessid = session_name();
		if(session_id() == '')
		{
			session_start();
		}

		return true;
	}

	/**
	 * set a variable in the data store
	 *
	 * @param string $key		Store the variable using this name.
	 *							$key are cache-unique, so storing a second value with the same $key will overwrite the original value.
	 * @param mixed	$buff		The variable to store
	 * @param int	$valid_time 	Time To Live; store $buff in the cache for ttl seconds.
	 * 								After the ttl has passed., the stored variable will be expunged from the cache (on the next request).
	 * 								If no ttl is supplied, use the default valid time SessionCookieBasic::valid_time.
	 * @return bool Returns true on success or false on failure.
	 */
	function set($key, $value)
	{
		self::lazyStart();

		$keys = explode('.', $key);
		$array = &$_SESSION;
		while(count($keys) > 1)
		{
			$key = array_shift($keys);
			// If the key doesn't exist at this depth, we will just create an empty array
			// to hold the next value, allowing us to create the arrays to hold final
			// values at the correct depth. Then we'll keep digging into the array.
			if(!isset($array[$key]) || !is_array($array[$key]))
			{
				$array[$key] = array();
			}
			$array = &$array[$key];
		}
		$array[array_shift($keys)] = $value;
		return true;
	}

	/**
	 * Fetch a stored variable from the session
	 *
	 * @param string $key The $key used to store the value.
	 * @return false|mixed Return false on failure or older then modified time. Return the string associated with the $key on success.
	 */
	function get($key)
	{
		$array = $_SESSION;
		if(is_null($key))
		{
			return $array;
		}

		if(isset($array[$key]))
		{
			return $array[$key];
		}

		foreach(explode('.', $key) as $segment)
		{
			if(!is_array($array) || !array_key_exists($segment, $array))
			{
				return null;
			}
			$array = $array[$segment];
		}
		return $array;
	}

	/**
	 * Return whether session value exists or not
	 *
	 * @param string $key Session key
	 * @return bool Return true or false.
	 */
	function has($key)
	{
		$array = $_SESSION;
		if(empty($array) || is_null($key))
		{
			return false;
		}
		if(array_key_exists($key, $array))
		{
			return true;
		}
		foreach(explode('.', $key) as $segment)
		{
			if(!is_array($array) || !array_key_exists($segment, $array))
			{
				return false;
			}
			$array = $array[$segment];
		}
		return true;
	}

	/**
	 * Delete variable from the session
	 *
	 * @param string|array $keys Used to delete the value.
	 * @return void
	 */
	function delete($keys)
	{
		self::lazyStart();
		$array = &$_SESSION;
		$original = &$array;
		foreach((array) $keys as $key)
		{
			$parts = explode('.', $key);
			while(count($parts) > 1)
			{
				$part = array_shift($parts);
				if(isset($array[$part]) && is_array($array[$part]))
				{
					$array = &$array[$part];
				}
			}
			unset($array[array_shift($parts)]);
			// clean up after each pass
			$array = &$original;
		}
	}

	/**
	 * Truncate all existing variables at the session
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	function truncate()
	{
		self::lazyStart();

		$_SESSION = array();
		session_unset();
		return true;
	}

	/**
	 * Destroy sesssion
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	function destroy()
	{
		self::lazyStart();

		return session_destroy();
	}
}

/* End of file SessionCookieBasic.class.php */
/* Location: ./classes/session/SessionCookieBasic.class.php */
