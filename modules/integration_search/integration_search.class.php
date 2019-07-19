<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * The view class of the integration_search module
 *
 * @author XEHub (developers@xpressengine.com)
 */
class integration_search extends ModuleObject
{
	/**
	 * Implement if additional tasks are necessary when installing
	 *
	 * @return BaseObject
	 */
	function moduleInstall()
	{
		// Registered in action forward
		$oModuleController = getController('module');
		$oModuleController->insertActionForward('integration_search', 'view', 'IS');

		return new BaseObject();
	}

	/**
	 * Check methoda whether successfully installed
	 *
	 * @return bool
	 */
	function checkUpdate() 
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			$config = $oModuleModel->getModuleConfig('integration_search');

			if($config->skin)
			{
				$config_parse = explode('.', $config->skin);
				if(count($config_parse) > 1)
				{
					$template_path = sprintf('./themes/%s/modules/integration_search/', $config_parse[0]);
					if(is_dir($template_path)) return true;
				}
			}

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return false;
	}

	/**
	 * Execute update
	 *
	 * @return BaseObject
	 */
	function moduleUpdate() 
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			$config = $oModuleModel->getModuleConfig('message');

			if($config->skin)
			{
				$config_parse = explode('.', $config->skin);
				if(count($config_parse) > 1)
				{
					$template_path = sprintf('./themes/%s/modules/integration_search/', $config_parse[0]);
					if(is_dir($template_path))
					{
						$config->skin = implode('|@|', $config_parse);
						$oModuleController = getController('module');
						$oModuleController->updateModuleConfig('integration_search', $config);
					}
				}
			}

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return new BaseObject(0, 'success_updated');
	}

	/**
	 * Re-generate the cache file
	 *
	 * @return void
	 */
	function recompileCache()
	{
	}
}
/* End of file integration_search.class.php */
/* Location: ./modules/integration_search/integration_search.class.php */
