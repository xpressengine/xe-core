<?php
/* Copyright (C) XEHub <https://www.xehub.io> */

/**
 * @class  communication 
 * @author XEHub (developers@xpressengine.com)
 * communication module of the high class
 */
class communication extends ModuleObject
{
	/** Sent copies belong to the sender; all other copies belong to the receiver. */
	public static function isMessageOwner($message, $member_srl)
	{
		if(!is_object($message) || !is_scalar($member_srl) || !preg_match('/^[1-9][0-9]*$/D', (string)$member_srl)) return false;
		$owner = $message->message_type === 'S' ? $message->sender_srl : $message->receiver_srl;
		// String comparison also works for large sequence IDs on 32-bit PHP.
		return is_scalar($owner) && (string)$owner === (string)$member_srl;
	}

	/**
	 * Implement if additional tasks are necessary when installing
	 * @return BaseObject
	 */
	function moduleInstall()
	{
		// Create a temporary file storage for one new private message notification
		FileHandler::makeDir('./files/member_extra_info/new_message_flags');
		return new BaseObject();
	}

	/**
	 * method to check if successfully installed.
	 * @return boolean true : need to update false : don't need to update
	 */
	function checkUpdate()
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
					$template_path = sprintf('./themes/%s/modules/communication/', $config_parse[0]);
					if(is_dir($template_path))
					{
						return TRUE;
					}
				}
			}

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		if(!is_dir("./files/member_extra_info/new_message_flags"))
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Update
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
			if(!is_object($config))
			{
				$config = new stdClass();
			}

			if($config->skin)
			{
				$config_parse = explode('.', $config->skin);
				if(count($config_parse) > 1)
				{
					$template_path = sprintf('./themes/%s/modules/communication/', $config_parse[0]);
					if(is_dir($template_path))
					{
						$config->skin = implode('|@|', $config_parse);
						$oModuleController = getController('module');
						$oModuleController->updateModuleConfig('communication', $config);
					}
				}
			}

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		if(!is_dir("./files/member_extra_info/new_message_flags"))
		{
			FileHandler::makeDir('./files/member_extra_info/new_message_flags');
		}

		return new BaseObject(0, 'success_updated');
	}

	/**
	 * Re-generate the cache file
	 * @return void
	 */
	function recompileCache()
	{
		
	}

}
/* End of file communication.class.php */
/* Location: ./modules/comment/communication.class.php */
