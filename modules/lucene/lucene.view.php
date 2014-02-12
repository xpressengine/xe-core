<?php
	
	/**
	 * @class   luceneView
	 * @author  NHN (developers@xpressengine.com)
	 * @brief   lucene 모듈의 view 클래스
	 */
	
	require_once(_XE_PATH_.'modules/lucene/lib/jsonphp.php');

	class luceneView extends lucene {

		var $json_service = null;

		/**
		 * @brief 초기화 작업.
		 */
		function init() {
			$this->json_service = new Services_JSON(0);
		}

		/**
		 * @brief 글/댓글에 대해서는 nLucene 검색, 그 외 요소에 대해서는 통합검색을 이용.
		 */
		function IS() {
			$searchAPI = "lucene_search_bloc/SearchBO/";

			// 모듈 설정 읽어오기
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');
			$ISconfig = $oModuleModel->getModuleConfig('integration_search');
						
			// 검색 API 설정
			$searchUrl = $config->searchUrl.$searchAPI;

			// 통합검색 모듈과 공유하는 설정
			if (!$ISconfig->skin) $ISconfig->skin = 'default';
			Context::set('module_info', unserialize($ISconfig->skin_vars));
			// 템플릿 경로 설정
			$oISModule = &getModule('integration_search');
			$this->setTemplatePath($this->module_path."/skins/".$ISconfig->skin."/");
			
			// 검색대상 모듈 설정 읽어오기
			$target_mid = $ISconfig->target_module_srl;
			// 검색대상 모듈 포함/제외 여부 설정 읽어오기
			$target_mode = $ISconfig->target;
		
			// 검색어 읽어오기
			$is_keyword = Context::get('is_keyword');
			// 페이지 읽어오기
			$page = (int) Context::get('page');
			if (!$page) $page = 1;
			// 검색범위 읽어오기 
			$where = Context::get('where');
			// 검색대상 읽어오기
			$search_target = Context::get('search_target');
			if ($search_target == 'tag') $search_target = 'tags';
			if(!$this->isFieldCorrect($search_target)) $search_target = 'title_content';

			// 검색대상 모듈을 적용한 질의어
			$query = $this->getSubquery($target_mid, $target_mode); 

			// 파라미터 설정
			$json_obj->query = $this->getQuery($is_keyword, $search_target);
			$json_obj->curPage = $page;
			$json_obj->numPerPage = 30;
			$json_obj->indexType = "db";
			$json_obj->fieldName = $search_target;
			$json_obj->target_mid = $target_mid;
			$json_obj->target_mode = $target_mode;

			$json_obj->subquery = $query;

			// 검색어가 있으면
			if ($is_keyword) {
				$oIS = &getModel('integration_search');
				// 설정된 검색 범위에 따라
				switch ($where) {
					case 'document':
						$output = $this->getDocuments($searchUrl, $json_obj, $config->service_name_prefix);
						Context::set('output', $output);
						$this->setTemplateFile("document", $page);
						break;

					case 'comment':
						$output = $this->getComments($searchUrl, $json_obj, $config->service_name_prefix);
						Context::set('output', $output);
						$this->setTemplateFile("comment", $page);
						break;

					// 트랙백, 파일 등은 지원할 때까지 제거.
					/*case 'trackback':
						if (!in_array($search_target, array('title', 'url', 'blog_name', 'excerpt'))) $search_title = 'title';
						Context::set('search_target', $search_target);
						
						$output = $oIS->getTrackbacks('include', $module_srl_list, $search_target, $is_keyword, $page, 10);
						$output = $oIS->getTrackbacks('include', array(), $search_target, $is_keyword, $page, 10);
						Context::set('output', $output);
						$this->setTemplateFile("trackback", $page);
						break;

					case 'multimedia':
						$output = $oIS->getImages($target, array(), $is_keyword, $page, 20);
						Context::set('output', $output);
						$this->setTemplateFile("multimedia", $page);
						break;

					case 'file':
						$output = $oIS->getFiles($target, array(), $is_keyword, $page, 20);
						Context::set('output', $output);
						$this->setTemplateFile("file", $page);
						break;*/

					default:
						$output['document'] = $this->getDocuments($searchUrl, $json_obj, $config->service_name_prefix);
						$output['comment'] = $this->getComments($searchUrl, $json_obj, $config->service_name_prefix);
						//$output['trackback'] = array();
						//$output['multimedia'] = array();
						//$output['file'] = array();
						
						Context::set('search_result', $output);
						$this->setTemplateFile("index", $page);
						break;

				}
			} else {
				$this->setTemplateFile("no_keywords");
			}
		}


		/**
		 * @brief 글의 id 목록으로 글을 가져옴.
		 */
		function getDocuments($searchUrl, $params, $service_prefix) {
			$oModelDocument = &getModel('document');
			
			$params->serviceName = $service_prefix.'_document';
			$params->query = '('.$params->query.')'.$params->subquery;
			$params->displayFields = array("id");

			$encodedParams = $this->json_service->encode($params);
			$searchResult = FileHandler::getRemoteResource($searchUrl."searchByMap", $encodedParams, 3, "POST", "application/json; charset=UTF-8", array(), array());

			// 결과가 유효한지 확인
			if (!$searchResult && $searchResult != "null") {
				$idList = array();
			} else {
				$idList = $this->result2idArray($searchResult);
			}

			// 결과가 1개 이상이어야 글 본문을 요청함.
			$documents = array();
			if (count($idList) > 0) {
				$tmpDocuments = $oModelDocument->getDocuments($idList, false, false);

				// 받아온 문서 목록을 루씬에서 반환한 순서대로 재배열
				$diff = 0;
				foreach($idList as $id) {
					if ($tmpDocuments[$id]) {
						$documents['doc'.$id] = $tmpDocuments[$id];
					} else {
						$diff++;
					}
				}
			}

			$searchResult = $this->json_service->decode($searchResult);
			$page_navigation = new PageHandler($searchResult->totalSize, ($searchResult->totalSize) / 30+1, $params->curPage, 30);

			$output->total_count = $searchResult->totalSize - $diff;
			$output->data = $documents;
			$output->page_navigation = $page_navigation;
		
			return $output;
		}

		/**
		 * @brief 댓글의 id목록으로 댓글을 가져옴
		 */
		function getComments($searchUrl, $params, $service_prefix) {

			$params->serviceName = $service_prefix.'_comment';
			$params->fieldName = 'content';
			$params->displayFields = array('id');
			$params->query = $params->query.$params->subquery;


			$oModelComment = &getModel('comment');
			$encodedParams = $this->json_service->encode($params);

			$searchResult = FileHandler::getRemoteResource($searchUrl."searchByMap", $encodedParams, 3, "POST", "application/json; charset=UTF-8", array(), array());
	
			if(!$searchResult && $searchResult != "null") {
				$idList = array();
			} else {
				$idList = $this->result2idArray($searchResult);
			}

			$comments = array();
			if (count($idList) > 0) {
				$tmpComments = $oModelComment->getComments($idList);

				$diff = 0;
				foreach($idList as $id) {
					if ($tmpComments[$id]) {
						$comments['com'.$id] = $tmpComments[$id];
					} else {
						$diff++;
					}
				}
			}

			$searchResult = $this->json_service->decode($searchResult);
			$page_navigation = new PageHandler($searchResult->totalSize, ($searchResult->totalSize) / 30+1, $params->curPage, 10);

			$output->total_count = $searchResult->totalSize - $diff;
			$output->data = $comments;
			$output->page_navigation = $page_navigation;
		
			return $output;
		}
		
		/**
		 * @brief 검색 결과에서 id의 배열을 추출
		 */
		function result2idArray($res) {
			$res = $this->json_service->decode($res);
			$resSet = $res->results;
			$answer = array();
			if (count($resSet) > 0) {
				foreach ($resSet as $aMap) {
					$answer[] = $aMap->id;
				}
			}
			return $answer;
		}

		/**
		 * @brief module_srl 리스트 및 포함/제외 여부에 따른 조건절을 만듬.
		 */
		function getSubquery($target_mid, $target_mode) {
			$no_secret = ' AND NOT is_secret:yes AND NOT module_srl:0';
			$target_mid = trim($target_mid);
			if ('' == $target_mid) return $no_secret;
			
			$target_mid_list = explode(',', $target_mid);
			$connective = strcmp('include', $target_mode) ? ' AND NOT ':' AND ';

			$query = $no_secret.$connective.'(module_srl:'.implode(' OR module_srl:', $target_mid_list).')';
			return $query;
		}

		/**
		 * @brief 검색어에서 nLucene 쿼리 문법을 적용
		 */
		function getQuery($query, $search_target) {
			$query_arr = explode(' ', $query);
			$answer = '';

			if ($search_target == "title_content") {
				return $this->getQuery($query, "title").$this->getQuery($query, "content");
			} else {
				foreach ($query_arr as $val) {
					$answer .= $search_target.':'.$val.' ';
				}
			}
			return $answer;
		}

		/**
		 *	@brief 검색 대상 필드를 확인
		 */
		function isFieldCorrect($fieldname) {
			$fields = array('title', 'content', 'title_content', 'tags');
			$answer = in_array($fieldname, $fields);
			return $answer; 
		}
	}
?>
