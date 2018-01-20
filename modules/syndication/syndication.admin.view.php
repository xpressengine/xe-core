<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  syndicationAdminView
 * @author NAVER (developers@xpressengine.com)
 * @brief  syndication admin view class
 **/
class syndicationAdminView extends syndication
{
	function init() 
	{
	}

	public function dispSyndicationAdminConfig()
	{
		$oModuleModel = getModel('module');

		$module_config = $oModuleModel->getModuleConfig('syndication');

		$oSyndicationModel = getModel('syndication');
		Context::set('ping_log', $oSyndicationModel->getResentPingLog());

		if(!$module_config->syndication_use)
		{
			$module_config->syndication_use = 'Y';
		}

		if(!$module_config->site_url)
		{
			$module_config->site_url = Context::getDefaultUrl()?Context::getDefaultUrl():getFullUrl();
		}

		if(!$module_config->year)
		{
			$module_config->year = date("Y");
		}

		if(!isset($module_config->syndication_password))
		{
			$module_config->syndication_password = uniqid();
		}

		Context::set('syndication_use', $module_config->syndication_use);
		Context::set('site_url', preg_replace('/^(http|https):\/\//i','',$module_config->site_url));
		Context::set('year', $module_config->year);
		Context::set('syndication_token', $module_config->syndication_token);
		Context::set('syndication_password', $module_config->syndication_password);
		Context::set('uri_scheme', (Context::getSslStatus() == 'always') ? 'https://' : 'http://');

		$output = executeQueryArray('syndication.getExceptModules');
		$except_module_list = array();
		if($output->data && count($output->data) > 0)
		{
			foreach($output->data as $item)
			{
				$except_module_list[] = $item;
			}
		}
		Context::set('except_module', $except_module_list);
		

		//Security
		$security = new Security();
		$security->encodeHTML('services..service','except_module..ping');
		$security->encodeHTML('except_module..mid','except_module..browser_title');

		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('config');
	}

}
