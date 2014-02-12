<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

	/**
	 * @class  krzipAdminView
	 * @author NAVER (developers@xpressengine.com)
	 * @brief admin view class of the krzip module 
	 **/

	class krzipAdminView extends krzip {

		/**
		 * @brief Initialization
		 **/
		function init() {
		}

		/**
		 * @brief Configuration
		 **/
		function dispKrzipAdminConfig() {
			// Get configurations (using module model object)
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('krzip');
			Context::set('config',$config);
			// Set a template file
			$this->setTemplatePath($this->module_path.'tpl');
			$this->setTemplateFile('index');
		}
	}
?>
