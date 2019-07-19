<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * @class  krzip
 * @author XEHub (developers@xpressengine.com)
 * @brief  Krzip module high class.
 */

if(!function_exists('lcfirst'))
{
	function lcfirst($text)
	{
		return strtolower(substr($text, 0, 1)) . substr($text, 1);
	}
}

class krzip extends ModuleObject
{
	public static $sequence_id = 0;

	public static $default_config = array('api_handler' => 0, 'postcode_format' => 5, 'show_box' => 'all');

	public static $api_list = array('daumapi', 'epostapi');

	public static $epostapi_host = 'http://biz.epost.go.kr/KpostPortal/openapi';

	function moduleInstall()
	{
		return $this->makeObject();
	}

	function moduleUninstall()
	{
		return $this->makeObject();
	}

	function checkUpdate()
	{
		return FALSE;
	}

	function moduleUpdate()
	{
		return $this->makeObject();
	}

	public function makeObject($code = 0, $message = 'success')
	{
		return class_exists('BaseObject') ? new BaseObject($code, $message) : new Object($code, $message);
	}
}

/* End of file krzip.class.php */
/* Location: ./modules/krzip/krzip.class.php */
