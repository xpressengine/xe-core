<?php
/**
* @class  wikiAdminView
* @developer NHN (developers@xpressengine.com)
* @brief  wiki admin view class
*/
class WikiAdminView extends Wiki
{
	var $wiki_markup_list = array("markdown", "mediawiki_markup", "googlecode_markup", "xe_wiki_markup" );

	/**
	 * @brief Admin view initialisation
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function init()
	{
		// check if module_srl is already set
		$module_srl = Context::get('module_srl');
		if(!$module_srl && $this->module_srl)
		{
			$module_srl = $this->module_srl;
			Context::set('module_srl', $module_srl);
		}

		// Create Object Module model
		$oModuleModel = &getModel('module');

		// Second module_srl come over to save the module, putting the information in advance
		if($module_srl)
		{
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
			if(!$module_info)
			{
				Context::set('module_srl', '');
				$this->act = 'list';
			}
			else
			{
				ModuleModel::syncModuleToSite($module_info);
				$this->module_info = $module_info;
				$this->module_info->use_status = explode('|@|', $module_info->use_status);
				Context::set('module_info', $module_info);
			}
		}
		$module_category = $oModuleModel->getModuleCategories();
		Context::set('module_category', $module_category);

		// Initialize default markup type
		if(!$this->module_info->markup_type)
		{
			$this->module_info->markup_type = 'markdown';
		}

		// Specify template path
		$template_path = sprintf("%stpl/", $this->module_path);
		$this->setTemplatePath($template_path);

		$security = new Security();
		$security->encodeHTML('module_category..');
	}

	/**
	 * @brief List of Wiki module
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminContent()
	{
		$args->sort_index = "module_srl";
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$args->s_module_category_srl = Context::get('module_category_srl');
		$output = executeQueryArray('wiki.getWikiList', $args);

		ModuleModel::syncModuleToSite($output->data);
		// In order to write in the template context::set
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('wiki_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);
		// Specify the template file
		$this->setTemplateFile('index');

		$security = new Security();
		$security->encodeHTML('wiki_list..');
	}

	/**
	 * @brief Wiki module input screen for additional information
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminInsertWiki()
	{
		if(!in_array($this->module_info->module, array('admin', 'wiki')))
		{
			return $this->alertMessage('msg_invalid_request');
		}
		// set skin list
		$oModuleModel = &getModel('module');
		$skin_list = $oModuleModel->getSkins($this->module_path);
		Context::set('skin_list', $skin_list);
		$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
		Context::set('mskin_list', $mskin_list);

		// set layout list
		$oLayoutModel = &getModel('layout');
		$layout_list = $oLayoutModel->getLayoutList();
		Context::set('layout_list', $layout_list);
		$mobile_layout_list = $oLayoutModel->getLayoutList(0, "M");
		Context::set('mlayout_list', $mobile_layout_list);

		$wiki_markup_list = $this->wiki_markup_list;
		Context::set('wiki_markup_list', $wiki_markup_list);

		// get document status list
		$oDocumentModel = &getModel('document');
		$documentStatusList = $oDocumentModel->getStatusNameList();
		Context::set('document_status_list', $documentStatusList);

		$security = new Security();
		$security->encodeHTML('wiki_list..browser_title', 'wiki_list..mid');
		$security->encodeHTML('skin_list..title', 'mskin_list..title');
		$security->encodeHTML('layout_list..title', 'layout_list..layout');
		$security->encodeHTML('mlayout_list..title', 'mlayout_list..layout');

		// Specify the template file
		$this->setTemplateFile('wiki_insert');
	}

	/**
	 * @brief Help page for wiki markup type
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @return
	 */
	function dispWikiAdminMarkupExamples()
	{
		$wiki_markup_list = $this->wiki_markup_list;
		Context::set('wiki_markup_list', $wiki_markup_list);

		$this->setTemplateFile('markup_examples');
	}

	/**
	 * @brief Confirmation screen for deleting wiki module
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminDeleteWiki()
	{
		if(!Context::get('module_srl'))
		{
			return $this->dispWikiAdminContent();
		}
		if(!in_array($this->module_info->module, array('admin', 'wiki')))
		{
			return $this->alertMessage('msg_invalid_request');
		}
		$module_info = Context::get('module_info');
		$oDocumentModel = &getModel('document');
		$document_count = $oDocumentModel->getDocumentCount($module_info->module_srl);
		$module_info->document_count = $document_count;
		Context::set('module_info', $module_info);
		$this->setTemplateFile('wiki_delete');
	}

	/**
	 * @brief Additional setup screen for Wiki module
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminWikiAdditionSetup()
	{
		// content will be passsed by reference to all triggers
		$content = '';
		// Trigger calls for additional settings
		// trigger name to be used
		$output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'before', $content);
		$output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'after', $content);

		Context::set('setup_content', $content);
		// Setup markup type, for disabling certain fields when wiki syntax is used
		Context::set('markup_type', $this->module_info->markup_type);
		// Specify the template file
		$this->setTemplateFile('addition_setup');
	}

	/**
	 * @brief Set permissions for a wiki module screen
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminGrantInfo()
	{
		// Call the common page for managing grants information
		$oModuleAdminModel = getAdminModel('module');
		$grant_content = $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant);
		Context::set('grant_content', $grant_content);
		$this->setTemplateFile('grant_list');
	}

	/**
	 * @brief Wiki module screen skins settings
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminSkinInfo()
	{
		// Call the common page for managing skin information
		$oModuleAdminModel = & getAdminModel('module');
		$skin_content = $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl);
		Context::set('skin_content', $skin_content);
		$this->setTemplateFile('skin_info');
	}

	/**
	 * @brief Wiki module screen mobile skins settings
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminMobileSkinInfo()
	{
		// Call the common page for managing skin information
		$oModuleAdminModel = & getAdminModel('module');
		$skin_content = $oModuleAdminModel->getModuleMobileSkinHTML($this->module_info->module_srl);
		Context::set('skin_content', $skin_content);
		$this->setTemplateFile('skin_info');
	}

	/**
	 * @brief Wiki module list update screen
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function dispWikiAdminArrange()
	{
		$this->setTemplateFile('arrange_list') ;
	}
}
/* End of file wiki.admin.view.php */
/* Location: wiki.admin.view.php */
