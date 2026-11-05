<?php
/* Copyright (C) XEHub <https://www.xehub.io> */

/**
 * - Security class
 * - This class helps to solve security problems.
 * @author XEHub (developers@xpressengine.com)
 * @package /classes/security
 * @version 0.1
 */
class Security
{
	/** Check redirect destinations against configured HTTP(S) hosts and ports. */
	public static function isInternalURL($url)
	{
		$parsed = self::_parseURL($url, true);
		return $parsed !== false && (isset($parsed['relative']) || self::_matchesConfiguredURL($parsed, false));
	}

	/** Request headers must contain an absolute URL for the current site. */
	public static function isTrustedRequestURL($url)
	{
		$parsed = self::_parseURL($url, false);
		return $parsed !== false && self::_matchesConfiguredURL($parsed, true);
	}

	public static function getSafeRedirectURL($url, $fallback = null, $allow_external = false)
	{
		// Only callers holding trusted configuration may opt in to external HTTP(S).
		if($allow_external && self::_parseURL($url, true) !== false) return $url;
		if(self::isInternalURL($url)) return $url;
		if($fallback === null) $fallback = Context::getRequestUri();
		if(self::isInternalURL($fallback)) return $fallback;
		$fallback = Context::getDefaultUrl();
		return self::isInternalURL($fallback) ? $fallback : './';
	}

	public static function isSameOrigin($url, $reference)
	{
		$a = self::_parseURL($url, false);
		$b = self::_parseURL($reference, false);
		return $a !== false && $b !== false && $a['scheme'] === $b['scheme'] && $a['host'] === $b['host'] && $a['port'] === $b['port'];
	}

