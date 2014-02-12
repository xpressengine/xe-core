<?php
	/**
	 * @class   luceneAdminController
	 * @author  NHN (developers@xpressengine.com) 
	 * @brief   lucene 모듈의 admin controller 클래스
	 **/
	require_once(_XE_PATH_.'modules/lucene/lucene.lib.php');

	class luceneAdminController extends lucene {
	
		/**
		 * @brief 설정을 저장하고 lucene모듈과 통합검색 모듈 사이에 actionForward 를 교체
		 */
		function procLuceneAdminInsertConfig() {
			// 설정 저장
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');

			$config->uselucene = Context::get('uselucene');
			$config->db_server = Context::get('db_server');
			$search_url = Context::get('search_url');
			
			if(!(substr($search_url, strlen($search_url) - strlen("/")) == "/")) {
				$config->searchUrl = $search_url."/";
			} else { 
				$config->searchUrl = $search_url;
			}

			// 통합검색과 루씬 검색 전환을 위한 actionFoward 처리
			// 현재 XE core 에 deleteActionForward.xml 이 누락되어 있음.
			// 포함되면 주석처리 된 부분을 교체할 것.
			$oModuleController = &getController('module');
			if (!strcmp($config->uselucene, 'true')) {
				$this->deleteActionForward('integration_search', 'view', 'IS');
				$oModuleController->insertActionForward('lucene', 'view', 'IS');
				//$oModuleController->deleteActionForward('integration_search', 'view', 'IS');
				//$oModuleController->insertActionForward('lucene', 'view', 'IS');
			} else {
				$this->deleteActionForward('lucene', 'view', 'IS');
				$oModuleController->insertActionForward('integration_search', 'view', 'IS');
				// $oModuleController->deleteActionForward('lucene', 'view', 'IS');
				// $oModuleController->insertActionForward('integration_search', 'view', 'IS');
			}
			if(!$config->target_module_srl) $config->target_module_srl = '';
			$args->skin_vars = $config->skin_vars;

			$oModuleController = &getController('module');	
			$output = $oModuleController->insertModuleConfig('lucene',$config);

			return $output;	
		}


		/**
		 * @brief nLucene 검색 서비스를 설정하고 설정 정보를 모듈 설정에 저장
		 **/
		function procLuceneAdminInsertServiceConfig() {
			$repo_path = Context::get('repo_path');
			$renew_interval = Context::get('renew_interval');
			$service_name_prefix = Context::get('service_name_prefix');

			return $this->_procLuceneAdminInsertServiceConfig($repo_path, $renew_interval, $service_name_prefix);
		}

		function _procLuceneAdminInsertServiceConfig($repo_path, $renew_interval, $service_name_prefix) {
			
			// 설정 읽기
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');
			$searchUrl = $config->searchUrl;


			// 댓글/글 색인등록 정보 구성.
			$args->dbinfo = Context::getDBInfo(); 
			$args->repo_path = $repo_path;
			$args->renew_interval = $renew_interval;
			$args->service_name_prefix = $service_name_prefix;

			$oComReq = new luceneCommentIndexRequest($args);
			$oDocReq = new luceneDocumentIndexRequest($args);

			$encoded_doc_reg = $oDocReq->getRequest();
			$encoded_com_reg = $oComReq->getRequest();


			// 색인 서비스 등록 요청
			$updateAPI_doc = $searchUrl."doc_lucene_index_bloc/BLOCConfigManageBO/updateConfig";
			$updateAPI_com = $searchUrl."com_lucene_index_bloc/BLOCConfigManageBO/updateConfig";

			$regResult_doc = FileHandler::getRemoteResource($updateAPI_doc.'?detailMap='.$encoded_doc_reg, null, 3, "GET", null, array(), array());
			$regResult_com = FileHandler::getRemoteResource($updateAPI_com.'?detailMap='.$encoded_com_reg, null, 3, "GET", null, array(), array());

			// 검색 서비스 등록 요청
			$search_reg->newRepo = $args->repo_path;
			$encoded_search_reg = urlencode(json_encode2($search_reg));
			$searchUpdateAPI = $searchUrl."lucene_search_bloc/SearchConfigManageBO/updateRepo";
			//$searchUpdateAPI = $searchUrl."lucene_search_bloc-1.1/SearchConfigManageBO/updateRepo";

			$regResult_search = FileHandler::getRemoteResource($searchUpdateAPI.'?params='.$encoded_search_reg, null, 3, "GET", null, array(), array());
			
			// 모듈 설정 저장	
			$config->service_name_prefix = $args->service_name_prefix;
			$config->repo_path = $args->repo_path;
			$config->renew_interval = $args->renew_interval;

			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig('lucene',$config);
			
			return $output;	
		}


		// 현재 XE 배포본에 deleteActionForward.xml 이 누락되어 있음.
		// 추가되면 삭제할 것.
		function deleteActionForward($module, $type, $act) {
			$args->module = $module;
			$args->type = $type;
			$args->act = $act;

			$output = executeQuery('lucene.deleteActionForward', $args);
			return $output;
		}
	}
?>
