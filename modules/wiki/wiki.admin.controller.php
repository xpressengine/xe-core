<?php
/**
 * @class  wikiAdminController
 * @developer NHN (developers@xpressengine.com)
 * @brief  wiki admin controller class
 */
class WikiAdminController extends Wiki
{

	/**
	 * @brief Add a wiki module
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @param $args
	 * @return
	 */
	function procWikiAdminInsertWiki($args = NULL)
	{
		$oModuleController = &getController('module');
		$oModuleModel = &getModel('module');

		$args = Context::getRequestVars();
		$args->module = 'wiki';
		if($args->use_comment != 'N')
		{
			$args->use_comment = 'Y';
		}
		if($args->use_comment_validation != 'N')
		{
			$args->use_comment_validation = 'Y';
		}
		if(is_array($args->use_status))
		{
			$args->use_status = implode('|@|', $args->use_status);
		}

		if($args->module_srl)
		{
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
			if($module_info->module_srl != $args->module_srl)
			{
				unset($args->module_srl);
			}
		}
		if(!$args->module_srl)
		{
			$output = $oModuleController->insertModule($args);
			$msg_code = 'success_registed';

			if($output->toBool())
			{
				// If insert was succesful, enable document history
				$document_config->use_history = 'Y';
				$oModuleController = &getController('module');
				$oModuleController->insertModulePartConfig('document', $output->get('module_srl'), $document_config);
			}
		}
		else
		{
			$output = $oModuleController->updateModule($args);
			$msg_code = 'success_updated';
		}
		if(!$output->toBool())
		{
			return $output;
		}

		$this->add('page', Context::get('page'));
		$this->add('module_srl', $output->get('module_srl'));
		$this->setMessage($msg_code);

		$returnUrl = Context::get('success_return_url');
		$this->setRedirectUrl($returnUrl);
	}

	/**
	 * @brief Deleting a wiki module
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function procWikiAdminDeleteWiki()
	{
		$module_srl = Context::get('module_srl');
		$oModuleController = &getController('module');
		$output = $oModuleController->deleteModule($module_srl);
		if(!$output->toBool())
		{
			return $output;
		}

		$this->add('module', 'wiki');
		$this->add('page', Context::get('page'));
		$this->setMessage('success_deleted');

		$returnUrl = Context::get('success_return_url');
		$this->setRedirectUrl($returnUrl);
	}

	/**
	 * @brief Adds alias to documents which are missing it
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function procWikiAdminArrangeList()
	{
		$oModuleModel = &getModel('module');
		$oDocumentController = &getController('document');

		// Verification target Wiki
		$module_srl = Context::get('module_srl');
		$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
		if(!$module_info->module_srl || $module_info->module != 'wiki')
		{
			return new Object(-1, 'msg_invalid_request');
		}
		// Wiki article of the target entry has no value extraction
		$args->module_srl = $module_srl;
		$output = executeQueryArray('wiki.getDocumentWithoutAlias', $args);

		if(!$output->toBool() || !$output->data)
		{
			return new Object();
		}

		foreach($output->data as $key => $val)
		{
			if($val->alias_srl)
			{
				continue;
			}

			$val->alias_title = wiki::beautifyEntryName($val->alias_title);
			$result = $oDocumentController->insertAlias($module_srl, $val->document_srl, $val->alias_title);
			if(!$result->toBool())
			{
				$oDocumentController->insertAlias($module_srl, $val->document_srl, $val->alias_title . '_' . $val->document_srl);
			}
		}
	}
}
/* End of file wiki.admin.controller.php */
/* Location: wiki.admin.controller.php */