	private static function _parseURL($url, $allow_relative)
	{
		if(!is_string($url) || $url === '' || !preg_match('//u', $url) || preg_match('/[\x00-\x20\x7f<>"`]/', $url)) return false;
		$url = str_replace('\\', '/', $url);
		$has_scheme = preg_match('!^[^/?#]*:!', $url);
		if($has_scheme && !preg_match('!^https?://!i', $url)) return false;
		if(substr($url, 0, 2) === '//')
		{
			// Resolve network-path references using the actual request scheme.
			$url = (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https:' : 'http:') . $url;
		}
		$parsed = @parse_url($url);
		if($parsed === false || isset($parsed['user']) || isset($parsed['pass'])) return false;
		if(!isset($parsed['host']))
		{
			return $allow_relative && !$has_scheme && substr($url, 0, 2) !== '//' ? array('relative' => true) : false;
		}
		if(empty($parsed['scheme']) || !in_array(strtolower($parsed['scheme']), array('http', 'https'))) return false;
		$host = strtolower($parsed['host']);
		if(preg_match('/[^\x00-\x7f]/', $host))
		{
			require_once _XE_PATH_ . 'libs/idna_convert/idna_convert.class.php';
			$idn = new idna_convert(array('idn_version' => 2008));
			$host = $idn->encode($host);
			if(!is_string($host) || $host === '') return false;
		}
		$host = strtolower($host);
		if(!preg_match('/^[a-z0-9_.-]+$/D', $host) && !(substr($host, 0, 1) === '[' && substr($host, -1) === ']' && filter_var(substr($host, 1, -1), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))) return false;
		$scheme = strtolower($parsed['scheme']);
		$port = isset($parsed['port']) ? (int)$parsed['port'] : ($scheme === 'https' ? 443 : 80);
		if($port < 1 || $port > 65535) return false;
		return array('scheme' => $scheme, 'host' => $host, 'port' => $port, 'explicit_port' => isset($parsed['port']));
	}

	private static function _matchesConfiguredURL($parsed, $current_site_only)
	{
		$bases = array(Context::getDefaultUrl());
		$site = Context::get('site_module_info');
		if(is_object($site) && !empty($site->domain) && (strpos($site->domain, '.') !== false || strpos($site->domain, ':') !== false))
		{
			$domain = strpos($site->domain, '://') === false ? 'http://' . $site->domain : $site->domain;
			if($current_site_only) $bases = array($domain);
			else $bases[] = $domain;
		}
		if(!Context::isInstalled() && !$bases[0]) $bases = array(Context::getRequestUri());
		foreach($bases as $base)
		{
			$reference = self::_parseURL($base, false);
			if($reference === false || $reference['host'] !== $parsed['host']) continue;
			$port = $reference['port'];
			$configured_port = Context::get($parsed['scheme'] === 'https' ? '_https_port' : '_http_port');
			if($reference['scheme'] !== $parsed['scheme'] || !$reference['explicit_port'])
			{
				$port = $configured_port ? (int)$configured_port : ($parsed['scheme'] === 'https' ? 443 : 80);
			}
			if($parsed['port'] === $port) return true;
		}
		return false;
	}

	/** Generate tokens on PHP 5.3 as well as newer versions; fail closed without entropy. */
	public static function getRandomToken($bytes = 32)
	{
		if(function_exists('random_bytes'))
		{
			try { return bin2hex(random_bytes($bytes)); } catch(Exception $e) { }
		}
		if(function_exists('openssl_random_pseudo_bytes'))
		{
			$strong = false;
			$random = openssl_random_pseudo_bytes($bytes, $strong);
			if($strong && is_string($random) && strlen($random) === $bytes) return bin2hex($random);
		}
		$handle = @fopen('/dev/urandom', 'rb');
		if(!$handle) return false;
		$random = fread($handle, $bytes);
		fclose($handle);
		return is_string($random) && strlen($random) === $bytes ? bin2hex($random) : false;
	}

	private static function _getRequestOrigin()
	{
		$scheme = !empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off' ? 'https' : 'http';
		return isset($_SERVER['HTTP_HOST']) ? $scheme . '://' . $_SERVER['HTTP_HOST'] . '/' : '';
	}

	public static function checkCSRFRequest()
	{
		if(!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') return false;
		if(in_array(Context::get('act'), array('dispMemberLogout', 'procMemberLogout', 'procAdminLogout'))) return self::checkLogoutRequest();
		// Optional SSL can make a legitimate HTTP -> HTTPS form cross-site in
		// Fetch Metadata. Validate its explicit origin before using that hint.
		if(!empty($_SERVER['HTTP_ORIGIN'])) return self::isTrustedRequestURL($_SERVER['HTTP_ORIGIN']);
		if(!empty($_SERVER['HTTP_REFERER'])) return self::isTrustedRequestURL($_SERVER['HTTP_REFERER']);
		return isset($_SERVER['HTTP_SEC_FETCH_SITE']) && $_SERVER['HTTP_SEC_FETCH_SITE'] === 'same-origin' && self::isTrustedRequestURL(self::_getRequestOrigin());
	}

	/** Check all HTTP logout entry points, including legacy GET links. */
	public static function checkLogoutRequest()
	{
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : '';
		if($method !== 'GET' && $method !== 'POST') return false;
		if($method === 'POST' && isset($_SESSION['XE_LOGOUT_TOKEN']))
		{
			$token = Context::get('logout_token');
			$expected = $_SESSION['XE_LOGOUT_TOKEN'];
			if(is_string($token) && is_string($expected) && strlen($expected) === 64 && strlen($token) === 64)
			{
				$difference = 0;
				for($i = 0; $i < 64; $i++) $difference |= ord($token[$i]) ^ ord($expected[$i]);
				if($difference === 0) return true;
			}
		}
		$origin = self::_getRequestOrigin();
		if(!self::isTrustedRequestURL($origin)) return false;
		if(!empty($_SERVER['HTTP_ORIGIN'])) return self::isSameOrigin($_SERVER['HTTP_ORIGIN'], $origin);
		if(isset($_SERVER['HTTP_SEC_FETCH_SITE']))
		{
			$site = $_SERVER['HTTP_SEC_FETCH_SITE'];
			$destination = isset($_SERVER['HTTP_SEC_FETCH_DEST']) ? $_SERVER['HTTP_SEC_FETCH_DEST'] : '';
			if($site === 'same-origin') return $destination === 'document' || $destination === 'empty' || $destination === '';
			return $site === 'none' && $method === 'GET' && $destination === 'document' && isset($_SERVER['HTTP_SEC_FETCH_MODE']) && $_SERVER['HTTP_SEC_FETCH_MODE'] === 'navigate';
		}
		return !empty($_SERVER['HTTP_REFERER']) && self::isSameOrigin($_SERVER['HTTP_REFERER'], $origin);
	}

	/**
	 * Action target variable. If this value is null, the method will use Context variables
	 * @var mixed
	 */
	var $_targetVar = NULL;

	/**
	 * @constructor
	 * @param mixed $var Target context
	 * @return void
	 */
	function __construct($var = NULL)
	{
		$this->_targetVar = $var;
	}

	/**
	 * - Convert special characters to HTML entities for the target variables.
	 * - The results of conversion are equivalent to the results of htmlspecialchars() which is a native function of PHP.
	 * @params string $varName. A variable's name to convert to process properties of an object or elements of an array,
	 * separate the owner(object or array) and the item(property or element) using a dot(.)
	 * @return mixed
	 */
	function encodeHTML(/* , $varName1, $varName2, ... */)
	{
		$varNames = func_get_args();
		if(count($varNames) < 0)
		{
			return FALSE;
		}

		$use_context = is_null($this->_targetVar);
		if(!$use_context)
		{
			if(!count($varNames) || (!is_object($this->_targetVar) && !is_array($this->_targetVar)))
			{
				return $this->_encodeHTML($this->_targetVar);
			}

			$is_object = is_object($this->_targetVar);
		}

		foreach($varNames as $varName)
		{
			$varName = explode('.', $varName);
			$varName0 = array_shift($varName);
			if($use_context)
			{
				$var = Context::get($varName0);
			}
			elseif($varName0)
			{
				$var = $is_object ? $this->_targetVar->{$varName0} : $this->_targetVar[$varName0];
			}
			else
			{
				$var = $this->_targetVar;
			}
			$var = $this->_encodeHTML($var, $varName);

			if($var === FALSE)
			{
				continue;
			}

			if($use_context)
			{
				Context::set($varName0, $var);
			}
			elseif($varName0)
			{
				if($is_object)
				{
					$this->_targetVar->{$varName0} = $var;
				}
				else
				{
					$this->_targetVar[$varName0] = $var;
				}
			}
			else
			{
				$this->_targetVar = $var;
			}
		}

		if(!$use_context)
		{
			return $this->_targetVar;
		}
	}

	/**
	 * Convert special characters to HTML entities for the target variables.
	 * @param mixed $var
	 * @param array $name
	 * @return mixed
	 */
	function _encodeHTML($var, $name = array())
	{
		if(is_string($var))
		{
			if(strncmp('$user_lang->', $var, 12) !== 0)
			{
				$var = htmlspecialchars($var, ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
			}

			return $var;
		}

		if(!count($name) || (!is_array($var) && !is_object($var)))
		{
			return false;
		}

		$is_object = is_object($var);
		$name0 = array_shift($name);

		if(strlen($name0))
		{
			$target = $is_object ? $var->{$name0} : $var[$name0];
			$target = $this->_encodeHTML($target, $name);

			if($target === false)
			{
				return $var;
			}

			if($is_object)
			{
				$var->{$name0} = $target;
			}
			else
			{
				$var[$name0] = $target;
			}

			return $var;
		}

		foreach($var as $key => $target)
		{
			$cloned_name = array_slice($name, 0);
			$target = $this->_encodeHTML($target, $name);
			$name = $cloned_name;

			if($target === false)
			{
				continue;
			}

			if($is_object)
			{
				$var->{$key} = $target;
			}
			else
			{
				$var[$key] = $target;
			}
		}

		return $var;
	}

	/**
	 * @brief check XML External Entity
	 *
	 * @see from drupal. https://github.com/drupal/drupal/commit/90e884ad0f7f2cf269d953f7d70966de9fd821ff
	 *
	 * @param string $xml
	 * @return bool
	 */
	static function detectingXEE($xml)
	{
		if(!$xml) return FALSE;

		if(strpos($xml, '<!ENTITY') !== FALSE)
		{
			return TRUE;
		}

		// Strip XML declaration.
		$header = preg_replace('/<\?xml.*?\?'.'>/s', '', substr($xml, 0, 100), 1);
		$xml = trim(substr_replace($xml, $header, 0, 100));
		if($xml == '')
		{
			return TRUE;
		}

		// Strip DTD.
		$header = preg_replace('/^<!DOCTYPE[^>]*+>/i', '', substr($xml, 0, 200), 1);
		$xml = trim(substr_replace($xml, $header, 0, 200));
		if($xml == '')
		{
			return TRUE;
		}

		// Confirm the XML now starts with a valid root tag. A root tag can end in [> \t\r\n]
		$root_tag = substr($xml, 0, strcspn(substr($xml, 0, 20), "> \t\r\n"));

		// Reject a second DTD.
		if(strtoupper($root_tag) == '<!DOCTYPE')
		{
			return TRUE;
		}

		if(!in_array($root_tag, array('<methodCall', '<methodResponse', '<fault')))
		{
			return TRUE;
		}

		return FALSE;
	}
}
/* End of file : Security.class.php */
/* Location: ./classes/security/Security.class.php */
