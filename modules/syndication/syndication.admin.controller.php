<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  syndicationAdminController
 * @author NAVER (developers@xpressengine.com)
 * @brief syndication module admin Controller class
 **/

class syndicationAdminController extends syndication {

	function init() 
	{
	}

	function procSyndicationAdminInsertConfig() 
	{
		$oModuleController = getController('module');
		$oSyndicationController = getController('syndication');
		$oSyndicationModel = getModel('syndication');

		$config = new stdClass;
		$config->syndication_use = Context::get('syndication_use');
		$config->site_url = preg_replace('/\/+$/is','',Context::get('site_url'));
		$config->year = Context::get('year');
		$config->syndication_token = Context::get('syndication_token');
		$config->syndication_password = urlencode(Context::get('syndication_password'));

		if(!$config->site_url) return $this->makeObject(-1,'msg_site_url_is_null');
		if(!$config->syndication_token) return $this->makeObject(-1,'msg_syndication_token_is_null');

		$oModuleController->updateModuleConfig('syndication',$config);

		$except_module = Context::get('except_module');
		$output = executeQuery('syndication.deleteExceptModules');
		if(!$output->toBool()) return $output;

		if ($except_module){
			$modules = explode(',',$except_module);
			for($i=0,$c=count($modules);$i<$c;$i++) {
				$args->module_srl = $modules[$i];
				$output = executeQuery('syndication.insertExceptModule',$args);
				if(!$output->toBool()) return $output;
			}
		}

		if(!$this->checkOpenSSLSupport())
		{
			return $this->makeObject(-1, 'msg_need_openssl_support');
		}

		$this->setMessage('success_applied');
		if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
			$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'admin', 'act', 'dispSyndicationAdminConfig');
			$this->setRedirectUrl($returnUrl);
			return;
		}
	}

	function procSyndicationAdminCheckPingResult()
	{
		$oModuleModel = getModel('module');

		$oSyndicationController = getController('syndication');
		$oSyndicationModel= getModel('syndication');

		$module_config = $oModuleModel->getModuleConfig('syndication');

		$site_url = trim(Context::get('site_url'));
		if(!$module_config->site_url) return $this->makeObject(-1,'msg_site_url_is_null');
		if(!$module_config->syndication_token) return $this->makeObject(-1,'msg_syndication_token_is_null');

		$id = $oSyndicationModel->getID('site');

		// site_url 정보와 token 정보를 이용해서 ping 전송 테스트
		if($oSyndicationController->ping($id, 'site')===FALSE)
		{
			$this->setError(-1);
			$this->setMessage($oSyndicationController->ping_message);
		}
		else
		{
			$this->setMessage('msg_success_ping_test');
		}
	}
}
