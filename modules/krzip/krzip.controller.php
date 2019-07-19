<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * @class  krzipController
 * @author XEHub (developers@xpressengine.com)
 * @brief  Krzip module controller class.
 */

class krzipController extends krzip
{
	function updateConfig($args)
	{
		if(!$args || !is_object($args))
		{
			$args = new stdClass();
		}

		$oModuleController = getController('module');
		$output = $oModuleController->updateModuleConfig('krzip', $args);
		if($output->toBool())
		{
			unset($this->module_config);
		}

		return $output;
	}
}

/* End of file krzip.controller.php */
/* Location: ./modules/krzip/krzip.controller.php */
