<?php
class moumiAdminView extends moumi
{
	function init()
	{
		$tpl_file = str_replace('dispMoumi', '', $this->act);
		$tpl_path = sprintf('%stpl/', $this->module_path);

		$this->setTemplatePath($tpl_path);
		$this->setTemplateFile($tpl_file);
	}

	function dispMoumiAdminIndex()
	{
		$oModel = &getModel('moumi');
		$output = $oModel->getPackageList();
		if(!$output->toBool()) return $output;

		$package_list = $output->get('package_list');
		Context::set('package_list', $package_list);
	}

	function dispMoumiAdminInsertPackage()
	{
		$package_srl = Context::get('package_srl');
		if($package_srl)
		{
			$oModel = &getModel('moumi');
			$output = $oModel->getPackage($package_srl);
			if (!$output->toBool()) return $output;
			$package_info = $output->get('package_info');
			Context::set('package_info', $package_info);
		}
	}
}
