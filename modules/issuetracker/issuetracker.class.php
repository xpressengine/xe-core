<?PHP
    /**
     * @class  issuetracker
     * @author NHN (developers@xpressengine.com)
     * @brief  base class for the issue tracker
     **/

    require_once(_XE_PATH_.'modules/issuetracker/issuetracker.item.php');

    class issuetracker extends ModuleObject
    {
        // 검색 대상 지정
        var $search_option = array('title','content','title_content','user_name','nick_name','user_id','tag');

        // 이슈 목록 노출 대상
        var $display_option = array('issue_id','status','title','milestone','priority','type','component','package','occured_version','assignee', 'writer');
        var $default_enable = array('issue_id','title','status','assignee','writer');
		var $status_list = array('new', 'reviewing', 'assign', 'resolve', 'reopen', 'postponed', 'duplicated', 'invalid');
		var $matching_list = array("m" => "milestone", "p" => "priority", "t" => "type", "c" => "component", "r" => "package",  "v" => "occured_version", "s" => "status", "a" => "assignee");

        function moduleInstall()
        {
            // 아이디 클릭시 나타나는 팝업메뉴에 작성글 보기 기능 추가
            $oModuleController = &getController('module');
            $oModuleController->insertTrigger('member.getMemberMenu', 'issuetracker', 'controller', 'triggerMemberMenu', 'after');
            $oModuleController->insertTrigger('document.deleteDocument', 'issuetracker', 'controller', 'triggerDeleteDocument', 'after');

            $oDB = &DB::getInstance();
            $oDB->addIndex("issue_changesets","idx_unique_revision", array("module_srl","revision"), true);

            // 히스토리(=댓글) 첨부파일 활성화 트리거
            $oModuleController->insertTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentCheckAttached', 'before');
            $oModuleController->insertTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentAttachFiles', 'after');

            // movemodule trigger
            $oModuleController->insertTrigger('document.moveDocumentModule', 'issuetracker', 'controller', 'triggerMoveDocumentModule', 'after');
        }

		function moduleUninstall()
		{
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

			// module delete
			$output = executeQueryArray("issuetracker.getAllIssuetracker");
			if($output->data) {
				set_time_limit(0);
				foreach($output->data as $issuetracker)
				{
					$oModuleController->deleteModule($issuetracker->module_srl);
				}
			}
			
            if($oModuleModel->getTrigger('member.getMemberMenu', 'issuetracker', 'controller', 'triggerMemberMenu', 'after')) {
                $oModuleController->deleteTrigger('member.getMemberMenu', 'issuetracker', 'controller', 'triggerMemberMenu', 'after');
            }
            if($oModuleModel->getTrigger('document.deleteDocument', 'issuetracker', 'controller', 'triggerDeleteDocument', 'after')) {
                $oModuleController->deleteTrigger('document.deleteDocument', 'issuetracker', 'controller', 'triggerDeleteDocument', 'after');
            }

            if($oModuleModel->getTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentCheckAttached', 'before')) {
                $oModuleController->deleteTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentCheckAttached', 'before');
            }
            if($oModuleModel->getTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentAttachFiles', 'after')) {
                $oModuleController->deleteTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentAttachFiles', 'after');
            }
            if($oModuleModel->getTrigger('document.moveDocumentModule', 'issuetracker', 'controller', 'triggerMoveDocumentModule', 'after')) {
                $oModuleController->deleteTrigger('document.moveDocumentModule', 'issuetracker', 'controller', 'triggerMoveDocumentModule', 'after');
            }
			if($oModuleModel->getTrigger("comment.deleteComment", "issuetracker", "controller", "triggerDeleteComment", "after")) 
			{
				$oModuleController->deleteTrigger("comment.deleteComment", "issuetracker", "controller", "triggerDeleteComment", "after"); 
			}
			return new Object();
		}

        function checkUpdate()
        {
            $oModuleModel = &getModel('module');
            $oDB = &DB::getInstance();

            // 아이디 클릭시 나타나는 팝업메뉴에 작성글 보기 기능 추가
            if(!$oModuleModel->getTrigger('member.getMemberMenu', 'issuetracker', 'controller', 'triggerMemberMenu', 'after')) return true;
            if(!$oModuleModel->getTrigger('document.deleteDocument', 'issuetracker', 'controller', 'triggerDeleteDocument', 'after')) return true;

            // 히스토리(=댓글) 첨부파일 활성화 트리거
            if(!$oModuleModel->getTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentCheckAttached', 'before')) return true;
            if(!$oModuleModel->getTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentAttachFiles', 'after')) return true;
            if(!$oDB->isColumnExists('issues_history', 'uploaded_count')) return true;

            if(!$oModuleModel->getTrigger('document.moveDocumentModule', 'issuetracker', 'controller', 'triggerMoveDocumentModule', 'after')) return true;
            if(!$oDB->isColumnExists('issue_changesets', 'member_srl')) return true;
			$output = executeQuery("issuetracker.getHistoriesToChange");
			if($output->data) return true;

			if(!$oModuleModel->getTrigger("comment.deleteComment", "issuetracker", "controller", "triggerDeleteComment", "after")) return true;
			if(!$oDB->isIndexExists("issue_history_change", "unique_comment_type")) return true;

            return false;
        }

        function moduleUpdate() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oDB = &DB::getInstance();

            // 아이디 클릭시 나타나는 팝업메뉴에 작성글 보기 기능 추가
            if(!$oModuleModel->getTrigger('member.getMemberMenu', 'issuetracker', 'controller', 'triggerMemberMenu', 'after')) {
                $oModuleController->insertTrigger('member.getMemberMenu', 'issuetracker', 'controller', 'triggerMemberMenu', 'after');
            }
            if(!$oModuleModel->getTrigger('document.deleteDocument', 'issuetracker', 'controller', 'triggerDeleteDocument', 'after')) {
                $oModuleController->insertTrigger('document.deleteDocument', 'issuetracker', 'controller', 'triggerDeleteDocument', 'after');
            }

            // 히스토리(=댓글) 첨부파일 활성화 트리거
            if(!$oModuleModel->getTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentCheckAttached', 'before')) {
                $oModuleController->insertTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentCheckAttached', 'before');
            }
            if(!$oModuleModel->getTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentAttachFiles', 'after')) {
                $oModuleController->insertTrigger('issuetracker.insertHistory', 'file', 'controller', 'triggerCommentAttachFiles', 'after');
            }
            if(!$oDB->isColumnExists('issues_history', 'uploaded_count')) {
                $oDB->addColumn('issues_history', 'uploaded_count', 'number', 11, 0);
            }

            if(!$oModuleModel->getTrigger('document.moveDocumentModule', 'issuetracker', 'controller', 'triggerMoveDocumentModule', 'after')) {
                $oModuleController->insertTrigger('document.moveDocumentModule', 'issuetracker', 'controller', 'triggerMoveDocumentModule', 'after');
            }

            if(!$oDB->isColumnExists('issue_changesets', 'member_srl')) { 
                $oDB->addColumn('issue_changesets', 'member_srl', 'number', 11, 0);
				$output = executeQueryArray("issuetracker.getAuthors");
				$oMemberModel =& getModel('member');
				foreach($output->data as $data)
				{
					$member_info = $oMemberModel->getMemberInfoByUserID($data->author);
					if(!$member_info) continue;

					$args = null;
					$args->member_srl = $member_info->member_srl;
					$args->author = $data->author;
					$output2 = executeQuery("issuetracker.updateMemberSrl", $args);
				}
			}

			if(!$oDB->isIndexExists("issue_history_change", "unique_comment_type")) {
				$oDB->addIndex("issue_history_change", "unique_comment_type", array("comment_srl", "type"), true);
			}

			$output = executeQuery("issuetracker.getHistoriesToChange");
			if($output->data)
			{
				set_time_limit(0);
				$matching_list = array("milestone" => "m", "priority" => "p", "type" => "t", "component" => "c", "package" => "r", "occured_version" => "v", "status" => "s", "assignee" => "a");
				$res = FileHandler::readDir($this->module_path."lang/");
				$status_list = array();
				$rev_st_list = array();
				foreach($this->status_list as $key=>$val)
				{
					$rev_st_list[$val] = $key;
				}
				foreach($res as $file)
				{
					$path = $this->module_path."lang/".$file;
					include($path);
					foreach($lang->status_list as $key=>$val)
					{
						$status_list[$val] = $rev_st_list[$key];
					}
				}
				$milestone = array();
				$output = executeQueryArray("issuetracker.getMilestones");
				if(!$output->data) $output->data = array();
				foreach($output->data as $milestone_item)
				{
					$milestone[$milestone_item->module_srl][$milestone_item->title] = $milestone_item->milestone_srl;
				}

				$component = array();
				$output = executeQueryArray("issuetracker.getComponents");
				if(!$output->data) $output->data = array();
				foreach($output->data as $component_item)
				{
					$component[$component_item->module_srl][$component_item->title] = $component_item->component_srl;
				}

				$priority = array();
				$output = executeQueryArray("issuetracker.getPriorities");
				if(!$output->data) $output->data = array();
				foreach($output->data as $priority_item)
				{
					$priority[$priority_item->module_srl][$priority_item->title] = $priority_item->priority_srl;
				}

				$type = array();
				$output = executeQueryArray("issuetracker.getTypes");
				if(!$output->data) $output->data = array();
				foreach($output->data as $type_item)
				{
					$type[$type_item->module_srl][$type_item->title] = $type_item->type_srl;
				}

				$package = array();
				$output = executeQueryArray("issuetracker.getPackages");
				if(!$output->data) $output->data = array();
				foreach($output->data as $package_item)
				{
					$obj = null;
					$obj->package_srl = $package_item->package_srl;
					$output2 = executeQueryArray("issuetracker.getReleases", $obj);
					$obj->releases = array();
					if($output2->data)
					{
						foreach($output2->data as $release)
						{
							$obj->releases[$release->title] = $release->release_srl;
						}
					}
					$package[$package_item->module_srl][$package_item->title] = $obj; 
				}

				$page = 1;
				$oMemberModel =& getModel('member');
				$members = array();
				$sargs = null;
				while(true)
				{
					$args->page = 1; 
					$args->list_count = 100;
					$sargs = null;
					$output = executeQueryArray("issuetracker.getHistoriesToChange", $args);
					if(!$output->data) break;
					foreach($output->data as $history)
					{
						if(!$sargs->minsrl) {
							$sargs->minsrl = $history->issues_history_srl;
						}
						else
						{
							if($history->issues_history_srl < $sargs->minsrl) $sargs->minsrl = $history->issues_history_srl;
						}

						if(!$sargs->maxsrl) {
							$sargs->maxsrl = $history->issues_history_srl;
						}
						else
						{
							if($history->issues_history_srl > $sargs->maxsrl) $sargs->maxsrl = $history->issues_history_srl;
						}
						$list_args = null;
						$list_args->comment_srl = $history->issues_history_srl;
						$list_args->document_srl = $history->target_srl;
						$list_args->module_srl = $history->module_srl;
						$list_args->regdate = $history->regdate;
						$list_args->head = $list_args->arrange = $list_args->comment_srl;
						$list_args->depth = 0;
						$output = executeQuery('comment.insertCommentList', $list_args);
						
						$history->comment_srl = $history->issues_history_srl;
						$history->document_srl = $history->target_srl;
						if(!$history->content) $history->content = "";
						$history->last_update = $history->regdate;
						$history->ipaddress = "";
						$history->list_order = $history->comment_srl * -1;
						$output2 = executeQuery("comment.insertComment", $history);
						if(!$history->history) continue;
						$hist = unserialize($history->history);
						
						foreach($hist as $t => $changes)
						{
							$args = null;
							$args->type = $matching_list[$t];
							$args->module_srl = $history->module_srl;
							$args->comment_srl = $history->comment_srl;
							if($args->type == "v") continue;
							switch($args->type)
							{
							case "a":
								foreach($changes as $k => $v)
								{
									if($v && !isset($members[$v])) {
										$member_srl = $oMemberModel->getMemberSrlByNickName($v);
										if($member_srl) {
											$members[$v] = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
											if($members[$v]) $members[$v] = $members[$v]->member_srl; 
										}
										else $members[$v] = null;
									}
								}
								$args->before = $changes[0]?$members[$changes[0]]:null;
								$args->after = $changes[1]?$members[$changes[1]]:null;
								break;
							case "r":
								$args->before = $changes[0]?$package[$history->module_srl][$changes[0]]:null;
								$args->after = $changes[1]?$package[$history->module_srl][$changes[1]]:null;
								if($hist["occured_version"])
								{
									$args2 = clone($args);
									$args2->type = "v";
									$args2->before = ($args->before && $hist["occured_version"][0])?$args->before->releases[$hist["occured_version"][0]]:null;
									$args2->after = ($args->after && $hist["occured_version"][1])?$args->after->releases[$hist["occured_version"][1]]:null;
									if(!$args2->before) unset($args2->before);
									if(!$args2->after) unset($args2->after);
									$output2 = executeQuery("issuetracker.insertHistoryChange", $args2);
								}
								$args->before = $args->before?$args->before->package_srl:null;
								$args->after = $args->after?$args->after->package_srl:null;
								break;
							case "s":
								$args->before = $changes[0]?$status_list[$changes[0]]:null;
								$args->after = $changes[1]?$status_list[$changes[1]]:null;
								break;
							default:
								$args->before = $changes[0]?${$t}[$history->module_srl][$changes[0]]:null;	
								$args->after = $changes[1]?${$t}[$history->module_srl][$changes[1]]:null;	
							}
							if($args->before == $args->after) continue;
							if(!$args->before) unset($args->before);
							if(!$args->after) unset($args->after);
							$output2 = executeQuery("issuetracker.insertHistoryChange", $args);
						}
					}
					$output3 = executeQuery("issuetracker.deleteHistoriesRange", $sargs);
					if($page == $output->page_navigation->total_page) break;
					unset($output);
				}
			}
			if(!$oModuleModel->getTrigger("comment.deleteComment", "issuetracker", "controller", "triggerDeleteComment", "after")) 
			{
				$oModuleController->insertTrigger("comment.deleteComment", "issuetracker", "controller", "triggerDeleteComment", "after"); 
			}

            return new Object(0, 'success_updated');
        }
    }
?>
