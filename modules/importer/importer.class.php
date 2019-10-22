<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * importer
 * high class of importer module
 *
 * @author XEHub (developers@xpressengine.com)
 * @package /modules/importer
 * @version 0.1
 */
class importer extends ModuleObject
{
	/**
	 * Implement if additional tasks are necessary when installing
	 * @return BaseObject
	 */
	function moduleInstall()
	{
		return new BaseObject();
	}

	/**
	 * A method to check if successfully installed
	 * @return bool
	 */
	function checkUpdate()
	{
		return false;
	}

	/**
	 * Execute update
	 * @return BaseObject
	 */
	function moduleUpdate()
	{
		return new BaseObject();
	}

	/**
	 * Re-generate the cache file
	 * @return void
	 */
	function recompileCache()
	{
	}
}
/* End of file importer.class.php */
/* Location: ./modules/importer/importer.class.php */
