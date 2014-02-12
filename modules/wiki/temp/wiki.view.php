<?php
    /**
     * @class  wikiView
     * @author haneul (haneul0318@gmail.com)
     * @brief  wiki 모듈의 View class
     **/

    class wikiView extends wiki {
        var $search_option = array('title','content','title_content','comment','user_name','nick_name','user_id','tag');
        var $document_exists = array();

        /**
         * @brief 초기화
         * wiki 모듈은 일반 사용과 관리자용으로 나누어진다.\n
         **/
        function init() {
			$site_module_info = Context::get('site_module_info');
//			if($site_module_info->site_srl==0) die('not allowed');

		
			// 한국어 인코딩에 대한 체크 - #18764757 - taggon
			$entry = Context::get('entry');
			// #18707314 로 인해 주석 처리 - yarra
			/*
			if ($entry == iconv('cp949', 'cp949', $entry)) {
				$entry = iconv('cp949', 'utf-8', $entry);
				Context::set('entry', $entry);
			}*/

			

            /**
             * 스킨 경로를 미리 template_path 라는 변수로 설정함
             * 스킨이 존재하지 않는다면 xe_wiki로 변경
             **/
            $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            if(!is_dir($template_path) || !$this->module_info->skin) {
                $this->module_info->skin = 'xe_wiki';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            }
            $this->setTemplatePath($template_path);

            $oModuleModel = &getModel('module');

            $document_config = $oModuleModel->getModulePartConfig('document', $this->module_info->module_srl);
            if(!isset($document_config->use_history)) $document_config->use_history = 'N';
            $this->use_history = $document_config->use_history;
            Context::set('use_history', $document_config->use_history);

            Context::addJsFile($this->module_path.'tpl/js/wiki.js');

            Context::set('grant', $this->grant);

			debugPrint($this->document_exists);
        }

        /**
         * @brief 선택된 글 출력
         **/
        function dispWikiContent() {
            $output = $this->dispWikiContentView();
			debugPrint($this->document_exists);
            if(!$output->toBool()) return;
        }

        function dispWikiHistory() {
            $oDocumentModel = &getModel('document');
            $document_srl = Context::get('document_srl');
            $page = Context::get('page');
            $oDocument = $oDocumentModel->getDocument($document_srl);
            if(!$oDocument->isExists()) return $this->stop('msg_invalid_request');
            $entry = $oDocument->getTitleText();
            Context::set('entry',$entry);
            $output = $oDocumentModel->getHistories($document_srl, 10, $page);
            if(!$output->toBool() || !$output->data) 
            {
                Context::set('histories', array());
            }
            else {
                Context::set('histories',$output->data);
                Context::set('page', $output->page);
                Context::set('page_navigation', $output->page_navigation);
            }
            
            Context::set('oDocument', $oDocument);
            $this->setTemplateFile('histories');
        }

        function dispWikiEditPage() {
            if(!$this->grant->write_document) return $this->dispWikiMessage('msg_not_permitted');

            $oDocumentModel = &getModel('document');
            $document_srl = Context::get('document_srl');
            $oDocument = $oDocumentModel->getDocument(0, $this->grant->manager);
            $oDocument->setDocument($document_srl);
            $oDocument->add('module_srl', $this->module_srl);
            Context::set('document_srl',$document_srl);
            Context::set('oDocument', $oDocument);
            $history_srl = Context::get('history_srl');
            if($history_srl)
            {
                $output = $oDocumentModel->getHistory($history_srl);
                if($output && $output->content != null)
                {
                    Context::set('history', $output);
                }
            } 
            Context::addJsFilter($this->module_path.'tpl/filter', 'insert.xml');

            $this->setTemplateFile('write_form');
        }

        /**
         * @brief Displaying Message 
         **/
        function dispWikiMessage($msg_code) {
            $msg = Context::getLang($msg_code);
            if(!$msg) $msg = $msg_code;
            Context::set('message', $msg);
            $this->setTemplateFile('message');
        }

        function dispWikiTitleIndex() {
            $page = Context::get('page');
            $oDocumentModel = &getModel('document');
            $obj->module_srl = $this->module_info->module_srl;
            $obj->sort_index = 'update_order';
            $obj->page = $page;
            $obj->list_count = 50;

            $obj->search_keyword = Context::get('search_keyword');
            $obj->search_target = Context::get('search_target');
            $output = $oDocumentModel->getDocumentList($obj);

            Context::set('document_list', $output->data);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);

            // 검색 옵션 세팅
            foreach($this->search_option as $opt) $search_option[$opt] = Context::getLang($opt);
            Context::set('search_option', $search_option);

            $this->setTemplateFile('title_index');
        }

        function dispWikiTreeIndex() {
            $oWikiModel = &getModel('wiki');
            Context::set('list', $oWikiModel->readWikiTreeCache($this->module_srl));
            $this->setTemplateFile('tree_list');
        }

        function dispWikiModifyTree() {
            if(!$this->grant->write_document) return new Object(-1,'msg_not_permitted');
            Context::set('isManageGranted', $this->grant->write_document?'true':'false');
            $this->setTemplateFile('modify_tree');
        }

        function addToVisitLog($entry) {
            $module_srl = $this->module_info->module_srl;
            if(!$_SESSION['wiki_visit_log'])
            {
                $_SESSION['wiki_visit_log'] = array();
            }
            if(!$_SESSION['wiki_visit_log'][$module_srl] || !is_array($_SESSION['wiki_visit_log'][$module_srl]))
            {
                $_SESSION['wiki_visit_log'][$module_srl] = array(); 
            }
            else
            {
                foreach($_SESSION['wiki_visit_log'][$module_srl] as $key => $value)
                {
                    if($value == $entry)
                    {
                        unset($_SESSION['wiki_visit_log'][$module_srl][$key]);
                    }
                }
                
                if(count($_SESSION['wiki_visit_log'][$module_srl]) >= 5)
                {
                    array_shift($_SESSION['wiki_visit_log'][$module_srl]);
                }
            }
            $_SESSION['wiki_visit_log'][$module_srl][] = $entry;
        }

        function callback_check_exists($matches)
        {
			$entry_name = wiki::makeEntryName($matches);
			$this->document_exists[$entry_name->link_entry] = 0;

<<<<<<< .mine
                $this->document_exists[$entry] = 0;
            }
            return $matches[0]; 
