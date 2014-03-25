<?php
class profile extends ModuleObject
{
	private static $config;

	public function getModuleConfig()
	{
		return $this->$config;
	}


	function moduleInstall()
	{
		return new Object();
	}

	function checkUpdate()
	{
		$oModuleModel = getModel('module');
		if(!$oModuleModel->getTrigger('member.getMemberMenu', 'profile', 'controller', 'triggerMemberMenu', 'after')) return true;

		return false;
	}

	function moduleUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		if(!$oModuleModel->getTrigger('member.getMemberMenu', 'profile', 'controller', 'triggerMemberMenu', 'after')) {
			$oModuleController->insertTrigger('member.getMemberMenu', 'profile', 'controller', 'triggerMemberMenu', 'after');
		}

		return new Object(0, 'success_updated');
	}

	function moduleUninstall()
	{
		return new Object();
	}
}
