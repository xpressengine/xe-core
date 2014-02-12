<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

	/**
	 * @class  krzipModel
	 * @author NAVER (developers@xpressengine.com)
	 * @brief model class of the krzip module
	 **/

	class krzipModel extends krzip {

		/**
		 * @brief Initialization
		 **/
		function init() {
		}

		/**
		 * @brief return zip code search template
		 * offer krzip code search template for other modules
		 **/
		function getKrzipCodeSearchHtml($column_name, $values) {
			
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('krzip');
			if($config->krzip_server_hostname) $this->hostname = $config->krzip_server_hostname;
			if($config->krzip_server_query) $this->query = $config->krzip_server_query;

			$krzip = new stdClass();
			$krzip->api_url = '//'.$this->hostname.$this->query;
			$krzip->column_name = $column_name;
			$krzip->values = $values;
			
			// load js file
			Context::loadFile(array($this->module_path.'tpl/js/krzip_search.js'), true);
			
			Context::set('krzip', $krzip);
			
			$oTemplate = &TemplateHandler::getInstance();
			return $oTemplate->compile($this->module_path.'tpl', 'krzip');
		}
		
		/**
		 * @brief Zip Code Search
		 * Request a zip code to the server with user-entered address
		 **/
		function getKrzipCodeList() {
			// Get configurations (using module model object)
			$oModuleModel = getModel('module');
			$config = $oModuleModel->getModuleConfig('krzip');
			if($config->krzip_server_hostname) $this->hostname = $config->krzip_server_hostname;
			if($config->krzip_server_query) $this->query = $config->krzip_server_query;
			// Get address(town)
			$addr = trim(Context::get('addr'));
			if(!$addr) return new Object(-1,'msg_not_exists_addr');
			// Attempt to request to the server
			$query_string = $this->query.urlencode($addr);

			$fp = @fsockopen($this->hostname, 80, $errno, $errstr);
			if(!$fp) return new Object(-1, 'msg_fail_to_socket_open');

			fputs($fp, "GET {$query_string} HTTP/1.0\r\n");
			fputs($fp, "Host: {$this->hostname}\r\n\r\n");

			$buff = '';
			while(!feof($fp)) {
				$str = fgets($fp, 1024);
				if(trim($str)=='') $start = true;
				if($start) $buff .= $str;
			}

			fclose($fp);

			$address_list = unserialize(base64_decode($buff));
			if(!$address_list) return new Object(-1, 'msg_no_result');

			$this->add('address_list', implode("\n",$address_list)."\n");
		}
	}
?>
