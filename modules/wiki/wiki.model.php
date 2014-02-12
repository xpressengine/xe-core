<?php
	/**
	* @class  wikiModel
	* @author NHN (developers@xpressengine.com) 
	* @brief  wiki 모듈의 Model class
	**/

	class WikiModel extends module {
		/**
		* @brief Initialization
		**/
		function init() {
		    
		}

		/**
		* @brief Retrieve the Tree hierarchy
		* document_category테이블을 이용해서 위키 문서의 계층 구조도를 그림
		* document_category테이블에 등록되어 있지 않은 경우 depth = 0 으로 하여 신규 생성
		**/
		function getWikiTreeList() {
			$oWikiController = &getController('wiki');

			header("Content-Type: text/xml; charset=UTF-8");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");

			if(!$this->module_srl) return new Object(-1,'msg_invalid_request');

			$xml_file = sprintf('%sfiles/cache/wiki/%d.xml', _XE_PATH_,$this->module_srl);
			if(!file_exists($xml_file)) $oWikiController->loadWikiTreeList($this->module_srl);
			print FileHandler::readFile($xml_file);
			Context::close();
			exit();
		}


		/**
		* @brief Read hierarchy from the cache 
		*/
		function readWikiTreeCache($module_srl) {
		    	
			$oWikiController = &getController('wiki');
			if(!$module_srl) return new Object(-1,'msg_invalid_request');

			$dat_file = sprintf('%sfiles/cache/wiki/%d.dat', _XE_PATH_,$module_srl);
			if(!file_exists($dat_file)) $oWikiController->recompileTree($module_srl);
			$buff = explode("\n", trim(FileHandler::readFile($dat_file)));
			if(!count($buff)) return array();
			$module_info = Context::get('module_info');
			$list = array();
			foreach($buff as $val) {
				if(!preg_match('/^([0-9]+),([0-9]+),([0-9]+),([0-9]+),(.+)$/i', $val, $m)) continue;
				unset($obj);
				$obj->parent_srl = $m[1];
				$obj->document_srl = $m[2];
				$obj->depth = $m[3];
				$obj->childs = $m[4];
				$obj->title = $m[5];
				
				if ($module_info->menu_style == "classic")
					$list[] = $obj;
				else
					$list[$obj->document_srl] = $obj;
			}
			return $list;
		}


		/**
		* @brief Read hierarchy 
		*/
		function loadWikiTreeList($module_srl) {
			// Select wanted List
			$args->module_srl = $module_srl;
			$output = executeQueryArray('wiki.getTreeList', $args);

			if(!$output->data || !$output->toBool()) return array();

			$list = array();
			//$root = $this->getRootDocument($module_srl);
			// root
			$min = -1;
			$selected = 0;
			foreach ($output->data as $key => $node) 
			{
				if($node->list_order == 0)
				{
					if ($min < 0)
					{
						$min = (int)$node->document_srl;
						$selected = $key;
					}
					else
					{
						if ((int)$node->document_srl < $min)
						{
							$min = (int)$node->document_srl;
							$selected = $key;
						}
					}
				}
			}
			
			$root_node = $output->data[$selected];
			$root_node->parent_srl = 0;
			foreach($output->data as $node)
			{
				if($node->document_srl == $root_node->document_srl)
				{
					continue;
				}
				
				unset($obj);
				$obj->parent_srl = (int)$node->parent_srl;
				$obj->document_srl = (int)$node->document_srl;
				$obj->title = $node->title;
				$list[$obj->document_srl] = $obj;
			}
			$tree = array();
			$result = array();
			$tree[$root_node->document_srl]->node = $root_node;

			foreach($list as $document_srl => $node) {
				if(!$list[$node->parent_srl]) $node->parent_srl = $root_node->document_srl;
				$tree[$node->parent_srl]->childs[$document_srl] = &$tree[$document_srl];
				$tree[$document_srl]->node = $node;

			}

			$result[$root_node->document_srl] = $tree[$root_node->document_srl]->node;
			$result[$root_node->document_srl]->childs = count($tree[$root_node->document_srl]->childs);

			$this->getTreeToList($tree[$root_node->document_srl]->childs, $result,1);
			return $result;
		}
		
		function getRootDocument($module_srl)
		{
			$docs_list = $this->readWikiTreeCache($module_srl);
			if (is_array($docs_list))
			{
				$min = -1;
				$selected = 0;
				foreach ($docs_list as $key => $node) 
				{
					if($node->parent_srl == 0)
					{
						if ($min < 0)
						{
							$min = (int)$node->document_srl;
							$selected = $key;
						}
						else
						{
							if ((int)$node->document_srl < $min)
							{
								$min = (int)$node->document_srl;
								$selected = $key;
							}
						}
					}
				}
				return $docs_list[$selected];
			}
			else
			{
				return null;
			}
		}

		/**
		* @brief Load previous / next article
		*/
		function getPrevNextDocument($module_srl, $document_srl) {
			$list = $this->readWikiTreeCache($module_srl);
			if(!count($list)) return array(0,0);

			$prev = $next_srl = $prev_srl = 0;
			$checked = false;
			foreach($list as $key => $val) {
				if($checked) {
					$next_srl = $val->document_srl; 
					break;
				}
				if($val->document_srl == $document_srl) {
					$prev_srl = $prev;
					$checked = true;
				}
				$prev = $val->document_srl;
			}
			return array($prev_srl, $next_srl);
		}


		function getTreeToList($childs, &$result,$depth) {
			if(!count($childs)) return;
			foreach($childs as $key => $node) {
				$node->node->depth = $depth;
				$node->node->childs = count($node->childs);
				$result[$key] = $node->node;
				if($node->childs) $this->getTreeToList($node->childs, $result,$depth+1);
			}
		}

		function getContributors($document_srl) {
			$oDocumentModel = &getModel('document');
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) return array();

			$args->document_srl = $document_srl;
			$output = executeQueryArray("wiki.getContributors", $args);
			if($output->data) $list = $output->data;
			else $list = array();

			$item->member_srl = $oDocument->getMemberSrl();
			$item->nick_name = $oDocument->getNickName();
			$contributors[] = $item;
			for($i=0,$c=count($list);$i<$c;$i++) {
				unset($item);
				$item->member_srl = $list[$i]->member_srl;
				$item->nick_name = $list[$i]->nick_name;
				if($item->member_srl == $oDocument->getMemberSrl()) continue;
				$contributors[] = $item;
			}
			return $contributors;
		}
		
		/**
		* Prepares the documents tree for display
		* by describing the relationship of each node
		* with the page being viewed: parent, sibling, child etc.
		*
		* Used for displaying the sidebar tree menu of the wiki
		*/
		function getMenuTree($module_srl, $document_srl, $mid)
		{
			/** Create menu tree */
			$documents_tree = $this->readWikiTreeCache($module_srl);
			$current_node = &$documents_tree[$document_srl];

			/* Mark current node as type 'active' */
			if($current_node->parent_srl != 0)
				$current_node->type = 'current';

			/* Find and mark parents */
			$node_srl_iterator = $current_node->parent_srl;

			while($node_srl_iterator > 0){
				if($documents_tree[$node_srl_iterator]->parent_srl != 0)
				$documents_tree[$node_srl_iterator]->type = 'parent';
				else
				$documents_tree[$node_srl_iterator]->type = 'active_root';
				$node_srl_iterator = $documents_tree[$node_srl_iterator]->parent_srl;
			}
			$oDocumentModel = &getModel("document");
			
			$documents_tree_copy = $documents_tree;
			foreach($documents_tree_copy as $key => $value){
				$node = &$documents_tree[$key];
				$node->href = getSiteUrl('','mid',$mid,'entry',$oDocumentModel->getAlias($node->document_srl), 'document_srl', '', 'history_srl', '', 'act', 'dispWikiContent');
				if(!isset($documents_tree[$node->document_srl]->type)){
					if($node->parent_srl == 0)
						$documents_tree[$node->document_srl]->type = 'root';
					else if($node->parent_srl == $current_node->parent_srl)
						$documents_tree[$node->document_srl]->type = 'sibling';
					else if($node->parent_srl == $current_node->document_srl)
						$documents_tree[$node->document_srl]->type = 'child';
					else unset($documents_tree[$node->document_srl]);
				}
			}

			return $documents_tree;
		}
		
		/**
		* @brief Recursive function that create BreadCrumbs Menu Array
		* @access public
		* 
		* @param $document_srl
		* @param $list
		* @param $list_breadcrumbs
		* 
		* @return array
		*/
		function createBreadcrumbsList($document_srl, $list, $list_breadcrumbs = array())
		{
			$oDocumentModel = &getModel("document");
			
			if ((int)$list[$document_srl]->parent_srl > 0)
			{
				$list_breadcrumbs[$list[$document_srl]->title] = $oDocumentModel->getAlias($document_srl);
				$list_breadcrumbs = $this->createBreadcrumbsList((int)$list[$document_srl]->parent_srl, $list, $list_breadcrumbs);
			}
			return $list_breadcrumbs;
		}
		
		/**
		* @brief Function that return BreadCrumbs Menu
		* @access public
		* 
		* @param $breadcrumbs_list
		* 
		* @return string
		*/
		function getBreadcrumbs($document_srl, $list)
		{
			$breadcrumbs = array_reverse($this->createBreadcrumbsList($document_srl, $list));
			$uri = Context::getRequestUri().Context::get("mid")."/";
			//$menu_breadcrumbs = "<a href='" . $uri . "'>Front Page</a>";
			$oModuleModel = &getModel("module");
			$oDocumentModel = &getModel("document");
			$module_srl = $oModuleModel->getModuleSrlByMid(Context::get("mid"));
			$root = $this->getRootDocument($module_srl[0]);
			$document_srl = $root->document_srl;
			$entry = $oDocumentModel->getAlias($document_srl);
			$menu_breadcrumbs = '';
			
			// Remove info about current page from array, for processing separately
			// Done with array functions, because array pop only return last element's value and not its key
			reset($breadcrumbs); // Reset internal pointer
			end($breadcrumbs); // Go to last element
			$current_page_title = key($breadcrumbs); // Retrieve its key, which represents page title
			array_pop($breadcrumbs); // Remove last element from list
			
			foreach($breadcrumbs as $key=>$value)
			{
				$menu_breadcrumbs .= "<strong>&nbsp;»&nbsp;</strong> <a href='" . $uri . "entry/$value'>" . $key . "</a>";
			}
			// Display current page without link
			if($current_page_title)
				$menu_breadcrumbs .= "<strong>&nbsp;»&nbsp;</strong> $current_page_title ";
			
			// Add home page only if current page is not Home page itself
			if($menu_breadcrumbs != '')
				$menu_breadcrumbs = "<a href='" . $uri . "entry/".$entry."'>".$root->title."</a>" . $menu_breadcrumbs;
			
			return $menu_breadcrumbs;
		}
		
		/**
		* Retrieves a list of all XE Wikimodules
		*/
        function getModuleList($add_extravars = false)
        {
            $args->sort_index = "module_srl";
            $args->page = 1;
            $args->list_count = 200;
            $args->page_count = 10;
            $args->s_module_category_srl = Context::get('module_category_srl');

            $output = executeQueryArray('wiki.getManualList', $args);
            ModuleModel::syncModuleToSite($output->data);

            if(!$add_extravars){
                    return  $output->data;
            }

            $oModuleModel = &getModel('module');
			
			if($output->data)
            foreach($output->data as $module_info){
                    $extra_vars = $oModuleModel->getModuleExtraVars($module_info->module_srl);
                    foreach($extra_vars[$module_info->module_srl] as $k=>$v){
                            $module_info->{$k} = $v;
                    }
            }

            return  $output->data;
        }
		
		/**
		* Searches through documents for the existence of a certain string
		*/
        function search($is_keyword, $target_module_srl, $search_target, $page, $items_per_page= 10)
        {
            $oLuceneModule = &getModule('lucene');

            if( !isset($oLuceneModule) ){
                    //if nlucene not installed we fallback to IS (integrated search module)
                    return $this->_is_search($is_keyword, $target_module_srl, $search_target, $page, $items_per_page);
            }

            return $this->_lucene_search($is_keyword, $target_module_srl, $search_target, $page, $items_per_page);
        }
		
		/**
		* Searches through documents for the existence of a certain string
		*
		* Used when nLucene module is installed.
		*/
        function _lucene_search($is_keyword, $target_module_srl, $search_target, $page, $items_per_page= 10 )
        {
            $oLuceneModel = &getModel('wiki'); //temporary imported sources so we not interfere with nlucene

            $searchAPI = "lucene_search_bloc-1.0/SearchBO/";
            $searchUrl = $oLuceneModel->getDefaultSearchUrl($searchAPI);

            if(!$oLuceneModel->isFieldCorrect($search_target)){
              $search_target = 'title_content';
            }

            //Search queries applied to the target module
            $query = $oLuceneModel->getSubquery($target_module_srl, "include", null);

            //Parameter setting
			$json_obj = new stdClass();
            $json_obj->query = $oLuceneModel->getQuery($is_keyword, $search_target, null);
            $json_obj->curPage = $page;
            $json_obj->numPerPage = $items_per_page;
            $json_obj->indexType = "db";
            $json_obj->fieldName = $search_target;
            $json_obj->target_mid = $target_module_srl;
            $json_obj->target_mode = $target_mode;

            $json_obj->subquery = $query;

            return $oLuceneModel->getDocuments($searchUrl, $json_obj);
        }
		
		/**
		* @brief Post id list, bringing the article.
		*/
        function getDocuments($searchUrl, $params, $service_prefix = null) {
			$output = new stdClass;
			if( !isset($service_prefix) ){
					$service_prefix = $this->getDefaultServicePrefix();
			}
            $oModelDocument = &getModel('document');

            $params->serviceName = $service_prefix.'_document';
            $params->query = '('.$params->query.')'.$params->subquery;
            $params->displayFields = array("id");

			$service = $this->getService();
            $encodedParams = $service->encode($params);
            $searchResult = FileHandler::getRemoteResource($searchUrl."searchByMap", $encodedParams, 3, "POST", "application/json; charset=UTF-8", array(), array());

            // Validate the results
            if (!$searchResult && $searchResult != "null") {
                $idList = array();
            } else {
                $idList = $this->result2idArray($searchResult);
            }

            // 결과가 1개 이상이어야 글 본문을 요청함.
            // Results must be at least one body has requested post.
            $documents = array();
            if (count($idList) > 0) {
                $tmpDocuments = $oModelDocument->getDocuments($idList, false, false);
                // Russineseo received a list of documents returned by rearranging the order
                foreach($idList as $id) {
                    $documents['doc'.$id] = $tmpDocuments[$id];
                }
            }
            $searchResult = json_decode($searchResult);
            $page_navigation = new PageHandler($searchResult->totalSize, ceil( (float)$searchResult->totalSize / 10.0 ), $params->curPage, 10);

            $output->total_count = $searchResult->totalSize;
            $output->data = $documents;
            $output->page_navigation = $page_navigation;
            return $output;
        }
		
		/**
		* @brief Results extracted from an array of id
		*/
        function result2idArray($res) {
            $res = json_decode($res);
            $results = $res->results;
            $answer = array();
            if ( count($results) > 0) {
                foreach ($results as $result) {
                    $answer[] = $result->id;
                }
            }
            return $answer;
       }
		
		/**
		* @brief To determine which fields to search
		* Check the Search for field
		*/
        function isFieldCorrect($fieldname) {
            $fields = array('title', 'content', 'title_content', 'tags');
            $answer = in_array($fieldname, $fields);
            return $answer;
        }
		
		function getDefaultSearchUrl($searchAPI)
        {
            $oModuleModel = &getModel('module');
            $config = $oModuleModel->getModuleConfig('lucene');
            syslog(1, "lucene config: ".print_r($config, true)."\n");
            return $searchUrl = $config->searchUrl.$searchAPI;
        }
		
		/**
		* @brief List and include / exclude based on whether the clause making
		*/
        function getSubquery($target_mid, $target_mode, $exclude_module_srl=NULL) {
          if( isset($exclude_module_srl) ){
            $no_secret = ' AND NOT is_secret:yes AND NOT module_srl:'.$exclude_module_srl."; ";
          }else{
                $no_secret = ' AND NOT is_secret:yes ';
          }
            $target_mid = trim($target_mid);
            if ('' == $target_mid) return $no_secret;

            $target_mid_list = explode(',', $target_mid);
            $connective = strcmp('include', $target_mode) ? ' AND NOT ':' AND ';

            $query = $no_secret.$connective.'(module_srl:'.implode(' OR module_srl:', $target_mid_list).')';
            return $query;
        }

        /**
		* @brief Results for query syntax to apply the nLucene
        */
        function getQuery($query, $search_target, $exclude_module_srl='0') {
            $query_arr = explode(' ', $query);
            $answer = '';

            if ($search_target == "title_content") {
              return $this->getQuery($query, "title", $exclude_module_srl).$this->getQuery($query, "content", $exclude_module_srl);
            } else {
                foreach ($query_arr as $val) {
                    $answer .= $search_target.':'.$val.' ';
                }
            }
            return $answer;
        }
		
		/**
		* Searches through documents for the existence of a certain string
		*
		* Used for XE Wiki search textbox when nLucene is not installed.
		*/
        function _is_search($is_keyword, $target_module_srl, $search_target, $page, $items_per_page= 10)
        {
            $oDocumentModel = &getModel('document');

            $obj = null;
            $obj->module_srl = array($target_module_srl);
            $obj->page = $page;
            $obj->list_count = $items_per_page;
            $obj->exclude_module_srl = '0';
            $obj->sort_index = 'module';
            //$obj->order_type = 'asc';
            $obj->search_keyword = $is_keyword;
            $obj->search_target = $search_target;
            return $oDocumentModel->getDocumentList($obj);
        }
		
		/**
		 * Get pages that link to this document
		 */
		function getInboundLinks($document_srl){
			$args->document_srl = $document_srl;
			$output = executeQueryArray('wiki.getInboundLinks', $args);
			if(!$output->toBool() || !$output->data) return array();
			return $output->data;
		}
	
		/**
		 * Get pages this document links to
		 */
		function getOutboundLinks($document_srl){
			$args->document_srl = $document_srl;
			$output = executeQueryArray('wiki.getLinkedDocuments', $args);
			if(!$output->toBool() || !$output->data) return array();
			return $output->data;
		}		

		/** 
		 * @brief return module name in sitemap
		 **/
		function triggerModuleListInSitemap(&$obj)
		{
			array_push($obj, 'wiki');
		}

		function getDefaultServicePrefix(){
			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');
			return $config->service_name_prefix;
		}

		/* lucene search related */
		var $json_service = null;

		function getService(){
			require_once(_XE_PATH_.'modules/lucene/lib/jsonphp.php');
			if( !isset($this->json_service) ){
				//debug_syslog(1, "creating new json_service\n");
				$this->json_service = new Services_JSON(0);
			}else{
				//debug_syslog(1, "reusing json_service\n");
			}
			return $this->json_service;

		}

	}
?>
