<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

	/**
	 * @class  krzip
	 * @author NAVER (developers@xpressengine.com)
	 * @brief Super class of krzip, which is a zip code search module
	 **/

	class krzip extends ModuleObject {

		var $hostname = 'krzip.xpressengine.com';
		var $query = '/server.php';
		
		/**
		 * @brief Implement if additional tasks are necessary when installing
		 **/
		function moduleInstall() {
			return new Object();
		}

		/**
		 * @brief a method to check if successfully installed
		 **/
		function checkUpdate() {
			
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('krzip');
			if($config->krzip_server_hostname == 'kr.zip.zeroboard.com') return true;
			
			return false;
		}

		/**
		 * @brief Execute update
		 **/
		function moduleUpdate() {
			
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('krzip');
			if($config->krzip_server_hostname == 'kr.zip.zeroboard.com')
			{
				$config->krzip_server_hostname = $this->hostname;
				$config->krzip_server_query = $this->query;
				
				// Insert by creating the module Controller object
				$oModuleController = getController('module');
				$output = $oModuleController->insertModuleConfig('krzip',$config);
			}
			
			return new Object(0, 'success_updated');
		}

		/**
		 * @brief Re-generate the cache file
		 **/
		function recompileCache() {
		}
	}
?>
