<?php
/**
 * @author XE Magazine <info@xemagazine.com>
 * @link http://xemagazine.com/
 **/
class ncenterAdminView extends ncenter
{
	function init()
	{
		$this->setTemplatePath($this->module_path.'tpl');

		if(version_compare(__ZBXE_VERSION__, '1.7.0', '>='))
		{
			$this->setTemplateFile(str_replace('dispNcenterAdmin', '', $this->act));
		}
		else if(version_compare(__ZBXE_VERSION__, '1.5.0', '>='))
		{
			$this->setTemplateFile(str_replace('dispNcenterAdmin', '', $this->act) . '.1.5');
		}
		else
		{
			$this->setTemplateFile(str_replace('dispNcenterAdmin', '', $this->act) . '.1.4');
		}
	}

	function dispNcenterAdminConfig()
	{
		$oModuleModel = &getModel('module');
		$oNcenterModel = &getModel('ncenter');
		$config = $oNcenterModel->getConfig();
		Context::set('config', $config);

		$skin_list = $oModuleModel->getSkins($this->module_path);
		Context::set('skin_list', $skin_list);

		if(!$skin_list[$config->skin]) $config->skin = 'default';
		Context::set('colorset_list', $skin_list[$config->skin]->colorset);

		if(version_compare(__ZBXE_VERSION__, '1.5.0', '>='))
		{
			$security = new Security();
			$security->encodeHTML('config..');
			$security->encodeHTML('skin_list..title');
			$security->encodeHTML('colorset_list..name','colorset_list..title');
		}

		$mid_list = $oModuleModel->getMidList(null, array('module_srl', 'mid', 'browser_title', 'module'));

		Context::set('mid_list', $mid_list);
	}
}
