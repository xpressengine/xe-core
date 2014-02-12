<?php
/**
 * @author XE Magazine <info@xemagazine.com>
 * @link http://xemagazine.com/
 **/
class ncenterAdminController extends ncenter
{
	function procNcenterAdminInsertConfig()
	{
		$oModuleController = &getController('module');

		$config = new stdClass;
		$config->use = Context::get('use');

		$config->mention_format = Context::get('mention_format');
		$config->document_notify = Context::get('document_notify');
		$config->message_notify = Context::get('message_notify');
		$config->block_kin_modify = Context::get('block_kin_modify');
		$config->hide_module_srls = Context::get('hide_module_srls');

		$config->skin = Context::get('skin');
		$config->colorset = Context::get('colorset');
		$config->zindex = Context::get('zindex');
		if(!$config->document_notify) $config->document_notify = 'all-comment';

		$this->setMessage('success_updated');

		if(version_compare(__ZBXE_VERSION__, '1.5.0', '>='))
		{
			$oModuleController->updateModuleConfig('ncenter', $config);

			if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON')))
			{
				$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispNcenterAdminConfig');
				header('location: ' . $returnUrl);
				return;
			}
		}
		else
		{
			$oModuleController->insertModuleConfig('ncenter', $config);
		}
	}
}
