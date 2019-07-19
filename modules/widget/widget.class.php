<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * @class  widget
 * @author XEHub (developers@xpressengine.com)
 * @brief widget module's high class
 */
class widget extends ModuleObject
{
	/**
	 * @brief Implement if additional tasks are necessary when installing
	 */
	function moduleInstall()
	{
		// Create cache directory used by widget
		FileHandler::makeDir('./files/cache/widget');
		FileHandler::makeDir('./files/cache/widget_cache');
		// Add this widget compile the trigger for the display.after
		$oModuleController = getController('module');
		$oModuleController->insertTrigger('display', 'widget', 'controller', 'triggerWidgetCompile', 'before');

		return new BaseObject();
	}

	/**
	 * @brief a method to check if successfully installed
	 */
	function checkUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			// widget compile display.after trigger for further (04/14/2009)
			if(!$oModuleModel->getTrigger('display', 'widget', 'controller', 'triggerWidgetCompile', 'before')) return true;

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return false;
	}

	/**
	 * @brief Execute update
	 */
	function moduleUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			// widget compile display.after trigger for further (04/14/2009)
			if(!$oModuleModel->getTrigger('display', 'widget', 'controller', 'triggerWidgetCompile', 'before'))
			{
				$oModuleController->insertTrigger('display', 'widget', 'controller', 'triggerWidgetCompile', 'before');
			}

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return new BaseObject(0, 'success_updated');
	}

	/**
	 * @brief Re-generate the cache file
	 */
	function recompileCache()
	{
	}
}
/* End of file widget.class.php */
/* Location: ./modules/widget/widget.class.php */
