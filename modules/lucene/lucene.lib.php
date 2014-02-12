<?php
	/**
	 * @class   luceneIndexRequest
	 * @author  NHN (developers@xpressengine.com)
	 * @brief   lucene 모듈의 색인 등록 정보 중 공통 정보를 구성하는 부모 클래스.
	 **/
	class luceneIndexRequest {
		var $service_name_prefix = null;
		var $repo_path = null;
		var $renew_interval = null;
		var $dbinfo = null;

		var $conuri_tpl = array('cubrid' => "jdbc:%s:%s:%s:%s:::", 'mysql' => "jdbc:%s://%s:%s/%s");
		var $jdbcdriver = array('cubrid' => 'cubrid.jdbc.driver.CUBRIDDriver', 'mysql' => 'org.gjt.mm.mysql.Driver');
		var $where_tpl = array('cubrid'=>'where rownum between #start# + 1 and #start# + #length# ', 'mysql'=>'limit #start#, #length#');

		/**
		 * @brief constructor
		 */
		function luceneIndexRequest($args) {
			$this->dbinfo = $args->dbinfo;
			$this->repo_path = $args->repo_path;
			$this->renew_interval = $args->renew_interval;
			$this->service_name_prefix = $args->service_name_prefix;
		}

		/**
		 * @brief 색인요청 내용 중 공통적인 부분을 생성
		 */
		function getCommonArgs($service_name_suffix) {

			$oModuleModel = &getModel('module');
			$config = $oModuleModel->getModuleConfig('lucene');

			// 댓글 글 공통 내용에 대한 설정
			$reg->serviceNm = $this->service_name_prefix.$service_name_suffix;
			$reg->repPath = $this->repo_path;
			$reg->classNm = 'org.apache.lucene.analysis.kr.KoreanAnalyzer';
			$reg->url = 'http://www.nhn.com';
		
			// pre-warm 쿼리 관련설정
			$reg->queryField = 'title';
			$reg->queryText  = 'sps';
			$reg->sortFields = 'id';
			$reg->datasource = 'DB';

			// db 관련설정
			$reg->dbType   = $this->dbinfo->db_type;
			$reg->user	 = $this->dbinfo->db_userid;
			$reg->password = $this->dbinfo->db_password;
			$reg->jdbcDri  = $this->jdbcdriver[$this->dbinfo->db_type];

			// db 위치가 localhost이면 서버 IP로 대체
			if (!$config->db_server || $config->db_server == '') {
				$db_host = $this->dbinfo->db_hostname;
				if ($db_host == 'localhost' || $db_host == '127.0.0.1') $db_host = $_SERVER['SERVER_ADDR'];
			} else {
				$db_host = $config->db_server;
			}
	
			$reg->conURI = sprintf($this->conuri_tpl[$this->dbinfo->db_type], $this->dbinfo->db_type, $db_host, $this->dbinfo->db_port, $this->dbinfo->db_database);

			// 증분색인 관련 설정 (현재 적용안함)
			$reg->increment = 'false';
			$reg->startDate = date("Y-m-d");
			
			// nlucene 1.0.1 지원
			$reg->pageLength = '2000';
			$reg->pageLengt = '2000';
	
			// 날짜 및 갱신 간격 설정
			$today = date("Y-m-d G:i:s");
			$renew_interval_in_millisec = strval(intval($this->renew_interval) * 60000);
			$reg->db = array('simple', $today, $renew_interval_in_millisec);

			return $reg;
		 }
	}


	/**
	 * @class   luceneDocumentIndexRequest
	 * @author  NHN (developers@xpressengine.com)
	 * @brief   lucene 모듈의 글 색인 등록 정보를 구성하는 클래스.
	 **/
	class luceneDocumentIndexRequest extends luceneIndexRequest {
		var $content = array('content', 'analyzed_no_norms', 'yes', 'with_positions_offsets', 'string');
		var $tag = array('tags', 'analyzed_no_norms', 'yes', 'with_positions_offsets', 'string');
		var $title = array('title', 'analyzed_no_norms', 'yes', 'with_positions_offsets', 'string');
		var $id = array('id', 'not_analyzed_no_norms', 'yes', 'no', 'string');
		var $module_srl = array('module_srl', 'not_analyzed', 'yes', 'no', 'string');
		var $is_secret = array('is_secret', 'not_analyzed', 'yes', 'no', 'string');

		/**
		 * @brief constructor
		 */
		function luceneDocumentIndexRequest($args) {
			parent::luceneIndexRequest($args);
		}

		/**
		 * @brief 색인의 필드별로 분석옵션을 지정한 배열을 리턴
		 */
		function getIndexOption() {
			return array($this->content, $this->tag, $this->title, $this->id, $this->module_srl, $this->is_secret);
		}

		/**
		 * @brief 색인하기 위한 원문을 얻는 SQL 쿼리 리턴.
		 */
		function getSQL() {
			//$select =  'select A.content, A.tags, A.title, A.module_srl, A.document_srl as id , A.is_secret ';
			$select =  "select A.content, A.tags, A.title, A.module_srl, A.document_srl as id , replace(A.is_secret, 'Y', 'yes') as is_secret ";
			$from = 'from '.$this->dbinfo->db_table_prefix.'_documents as A ';
			$where = $this->where_tpl[$this->dbinfo->db_type];

			return $select.$from.$where;
		}

		/**
		 * @brief 색인요청 전체를 완성
		 */
		function getRequest() {
			$request = $this->getCommonArgs('_document');
			$request->sqlStr = $this->getSQL();
			$request->fields = $this->getIndexOption();

			return urlencode(json_encode($request));
		}
	}

	/**
	 * @class   luceneCommentIndexRequest
	 * @author  NHN (developers@xpressengine.com)
	 * @brief   lucene 모듈의 댓글 색인 등록 정보를 구성하는 클래스.
	 **/
	class luceneCommentIndexRequest extends luceneIndexRequest {
		var $content = array('content', 'analyzed_no_norms', 'yes', 'with_positions_offsets', 'string');
		var $id = array('id', 'not_analyzed_no_norms', 'yes', 'no', 'string');
		var $module_srl = array('module_srl', 'not_analyzed', 'yes', 'no', 'string');
		var $is_secret = array('is_secret', 'not_analyzed', 'yes', 'no', 'string');

		/**
		 * @brief contructor
		 */
		function luceneCommentIndexRequest($args) {
			parent::__construct($args);
		}

		/**
		 * @brief 색인의 필드별로 분석옵션을 지정한 배열을 리턴
		 */
		function getIndexOption() {
			return array($this->content, $this->id, $this->module_srl, $this->is_secret);
		}

		/**
		 * @brief 색인하기 위한 원문을 얻는 SQL 쿼리 리턴.
		 */
		function getSQL() {
			$select =  "select A.content, A.module_srl, A.comment_srl as id, replace(A.is_secret, 'Y', 'yes') as is_secret ";
			$from = 'from '.$this->dbinfo->db_table_prefix.'_comments as A ';
			$where = $this->where_tpl[$this->dbinfo->db_type];

			return $select.$from.$where;
		}

		/**
		 * @brief 색인요청 전체를 완성
		 */
		function getRequest() {
			$request = $this->getCommonArgs('_comment');
			$request->sqlStr = $this->getSQL();
			$request->fields = $this->getIndexOption();

			return urlencode(json_encode($request));
		}
	}
?>
