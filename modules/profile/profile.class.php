<?php
class profile extends ModuleObject
{
	private static $config;
	private $triggers = array(
		array('member.getMemberMenu',    'profile', 'controller', 'triggerMemberMenu',          'after'),
		array('comment.insertComment',   'profile', 'controller', 'triggerAfterInsertComment',  'after'),
		array('comment.deleteComment',   'profile', 'controller', 'triggerAfterDeleteComment',  'after'),
		array('document.insertDocument', 'profile', 'controller', 'triggerAfterInsertDocument', 'after'),
		array('document.deleteDocument', 'profile', 'controller', 'triggerAfterDeleteDocument', 'after'),
	);

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

		foreach($this->triggers as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4])) return TRUE;
		}

		return false;
	}

	function moduleUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		foreach($this->triggers as $trigger)
		{
			if(!$oModuleModel->getTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]))
			{
				$oModuleController->insertTrigger($trigger[0], $trigger[1], $trigger[2], $trigger[3], $trigger[4]);
			}
		}

		return new Object(0, 'success_updated');
	}

	function moduleUninstall()
	{
		return new Object();
	}
}
