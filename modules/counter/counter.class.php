<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * High class of counter module
 *
 * @author NAVER (developers@xpressengine.com)
 */
class counter extends ModuleObject
{

	/**
	 * Implement if additional tasks are necessary when installing
	 * @return BaseObject
	 */
	function moduleInstall()
	{
		$oCounterController = getController('counter');

		// add a row for the total visit history 
		//$oCounterController->insertTotalStatus();

		// add a row for today's status
		//$oCounterController->insertTodayStatus();

		return new BaseObject();
	}

	/**
	 * method if successfully installed
	 *
	 * @return bool
	 */
	function checkUpdate()
	{
		// Add site_srl to the counter
		$oDB = DB::getInstance();
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			if(!$oDB->isColumnExists('counter_log', 'site_srl'))
			{
				return TRUE;
			}

			if(!$oDB->isIndexExists('counter_log', 'idx_site_counter_log'))
			{
				return TRUE;
			}

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return FALSE;
	}

	/**
	 * Module update
	 *
	 * @return BaseObject
	 */
	function moduleUpdate()
	{
		// Add site_srl to the counter
		$oDB = DB::getInstance();

		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			if(!$oDB->isColumnExists('counter_log', 'site_srl'))
			{
				$oDB->addColumn('counter_log', 'site_srl', 'number', 11, 0, TRUE);
			}

			if(!$oDB->isIndexExists('counter_log', 'idx_site_counter_log'))
			{
				$oDB->addIndex('counter_log', 'idx_site_counter_log', array('site_srl', 'ipaddress'), FALSE);
			}

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return new BaseObject(0, 'success_updated');
	}

	/**
	 * re-generate the cache file
	 *
	 * @return BaseObject
	 */
	function recompileCache()
	{
		
	}

}
/* End of file counter.class.php */
/* Location: ./modules/counter/counter.class.php */