=======
			return $matches[0];
>>>>>>> .r45
        }

        function getCSSClass($name)
        {
            if($this->document_exists[$name]) return "exists";

            else return "notexist";
        }


		/**
		 * @brief 위키 문법에 따라 치환되는 링크를 리턴
		 */
        function callback_wikilink($matches)
        {
			if($matches[1][0] == "!") return "[".substr($matches[1], 1)."]";

			$entry_name = wiki::makeEntryName($matches);
			$answer = "<a href=\"".getFullUrl('', 'mid', $this->mid, 'entry', $entry_name->link_entry, 'document_srl', '')."\" class=\"".$this->getCSSClass($entry_name->link_entry)."\" >".$entry_name->printing_name."</a>";

			return $answer;
        }


        function dispWikiContentView() {
            $oWikiModel = &getModel('wiki');
            $oDocumentModel = &getModel('document');

            // 요청된 변수 값들을 정리
            $document_srl = Context::get('document_srl');
            $entry = Context::get('entry');
            if(!$document_srl && !$entry) {
                $entry = "Front Page";
                Context::set('entry', $entry);
                $document_srl = $oDocumentModel->getDocumentSrlByAlias($this->module_info->mid, $entry);
            }

            /**
             * 요청된 문서 번호가 있다면 문서를 구함
             **/
            if($document_srl) {
                $oDocument = $oDocumentModel->getDocument($document_srl);

                // 해당 문서가 존재할 경우 필요한 처리를 함
                if($oDocument->isExists()) {

                    // 글과 요청된 모듈이 다르다면 오류 표시
                    if($oDocument->get('module_srl')!=$this->module_info->module_srl ) return $this->stop('msg_invalid_request');

                    // 관리 권한이 있다면 권한을 부여
                    if($this->grant->manager) $oDocument->setGrant();

                    if(!$entry)
                    {
                        $entry = $oDocument->getTitleText();
                        Context::set('entry', $entry);
                    }

                    // 상담기능이 사용되고 공지사항이 아니고 사용자의 글도 아니면 무시

                    $history_srl = Context::get('history_srl');
                    if($history_srl)
                    {
                        $output = $oDocumentModel->getHistory($history_srl);
                        if($output && $output->content != null)
                        {
                            Context::set('history', $output);
                        }
                    } 	

                    $content = $oDocument->getContent(false);

					debugPrint($content);

                    $content = preg_replace_callback("!\[([^\]]+)\]!is", array( $this, 'callback_check_exists' ), $content );

					debugPrint($content);

                    $entries = array_keys($this->document_exists);
<<<<<<< .mine
                    $oDB = &DB::getInstance();
			
					if(count($entries))
					{
						$args->entries = $entries;
						$args->module_srl = $this->module_info->module_srl;
						$output = executeQueryArray("wiki.getDocumentsWithEntries", $args);
						if($output->data)
						{
							foreach($output->data as $alias)
							{
								$this->document_exists[$alias->alias_title] = 1;
							}
						}
					}
=======
                    $oDB = &DB::getInstance();
			
					if(count($entries))
					{
						$args->entries = $entries;
						$args->module_srl = $this->module_info->module_srl;
						$output = executeQueryArray("wiki.getDocumentsWithEntries", $args);

						debugPrint($output);

						if($output->data)
						{
							foreach($output->data as $alias)
							{
								$this->document_exists[$alias->alias_title] = 1;
							}
						}
					}
>>>>>>> .r45
                    $content = preg_replace_callback("!\[([^\]]+)\]!is", array( $this, 'callback_wikilink' ), $content );
					$content = preg_replace('@<([^>]*)(src|href)="((?!https?://)[^"]*)"([^>]*)>@i','<$1$2="'.Context::getRequestUri().'$3"$4>', $content);

                    $oDocument->add('content', $content);

                    // 이전 다음 구하기
                    list($prev_document_srl, $next_document_srl) = $oWikiModel->getPrevNextDocument($this->module_srl, $document_srl);
                    if($prev_document_srl) Context::set('oDocumentPrev', $oDocumentModel->getDocument($prev_document_srl));
                    if($next_document_srl) Context::set('oDocumentNext', $oDocumentModel->getDocument($next_document_srl));
                    $this->addToVisitLog($entry);

                // 요청된 문서번호의 문서가 없으면 document_srl null 처리 및 경고 메세지 출력
                } else {
                    Context::set('document_srl','',true);

                    return new Object(-1, 'msg_not_founded');
                }

            /**
             * 요청된 문서 번호가 아예 없다면 빈 문서 객체 생성
             **/
            } else {
                $oDocument = $oDocumentModel->getDocument(0);
            }

            /**
             * 글 보기 권한을 체크해서 권한이 없으면 오류 메세지 출력하도록 처리
             **/
            if($oDocument->isExists()) {
                // 브라우저 타이틀에 글의 제목을 추가
                Context::addBrowserTitle($oDocument->getTitleText());

                // 조회수 증가 (비밀글일 경우 권한 체크)
                if(!$oDocument->isSecret() || $oDocument->isGranted()) $oDocument->updateReadedCount();

                // 비밀글일때 컨텐츠를 보여주지 말자.
                if($oDocument->isSecret() && !$oDocument->isGranted()) $oDocument->add('content',Context::getLang('thisissecret'));
                $this->setTemplateFile('view_document');

                // set contributors
                if($this->use_history)
                {
                    $oModel = &getModel('wiki');
                    $contributors = $oModel->getContributors($oDocument->document_srl);
                    Context::set('contributors', $contributors);
                }

                // 댓글 허용일 경우 문서에 강제 지정
                if($this->module_info->use_comment != 'N') $oDocument->add('allow_comment','Y');
            }
            else
            {
                $this->setTemplateFile('create_document');
            }
            Context::set('visit_log', $_SESSION['wiki_visit_log'][$this->module_info->module_srl]);
            // 스킨에서 사용할 oDocument 변수 세팅
            Context::set('oDocument', $oDocument);

            /** 
             * 사용되는 javascript 필터 추가
             **/
            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');
        
            return new Object();
        }

        /**
         * @brief 댓글의 댓글 화면 출력
         **/
        function dispWikiReplyComment() {
            // 권한 체크
            if(!$this->grant->write_comment) return $this->dispWikiMessage('msg_not_permitted');

            // 목록 구현에 필요한 변수들을 가져온다
            $parent_srl = Context::get('comment_srl');

            // 지정된 원 댓글이 없다면 오류
            if(!$parent_srl) return new Object(-1, 'msg_invalid_request');

            // 해당 댓글를 찾아본다
            $oCommentModel = &getModel('comment');
            $oSourceComment = $oCommentModel->getComment($parent_srl, $this->grant->manager);

            // 댓글이 없다면 오류
            if(!$oSourceComment->isExists()) return $this->dispWikiMessage('msg_invalid_request');
            if(Context::get('document_srl') && $oSourceComment->get('document_srl') != Context::get('document_srl')) return $this->dispWikiMessage('msg_invalid_request');

            // 대상 댓글을 생성
            $oComment = $oCommentModel->getComment();
            $oComment->add('parent_srl', $parent_srl);
            $oComment->add('document_srl', $oSourceComment->get('document_srl'));

            // 필요한 정보들 세팅
            Context::set('oSourceComment',$oSourceComment);
            Context::set('oComment',$oComment);

            /** 
             * 사용되는 javascript 필터 추가
             **/
            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

            $this->setTemplateFile('comment_form');
        }

        /**
         * @brief 댓글 수정 폼 출력
         **/
        function dispWikiModifyComment() {
            // 권한 체크
            if(!$this->grant->write_comment) return $this->dispWikiMessage('msg_not_permitted');

            // 목록 구현에 필요한 변수들을 가져온다
            $document_srl = Context::get('document_srl');
            $comment_srl = Context::get('comment_srl');

            // 지정된 댓글이 없다면 오류
            if(!$comment_srl) return new Object(-1, 'msg_invalid_request');

            // 해당 댓글를 찾아본다
            $oCommentModel = &getModel('comment');
            $oComment = $oCommentModel->getComment($comment_srl, $this->grant->manager);

            // 댓글이 없다면 오류
            if(!$oComment->isExists()) return $this->dispWikiMessage('msg_invalid_request');

            // 글을 수정하려고 할 경우 권한이 없는 경우 비밀번호 입력화면으로
            if(!$oComment->isGranted()) return $this->setTemplateFile('input_password_form');

            // 필요한 정보들 세팅
            Context::set('oSourceComment', $oCommentModel->getComment());
            Context::set('oComment', $oComment);

            /** 
             * 사용되는 javascript 필터 추가
             **/
            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

            $this->setTemplateFile('comment_form');
        }

        /**
         * @brief 댓글 삭제 화면 출력
         **/
        function dispWikiDeleteComment() {
            // 권한 체크
            if(!$this->grant->write_comment) return $this->dispWikiMessage('msg_not_permitted');

            // 삭제할 댓글번호를 가져온다
            $comment_srl = Context::get('comment_srl');

            // 삭제하려는 댓글이 있는지 확인
            if($comment_srl) {
                $oCommentModel = &getModel('comment');
                $oComment = $oCommentModel->getComment($comment_srl, $this->grant->manager);
            }

            // 삭제하려는 글이 없으면 에러
            if(!$oComment->isExists() ) return $this->dispWikiContent();

            // 권한이 없는 경우 비밀번호 입력화면으로
            if(!$oComment->isGranted()) return $this->setTemplateFile('input_password_form');

            Context::set('oComment',$oComment);

            /** 
             * 필요한 필터 추가
             **/
            Context::addJsFilter($this->module_path.'tpl/filter', 'delete_comment.xml');

            $this->setTemplateFile('delete_comment_form');
        }

    }
?>
