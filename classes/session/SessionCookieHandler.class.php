<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * SessionCookieHandler
 *
 * @author NAVER (developer@xpressengine.com)
 */
class SessionCookieHandler extends Handler
{
	/**
	 * instance of session handler
	 * @var SessionBase
	 */
	var $handler = null;

	/**
	 * Get a instance of SessionCookieHandler(for singleton)
	 *
	 * @param string $target type of session (object)
	 * @param object $info info. of DB
	 * @param boolean $fallback If set true, use the internal php session
	 * @return SessionCookieHandler
	 */
	function &getInstance($info = null)
	{
		if(!$GLOBALS['__XE_SESSION_COOKIE_HANDLER__'])
		{
			$GLOBALS['__XE_SESSION_COOKIE_HANDLER__'] = new SessionCookieHandler($info);
		}
		return $GLOBALS['__XE_SESSION_COOKIE_HANDLER__'];
	}

	/**
	 * Constructor.
	 *
	 * Do not use this directly. You can use getInstance() instead.
	 *
	 * @see SessionCookieHandler::getInstance
	 * @param string $target type of session
	 * @param object $info info. of DB
	 * @param boolean $fallback If set true, use the default php session
	 * @return SessionCookieHandler
	 */
	function SessionCookieHandler($info = null)
	{
		$class = 'SessionCookieBasic';
		include_once sprintf('%sclasses/session/%s.class.php', _XE_PATH_, $class);
		$this->handler = call_user_func(array($class, 'getInstance'), $url);
	}

	/**
	 * Return whether support or not support session
	 *
	 * @return boolean
	 */
	function isSupport()
	{
		if($this->handler && $this->handler->isSupport())
		{
			return true;
		}

		return false;
	}

	/**
	 * Open session
	 *
	 * @return bool Return false or true
	 */
	function open()
	{
		if(!$this->handler)
		{
			return false;
		}

		return $this->handler->open();
	}

	/**
	 * Close session
	 *
	 * @return bool Return false or true
	 */
	function close()
	{
		if(!$this->handler)
		{
			return false;
		}

		return $this->handler->close();
	}

	/**
	 * Session status
	 *
	 * @return bool Return false or true
	 */
	function status()
	{
		if(!$this->handler)
		{
			return PHP_SESSION_NONE;
		}

		return $this->handler->status();
	}

	/**
	 * Get session data
	 *
	 * @param string $key Session key or null to get all
	 * @param int $modified_time 	Unix time of data modified.
	 * 								If stored time is older then modified time, return false.
	 * @return false|mixed Return false on failure or older then modified time. Return the string associated with the $key on success.
	 */
	function get($key, $modified_time = 0)
	{
		if(!$this->handler)
		{
			return false;
		}

		return $this->handler->get($key, $modified_time);
	}

	/**
	 * Put data into session
	 *
	 * @param string $key Session key
	 * @param mixed $obj	Value of a variable to store. $value supports all data types except resources, such as file handlers.
	 * @param int $valid_time	Time for the variable to live in the session in seconds.
	 * 							After the value specified in ttl has passed the stored variable will be deleted from the session.
	 * 							If no ttl is supplied, use the default valid time.
	 * @return bool|void Returns true on success or false on failure. If use SessionFile, returns void.
	 */
	function set($key, $obj, $valid_time = 0)
	{
		if(!$this->handler || !$key)
		{
			return false;
		}

		return $this->handler->set($key, $obj, $valid_time);
	}

	/**
	 * Return whether session value exists or not
	 *
	 * @param string $key Session key
	 * @param int $modified_time 	Unix time of data modified.
	 * 								If stored time is older then modified time, the data is invalid.
	 * @return bool Return true or false.
	 */
	function has($key, $modified_time)
	{
		if(!$this->handler)
		{
			return false;
		}

		return $this->handler->has($key, $modified_time);
	}

	/**
	 * Delete Session
	 *
	 * @param string $key Session key
	 * @return void
	 */
	function delete($key)
	{
		if(!$this->handler)
		{
			return false;
		}

		return $this->handler->delete($key);
	}

	/**
	 * Truncate all session variables
	 *
	 * @return bool|void Returns true on success or false on failure.
	 */
	function truncate()
	{
		if(!$this->handler)
		{
			return false;
		}

		return $this->handler->truncate();
	}

	/**
	 * Destroy session
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	function destroy()
	{
		if(!$this->handler)
		{
			return false;
		}

		return $this->handler->destroy();
	}
}

/**
 * Base class of Session
 *
 * @author NAVER (developer@xpressengine.com)
 */
class SessionBase
{
	/**
	 * Open session
	 *
	 * @return bool Return false or true
	 */
	function open()
	{
		return true;
	}

	/**
	 * Close session
	 * @return bool Return false or true
	 */
	function close()
	{
		return true;
	}

	/**
	 * Session status
	 * @return bool Return false or true
	 */
	function status()
	{
		return PHP_SESSION_NONE;
	}

	/**
	 * Get session data
	 *
	 * @param string $key Session key or null to get all
	 * @param int $modified_time 	Unix time of data modified.
	 * 								If stored time is older then modified time, return false.
	 * @return false|mixed Return false on failure or older then modified time. Return the string associated with the $key on success.
	 */
	function get($key, $modified_time = 0)
	{
		return false;
	}

	/**
	 * Set data into session
	 *
	 * @param string $key Session key
	 * @param mixed $obj	Value of a variable to store. $value supports all data types except resources, such as file handlers.
	 * @param int $valid_time	Time for the variable to live in the session in seconds.
	 * 							After the value specified in ttl has passed the stored variable will be deleted from the session.
	 * 							If no ttl is supplied, use the default valid time.
	 * @return bool|void Returns true on success or false on failure. If use SessionFile, returns void.
	 */
	function set($key, $obj, $valid_time = 0)
	{
		return false;
	}

	/**
	 * Return whether session value exists or not
	 *
	 * @param string $key Session key
	 * @param int $modified_time 	Unix time of data modified.
	 * 								If stored time is older then modified time, the data is invalid.
	 * @return bool Return true or false.
	 */
	function has($key, $modified_time)
	{
		return false;
	}

	/**
	 * Return whether support or not support session
	 *
	 * @return boolean
	 */
	function isSupport()
	{
		return false;
	}

	/**
	 * Truncate all session variables
	 *
	 * @return bool|void Returns true on success or false on failure.
	 */
	function truncate()
	{
		return false;
	}

	/**
	 * Destroy session
	 *
	 * @return bool Returns true on success or false on failure.
	 */
	function destroy()
	{
		return false;
	}
}
/* End of file SessionCookieHandler.class.php */
/* Location: ./classes/session/SessionCookieHandler.class.php */
