<?php
class seoView extends seo
{
	function triggerDispSeoAdditionSetup(&$obj)
	{
		$current_module_srl = Context::get('module_srl');
		$current_module_srls = Context::get('module_srls');

		$oModuleModel = getModel('module');
		$seo_module_part_config = $oModuleModel->getModulePartConfig('seo', $current_module_srl);
		Context::set('seo_module_part_config', $seo_module_part_config);

		$oTemplate = &TemplateHandler::getInstance();
		$tpl = $oTemplate->compile($this->module_path.'tpl', 'module_part_config');
		$obj .= $tpl;

		return new BaseObject();
	}
}
/* !End of file */
