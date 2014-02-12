<?php
    /**
     * @class  kinView
     * @author zero (skklove@gmail.com)
     * @brief  kin view class
     **/

    class kinView extends kin {

		var $list_count = 20;

        function init() {
            $oDocumentModel = &getModel('document');
			if($this->module_info->list_count) $this->list_count = $this->module_info->list_count;

            $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            if(!is_dir($template_path)||!$this->module_info->skin) {
                $this->module_info->skin = 'xe_kin_official';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            }

			$categories = $oDocumentModel->getCategoryList($this->module_info->module_srl);
			if($this->module_info->hide_category != 'Y' && count($categories))
			{
				$this->module_info->use_category = 'Y';
				Context::set('categories', $categories);
			}
			else
			{
				$this->module_info->use_category = 'N';
			}

            $this->setTemplatePath($template_path);
            $this->setTemplateFile(strtolower(str_replace('dispKin','',$this->act)));

			$security = new Security($this->module_info);
			$security->encodeHTML('.qa_title');
        }

        function dispKinIndex() {
            $oKinModel = &getModel('kin');
            $oDocumentModel = &getModel('document');

            if(Context::get('document_srl')) {
                $this->setTemplateFile('view');
                return $this->dispKinView();
            }

            $category_srl = Context::get('category_srl');
			if(!$category_srl) $category_srl = Context::get('category');
			else{
				$category_info =  $oDocumentModel->getCategory($category_srl);
				Context::set('category_info', $category_info);

				$parent_srl = Context::get('parent_srl');
				if($parent_srl){
					$parent_category_info =  $oDocumentModel->getCategory($parent_srl);
					Context::set('parent_category_info', $parent_category_info);
					$childCategoryCount = $oDocumentModel->getCategoryChlidCount($parent_srl);
					
					if($childCategoryCount){
						$categories = Context::get('categories');
						$category_childs = $categories[$parent_srl]->childs;
						$category_child_list = array();
						if($category_childs){
							foreach($category_childs as $key => $child_category_srl){
								$child_category_info =  $oDocumentModel->getCategory($child_category_srl);
								$category_child_list[$child_category_srl] = $child_category_info;
							}
						}
					}
					Context::set('category_child_list',$category_child_list);

				}else{
					$childCategoryCount = $oDocumentModel->getCategoryChlidCount($category_srl);
					if($childCategoryCount){
						$categories = Context::get('categories');
						$category_childs = $categories[$category_srl]->childs;

						$category_child_list = array();
						foreach($category_childs as $key => $child_category_srl){
							$child_category_info =  $oDocumentModel->getCategory($child_category_srl);
							$category_child_list[$child_category_srl] = $child_category_info;
						}
						Context::set('category_child_list',$category_child_list);
					}
				}

			}

            $page = Context::get('page');
            if(!$page) Context::set('page',$page=1);
            $type = Context::get('type');
            if(!Context::get('is_logged')&&$type == 'my_questions') $type = '';
			
			// default type is questions
			if(!$type) $type = 'questions';
			Context::set('type',$type);


            $logged_info = Context::get('logged_info');

            $search_keyword = Context::get('search_keyword');
			
			// re-initialize category children
			$category_childs = $categories[$category_srl]->childs;
            switch($type) {
                case 'questions' :
                        $output = $oKinModel->getNotRepliedQuestions($this->module_srl, $category_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('document_list', $output->data);
                    break;
                case 'replies' :
                        $output = $oKinModel->getNotSelectedReplies($this->module_srl, $category_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('reply_list', $output->data);
                    break;
                case 'selected' :
                        $output = $oKinModel->getSelectedQuestions($this->module_srl, $category_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('document_list', $output->data);
                    break;
				 case 'score' :
                        $output = $oKinModel->getScoreNotSelectedQuestions($this->module_srl, $category_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('document_list', $output->data);
                    break;
                case 'my_questions' :
                        $output = $oKinModel->getMyQuestions($this->module_srl, $category_srl, $logged_info->member_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('document_list', $output->data);
                    break;
                case 'my_replies' :
                        $output = $oKinModel->getMyReplies($this->module_srl, $logged_info->member_srl, $category_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('reply_list', $output->data);
                    break;
				  case 'popular' :
                        $output = $oKinModel->getPopularQuestions($this->module_srl, $category_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('document_list', $output->data);
                    break;
				  case 'popular_answers' :
                        $output = $oKinModel->getPopularReplies($this->module_srl, $category_srl, $this->list_count, $page, $search_keyword, $category_childs);
                        Context::set('reply_list', $output->data);
                    break;
				case 'all' :
						$obj->module_srl = $this->module_srl;
                        $obj->page = $page;
                        $obj->category_srl = $category_srl;
                        $obj->list_count = $this->list_count;
                        if($search_keyword) {
                            $obj->search_target = 'title_content';
                            $obj->search_keyword = $search_keyword;
                        }
                        $output = $oDocumentModel->getDocumentList($obj);
                        Context::set('document_list', $output->data);
                    break;
                default :
                        $obj->module_srl = $this->module_srl;
                        $obj->page = $page;
                        $obj->category_srl = $category_srl;
                        $obj->list_count = $this->list_count;
                        if($search_keyword) {
                            $obj->search_target = 'title_content';
                            $obj->search_keyword = $search_keyword;
                        }
                        $output = $oDocumentModel->getDocumentList($obj);
                        Context::set('document_list', $output->data);
                    break;
            }
            Context::set('page_navigation', $output->page_navigation);

            //trans $output->data object to array $output->data:question or replies and their point
            if(count($output->data)) {
                $document_srls = array();
                foreach($output->data as $key => $val) {
                    if($val->document_srl) $document_srls[] = $val->document_srl;
                }
                if(count($document_srls)) {
                    $point_args->document_srls = implode(',',$document_srls);
                    $output = executeQueryArray('kin.getKinPoints', $point_args);
                    if($output->data) {
                        $document_kins = array();
                        foreach($output->data as $key => $val) {
                            $document_kins[$val->document_srl] = $val->point;
                        }
                        Context::set('document_kins', $document_kins);
                    }
                }
            }

            //get user point by date last week
			/*
            $startTime = time()-86400*7;
            $endTime = time();
            $topRes['lastWeek'] = $oKinModel->getTopKinPoints(5, $startTime, $endTime);

            //get user point by date last month
            $startTime = strtotime('last Month');
            $endTime = time();
            $topRes['lastMonth'] = $oKinModel->getTopKinPoints(5, $startTime, $endTime);

            //get user point by date last year
            $startTime = strtotime('last Year');
            $endTime = time();
            $topRes['lastYear'] = $oKinModel->getTopKinPoints(5, $startTime, $endTime);

			// total point ranking
			$topRes['totalPoint'] = $oKinModel->getTopKinPoints(5);

			//get login user point
            $topRes['userPoint'] = current($oKinModel->getTotalKinPoint(1,$logged_info->member_srl));

            Context::set('document_top', $topRes);

			// get top 5 popular question
			$output = $oKinModel->getPopularQuestions($this->module_srl, null, 5);
			$popular_list = $output->data;
			Context::set('popular_list', $popular_list);

			$security = new security();
			$security->encodeHTML('document_top...');
			*/
        }

		//view the question and replies list
        function dispKinView() {
            $oModuleModel = &getModel('module');
            $oDocumentModel = &getModel('document');
			$oCommentModel = &getModel('comment');
            $oKinModel = &getModel('kin');

            $oDocument = $oDocumentModel->getDocument(Context::get('document_srl'));
            if(!$oDocument->isExists()) return new Object(-1, 'msg_document_is_null');

            $module_point_config = $oModuleModel->getModulePartConfig('point', $this->module_srl);
            $min_point = $module_point_config['insert_document'];

            Context::addBrowserTitle($oDocument->getTitleText());
            $oDocument->updateReadedCount();

            $point = $oKinModel->getKinPoint($oDocument->document_srl);
            $oDocument->add('point', $point);
            Context::set('oDocument', $oDocument);

            $parent_srls = array($oDocument->document_srl);
            if(count($oDocument->getCommentCount())) {
                $replies = $oDocument->getComments();
                if(count($replies)) {
                    foreach($replies as $key => $val) $parent_srls[] = $val->comment_srl;
                }
            }
            $replies_count = $oKinModel->getKinCommentCount($parent_srls);
            Context::set('replies_count', $replies_count);

            Context::set('category_srl', $oDocument->get('category_srl'));
            Context::set('selected_reply', $oKinModel->getSelectedReply($oDocument->document_srl));

            $point_config = $oModuleModel->getModuleConfig('point');
            Context::set('point_name', $point_config->point_name?$point_config->point_name:'point');
            Context::set('document_point', $point);

			// arrange answer sorting
			$a_target = Context::get('a_target')?Context::get('a_target'):'all';

			switch($a_target){
				case 'all':
						$answer_list = $oDocument ->getComments();
					break;
				case 'accepted':
						$accepted_answer_srl = $oKinModel->getSelectedReply($oDocument->document_srl);
						if(!$accepted_answer_srl) $answer_list = null;
						else $answer_list[] = $oCommentModel->getComment($accepted_answer_srl);
					break;
				case 'vote':
						 $answer_list = $oKinModel->getDocumentRepliesBySort($this->module_srl, $oDocument->document_srl, 'voted_count');
					break;
				default:
						$answer_list = $oDocument ->getComments();
					break;
			}

			Context::set('answer_list', $answer_list);

            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');
        }

		//to ask question
        function dispKinWrite() {
            $oDocumentModel = &getModel('document');
            $oKinModel = &getModel('kin');
            $oPointModel = &getModel('point');
            $oModuleModel = &getModel('module');

            if(!$this->grant->write_document) return new Object(-1,'msg_not_permitted');

            $logged_info = Context::get('logged_info');
            $point_config = $oModuleModel->getModuleConfig('point');
            $module_point_config = $oModuleModel->getModulePartConfig('point', $this->module_srl);
            Context::set('point_name', $point_config->point_name?$point_config->point_name:'point');
            Context::set('user_point', $user_point = $oPointModel->getPoint($logged_info->member_srl, true));
            Context::set('min_point', $min_point = $module_point_config['insert_document']);

            $max_point = $this->module_info->limit_give_point;
            if(!$max_point) $max_point = 100;
			$max_point = intval($min_point) > intval($max_point) ? $max_point:$min_point;

			$document_srl = Context::get('document_srl');
            $oDocument = $oDocumentModel->getDocument($document_srl);

            if(!$oDocument->isExists()) {
                if($user_point < $min_point) {
                    if($this->module_info->limit_write == 'Y') return new Object(-1,'msg_limit_write');
                    else {
                        Context::set('min_point', 0);
                    }
                }
                $oDocument->add('module_srl', $this->module_srl);
                $oDocument->add('title', Context::get('title'));
            } else {
                $point = $oKinModel->getKinPoint($oDocument->document_srl);
                $oDocument->add('point', $point);
            }

			Context::set('min_point', 0);
			Context::set('max_point', intval($max_point));
			if($this->module_info->limit_write == 'Y')
				$limit_point = intval($user_point)>intval($this->module_info->limit_give_point)? $this->module_info->limit_give_point:$user_point;
			else
				$limit_point = $user_point;

			Context::set('limit_point', $limit_point);
            Context::set('oDocument', $oDocument);
        }

        function dispKinReply() {
            $oDocumentModel = &getModel('document');
            $oCommentModel = &getModel('comment');

            if(!$this->grant->write_reply) return new Object(-1,'msg_not_permitted');

            $document_srl = Context::get('document_srl');
            $oDocument = $oDocumentModel->getDocument($document_srl);
            if(!$oDocument->isExists()) return new Object(-1,'msg_invalid_request');

            $oComment = $oCommentModel->getComment(0);
            $oComment->add('module_srl', $this->module_srl);
            $oComment->add('document_srl', $document_srl);
            $oComment->add('comment_srl', getNextSequence());
            Context::set('oDocument', $oDocument);
            Context::set('oReply', $oComment);
        }

        function dispKinModifyReply() {
            $oCommentModel = &getModel('comment');

            $comment_srl = Context::get('comment_srl');
            if(!$comment_srl) return new Object(-1,'msg_invalid_request');

            $oReply = $oCommentModel->getComment($comment_srl);
            if(!$oReply->isExists() || !$oReply->isGranted()) return new Object(-1,'msg_not_permitted');

            Context::set('oReply', $oReply);
        }

		//view point ranking information
        function dispKinPointRank() {
			return;
            $logged_info = Context::get('logged_info');
            $oKinModel = &getModel('kin');

			$rank_target = Context::get('rtarget')?Context::get('rtarget'):'total';
			Context::set('rtarget',$rank_target);
			$limit_count = 20;
			$page = Context::get('page')?Context::get('page'):1;
            Context::set('page',$page);
	
			$search_keyword = Context::get('search_keyword');
			$output  = null;
			switch($rank_target){
				case 'total':
					$output = $oKinModel->getTopKinPoints($limit_count , null, null, null, $page, 1, $search_keyword);
					break;
				case 'week':
					$startTime = time()-86400*7;
					$endTime = time();
					$output = $oKinModel->getTopKinPoints($limit_count , $startTime, $endTime, null, $page, 1, $search_keyword);
					break;
				case 'month':
					$startTime = strtotime('last Month');
					$endTime = time();
					$output = $oKinModel->getTopKinPoints($limit_count , $startTime, $endTime, null, $page, 1, $search_keyword);
					break;
				case 'annual':
					$startTime = strtotime('last Year');
					$endTime = time();
					$output = $oKinModel->getTopKinPoints($limit_count , $startTime, $endTime, null, $page, 1, $search_keyword);
					break;
				default:
					$output = $oKinModel->getTopKinPoints($limit_count , null, null, null, $page, 1, $search_keyword);
					break;
			}

			$member_point_rank = $output->data;
			if($member_point_rank){
				foreach($member_point_rank as $key => $member){
					$question_count = $oKinModel->getMemberQuestionsCount($this->module_srl, $member->member_srl);
					$answer_count = $oKinModel->getMemberAnswerCount($this->module_srl, $member->member_srl);
					$accepted_count = $oKinModel->getMemberAcceptedAnswerCount($this->module_srl, $member->member_srl);
					$member_point_rank[$key]->question_count = $question_count;
					$member_point_rank[$key]->answer_count = $answer_count;
					$member_point_rank[$key]->accepted_count = $accepted_count;
				}
			}

			Context::set('page_navigation', $output->page_navigation);			
			Context::set('member_point_rank', $member_point_rank);

			//get user point by date last week
            $startTime = time()-86400*7;
            $endTime = time();
            $topRes['lastWeek'] = $oKinModel->getTopKinPoints(5, $startTime, $endTime);

			// total point ranking
			$topRes['totalPoint'] = $oKinModel->getTopKinPoints(5);

			//get login user point
            $topRes['userPoint'] = current($oKinModel->getTotalKinPoint(1,$logged_info->member_srl));

			Context::set('document_top', $topRes);

			// get top 5 popular question
			$output = $oKinModel->getPopularQuestions($this->module_srl, null, 5);
			$popular_list = $output->data;
			Context::set('popular_list', $popular_list);

			$security = new security();
			$security->encodeHTML('document_top...');
			$security->encodeHTML('member_point_rank..');
		}

    }
?>
