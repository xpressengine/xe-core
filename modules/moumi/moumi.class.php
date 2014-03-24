<?php
class moumi extends ModuleObject
{
	public $allow_ip = array(
		'127.0.0.1',
		'14.52.92.207', // xehub
		'10.3.41.241',	// web004
		'125.209.193.160',	// moni001
		'10.3.41.117',	// moni001 
	);

	function isAllowed()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		if(in_array($ip, $this->allow_ip)) return TRUE;

		return FALSE;
	}

	function moduleInstall()
	{
		return new Object();
	}

	function checkUpdate()
	{
		return false;
	}

	function moduleUpdate()
	{
		return new Object(0, 'success_updated');
	}

	function moduleUninstall()
	{
		return new Object();
	}

	function recomileCache()
	{
	}
}
