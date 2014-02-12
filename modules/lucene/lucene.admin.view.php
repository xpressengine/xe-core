<?php
	
	/**
	 * @class   luceneAdminView
	 * @author  NHN (developers@xpressengine.com)
	 * @brief   lucene 모듈의 admin view 클래스
	 **/
	class luceneAdminView extends lucene {
		
		var $config = null;

		/**
		 * @brief 초기화.
		 **/
		function init() {

			$oModuleModel = &getModel('module');
			$this->config = $oModuleModel->getModuleConfig('lucene');
			Context::set('config', $this->config);

			$this->setTemplatePath($this->module_path."tpl");
		}


		/**
		 * @brief 검색설정
		 **/
		function dispLuceneAdminContent() {
			// 설정 읽어오기.
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');

			// 루씬 서버 URL
			$searchUrl = $config->searchUrl;
			if(!$searchUrl) $searchUrl = "http://search.nlucene.nhncorp.com:5001/lucene_search_bloc-1.0/SearchBO/";
			Context::set('searchUrl', $searchUrl);

			// 검색모드 (통합검색 / 루씬검색)
			$uselucene = $config->uselucene;
			if(!$uselucene) $uselucene = 'false';

			Context::set('uselucene', $uselucene);
			Context::set('db_server', $config->db_server);
			Context::set('config', $this->config);
			
			$security = new Security();				
			$security->encodeHTML('uselucene');
			$security->encodeHTML('db_server');

			$this->setTemplateFile("index");
		}

		/**
		 * @brief 색인 설정
		 **/
		function dispLuceneAdminIndex() {

			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');

			$service_name_prefix = $config->service_name_prefix;
			if (!$service_name_prefix) $service_name_prefix = '';

			$renew_interval = intval($config->renew_interval);
			if (!$renew_interval) $renew_interval = '';

			$repo_path = $config->repo_path;
			if (!$repo_path) $repo_path = '';

			Context::set('service_name_prefix', $service_name_prefix);
			Context::set('renew_interval', $renew_interval);
			Context::set('repo_path', $repo_path);
			Context::set('config', $this->config);
			
			$security = new Security();				
			$security->encodeHTML('service_name_prefix');
			$security->encodeHTML('renew_interval');
			$security->encodeHTML('repo_path');
			$security->encodeHTML('config');
			
			$this->setTemplateFile("admin_index");
		}

		/**
		 * @brief 현재 색인 상태를 보여줌.
		 */
		function dispLuceneAdminIndicesStatus() {

			// 색인 설정이 이루어져 있지 않으면 설정화면으로 넘어감.
			if (!$this->config->service_name_prefix) {
				$this->dispLuceneAdminContent();
				Context::set('act', 'dispLuceneAdminContent');
				return;
			}
			Context::set('config', $this->config);

			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');

			$com_request = $config->searchUrl."com_lucene_index_bloc/BLOCConfigManageBO/getIndexStatusInfo";
			$doc_request = $config->searchUrl."doc_lucene_index_bloc/BLOCConfigManageBO/getIndexStatusInfo";

			$doc_index_name = $config->service_name_prefix . '_document';
			$com_index_name = $config->service_name_prefix . '_comment';

			$regResult_doc = FileHandler::getRemoteResource($doc_request.'?serviceName='.$doc_index_name, null, 3, "GET", null, array(), array());
			$regResult_com = FileHandler::getRemoteResource($com_request.'?serviceName='.$com_index_name, null, 3, "GET", null, array(), array());

			$regResult_doc = json_decode($regResult_doc);
			$regResult_com = json_decode($regResult_com);
			
			if ($regResult_doc) $regResult_doc->exists = 'Y';
			if ($regResult_com) $regResult_com->exists = 'Y';

			Context::set('doc_status', $regResult_doc);
			Context::set('com_status', $regResult_com);

		
			//$this->add('doc_numDocs', $regResult_doc->numDocs);
			//$this->add('doc_lastUpdated', $regResult_doc->lastUpdateDate);
			//$this->add('doc_lastUpdateDuration', $regResult_doc->lastUpdateDuration);
			//$this->add('com_numDocs', $regResult_com->numDocs);
			//$this->add('com_lastUpdated', $regResult_com->lastUpdateDate);
			//$this->add('com_lastUpdateDuration', $regResult_com->lastUpdateDuration);
			$this->setTemplateFile("indices_status");

		}

		/**
		 * @brief 권한 설정
		 */
		function dispLuceneAdminGrantInfo() {
		}
	}

?>
