<?php
    /**
     * @class  issuetrackerModel
     * @author haneul (developers@xpressengine.com)
     * @brief  issuetracker 모듈의 model class
     **/

    require_once(_XE_PATH_.'modules/issuetracker/issuetracker.item.php');

    function _compare($a, $b)
    {
        if(!$a->date || !$b->date) return 0;
        $res = strcmp($a->date, $b->date) * -1;
        if($res == 0)
        {
            if(!$a->revision || !$b->revision) return 0;
            else if($a->revision == $b->revision) return 0;
            else return ($a->revision > $b->revision)?-1:1;
        }
        return $res; 
    }

    class issuetrackerModel extends issuetracker {
        var $oSvn = null;

        function init() {
        }

		function getIssueCountByStatus($milestone_srl, $module_srl)
		{
			$args->milestone_srl = $milestone_srl;
			$args->module_srl = $module_srl;
			$output = executeQueryArray("issuetracker.getIssueCountByStatus", $args);
			$issues = array();
			if($output->data)
			{
				foreach($output->data as $data)
				{
					$issues[$data->status] = $data->count;
				}
			}
			$issues['total'] = $issues['new']+$issues['assign']+$issues['resolve']+$issues['reopen']+$issues['reviewing'];
			return $issues;
		}

		function getIssueCount($module_srl)
		{
			if(!$module_srl) return 0;
			$args->module_srl = $module_srl;
			$output = executeQuery("issuetracker.getIssueCount", $args);
			if(!$output->data) return 0;
			return $output->data->count;
		}

		function getChangesetCount($module_srl)
		{
			if(!$module_srl) return 0;
			$args->module_srl = $module_srl;
			$output = executeQuery("issuetracker.getChangesetCount", $args);
			if(!$output->data) return 0;
			return $output->data->count;
		}

		function getOldestIssue($module_srl)
		{
			if(!$module_srl) return NULL;
			$args->module_srl = $module_srl;
			$output = executeQuery("issuetracker.getOldestIssue", $args);
			if(!$output->data) return NULL;
			return $output->data->regdate;
		}

		function getOldestChange($module_srl)
		{
			if(!$module_srl) return NULL;
			$args->module_srl = $module_srl;
			$output = executeQuery("issuetracker.getOldestChange", $args);
			if(!$output->data) return NULL;
			return $output->data->regdate;
		}

        function &getProjectInfo($module_srl) {
            static $projectInfo = array();
            if(!isset($projectInfo[$module_srl])) {
                $projectInfo[$module_srl]->milestones = $this->getList($module_srl, 'Milestones');
                $projectInfo[$module_srl]->priorities = $this->getList($module_srl, 'Priorities');
                $projectInfo[$module_srl]->types = $this->getList($module_srl, 'Types');
                $projectInfo[$module_srl]->components = $this->getList($module_srl, 'Components');
                $projectInfo[$module_srl]->packages = $this->getList($module_srl, 'Packages');
                $projectInfo[$module_srl]->releases = $this->getModuleReleases($module_srl);
            }
            return $projectInfo[$module_srl];
        }

        function getIssue($document_srl=0, $is_admin = false, $load_extra_vars=true) {
            if(!$document_srl) return new issueItem();

            if(!$GLOBALS['__IssueItem__'][$document_srl]) {
                $oIssue = new issueItem($document_srl, $load_extra_vars);
                if($is_admin) $oIssue->setGrant();
                $GLOBALS['__IssueItem__'][$document_srl] = $oIssue;
            }

            return $GLOBALS['__IssueItem__'][$document_srl];
        }

        function getIssuesCount($module_srl,$target, $value, $status = null) {
            $args->module_srl = $module_srl;
            $args->{$target} = $value;
            if($status !== null) $args->status = $status;
            $output = executeQuery('issuetracker.getIssuesCount', $args);
            if(!$output->toBool() || !$output->data) return -1;
            return $output->data->count;
        }

		function populateIssues($target_srls)
		{
			if(!is_array($target_srls) || !count($target_srls)) return array();
			$args->target_srl = implode(",", $target_srls);
			$output = executeQueryArray("issuetracker.getIssues", $args);
			$issues = array();
			if($output->data)
			{
				foreach($output->data as $issue)
				{
					$oIssue = $this->getIssue(0);
					$oIssue->setAttribute($issue, false);
					$issues[$issue->target_srl] = $oIssue;
				}
			}
			return $issues;
		}

		function getIssuetrackerMoreChangesets() {
			$template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
			$enddate = Context::get('lastdatetime');
			$displayedDate = Context::get('displayed_date');
            $targets = Context::get('targets');
            if($targets && !is_array($targets))
            {
                $targets = explode(",", $targets);
            }
			$startdate = Context::get('startdate');
            if(!$targets || !is_array($targets) || !count($targets))
            {
                $targets = array('issue_created', 'issue_changed', 'commit');
                Context::set('targets', $targets);
            }
			$search_value = Context::get('search_value');
			$res = $this->getChangesets($this->module_srl, $startdate, $enddate, 20, $targets, $search_value );
			$changesets = $res->data;
			$target_srls = array();
            foreach($changesets as $changeset)
            {
                if(!$changeset->target_srl) continue;
				$target_srls[] = $changeset->target_srl;
            }
			$issues = $this->populateIssues($target_srls);
            Context::set('issues', $issues);
			$count = 0;
			foreach($changesets as $changeset)
			{
				if(zdate($changeset->date, "Y.m.d") == $displayedDate)
				{
					$count ++;
				}
				else 
				{
					break;	
				}
			}
			$templateHandler = new TemplateHandler();
			if($count > 0)
			{
				$lastChangesets = array_slice($changesets, 0, $count);
				Context::set('lastChangesets', $lastChangesets);
				$lastItems = $templateHandler->compile($template_path, "changesets.html");
				$this->add('lastitems', $lastItems);
				$changesets = array_slice($changesets, $count);
			}
			if($res->lastdate) $this->add('lastdate', $res->lastdate);
			if(count($changesets > 0))
			{
				Context::set('changesets', $changesets);
				$changesetsCompiled = $templateHandler->compile($template_path, "changesets.html");
				$this->add("changesets", $changesetsCompiled);
			}
		}

		function getIssuetrackerMoreLog() {
            require_once($this->module_path.'classes/svn.class.php');
            if(!$this->module_info->svn_cmd) $this->module_info->svn_cmd = '/usr/bin/svn';
            $oSvn = new Svn($this->module_info->svn_url, $this->module_info->svn_cmd, $this->module_info->svn_userid, $this->module_info->svn_passwd);
			$erev = Context::get('lastrev');
			$path = Context::get('path'); 
			$logs = $oSvn->getLog($path, $erev, $brev, false, 21);
			if(!$logs) $logs = array();
			foreach($logs as $key => $val)
			{
				if($val->paths[0]->path)
				{
					$p = $val->paths[0]->path;
				}
				else
				{
					$p = $path;
				}
				$logs[$key]->p = urlencode($p);
				$logs[$key]->msg = htmlspecialchars($logs[$key]->msg);
			}
			$this->add('logs', $logs);
		}

        function getIssueList($args) {
            // 기본으로 사용할 query id 지정 (몇가지 검색 옵션에 따라 query id가 변경됨)
            $query_id = 'issuetracker.getIssueList';

            // 검색 옵션 정리
            if($args->search_target && $args->search_keyword) {
                switch($args->search_target) {
                    case 'title' :
                    case 'content' :
                            if($args->search_keyword) $args->search_keyword = str_replace(' ','%',$args->search_keyword);
                            $args->{"s_".$args->search_target} = $args->search_keyword;
                        break;
                    case 'title_content' :
                            if($args->search_keyword) $args->search_keyword = str_replace(' ','%',$args->search_keyword);
                            $args->s_title = $args->search_keyword;
                            $args->s_content = $args->search_keyword;
                        break;
                    case 'user_id' :
                            if($args->search_keyword) $args->search_keyword = str_replace(' ','%',$args->search_keyword);
                            $args->s_user_id = $args->search_keyword;
                        break;
                    case 'user_name' :
                    case 'nick_name' :
                            $args->{"s_".$args->search_target} = $args->search_keyword;
                        break;
                    case 'member_srl' :
                            $args->{"s_".$args->search_target} = (int)$args->search_keyword;
                        break;
                    case 'tag' :
                            $args->s_tags = str_replace(' ','%',$args->search_keyword);
                            $query_id = 'issuetracker.getIssueListWithinTag';
                        break;
                    default :
                            preg_match('/^extra_vars([0-9]+)$/',$args->search_target,$matches);
                            if($matches[1]) {
                                $query_id = 'issuetracker.getIssueListWithExtraVars';
                                $args->var_idx = $matches[1];
                                $args->var_value = str_replace(' ','%',$args->search_keyword);
                            }
                        break;
                }
            }

            if(in_array($query_id, array('issuetracker.getIssueListWithinTag'))) {
                $group_args = clone($args);
                $group_output = executeQueryArray($query_id, $group_args);
                if(!$group_output->toBool()||!count($group_output->data)) return $group_output;

                foreach($group_output->data as $key => $val) {
                    if($val->document_srl) {
                        $target_srls[$key] = $val->document_srl;
                        $order_srls[$val->document_srl] = $key;
                    }
                }

                $target_args->target_srl = implode(',',$target_srls);
                $output = executeQueryArray('issuetracker.getIssues', $target_args);
                if($output->toBool() && count($output->data)) {
                    $data = $output->data;
                    $output->data = array();
                    foreach($data as $key => $val) {
                        $output->data[$order_srls[$val->document_srl]] = $val;
                    }
                    $output->total_count = $group_output->data->total_count;
                    $output->total_page = $group_output->data->total_page;
                    $output->page = $group_output->data->page;
					$output->page_navigation = $group_output->page_navigation;
                }
            } else {
                $output = executeQueryArray($query_id, $args);
            }

            // 결과가 없거나 오류 발생시 그냥 return
            if(!$output->toBool()||!count($output->data)) return $output;

            $idx = 0;
            $data = $output->data;
            unset($output->data);

            $keys = array_keys($data);
            $virtual_number = $keys[0];

            foreach($data as $key => $attribute) {
                $document_srl = $attribute->document_srl;
                $oIssue = null;
                $oIssue = new issueItem();
                $oIssue->setAttribute($attribute);
                $oIssue->setProjectInfo($attribute);
                if($is_admin) $oIssue->setGrant();

                $output->data[$virtual_number] = $oIssue;
                $virtual_number --;
            }

            return $output;
        }

        function getList($module_srl, $listname)
        {
            if(!$module_srl) return array();

            $args->module_srl = $module_srl;
            $output = executeQueryArray("issuetracker.get".$listname, $args);
            if(!$output->toBool() || !$output->data) return array();
            return $output->data;
        }

		function getHistoryValue(&$project_info, $type, $value)
		{
			if($type == "s") {
				$status_lang_list = Context::getLang('status_list');
				return $status_lang_list[$this->status_list[$value]];
			}
			else if(!$value) return $value;
			else if($type == "a")
			{
				static $member_info = array();
				if(!isset($member_info[$value])) 
				{
					$oMemberModel =& getModel('member');
					$info = $oMemberModel->getMemberInfoByMemberSrl($value);
					if($info) $member_info[$value] = $info->nick_name;
					else $member_info[$value] = "";
				}
				return $member_info[$value];
			}
			static $matching_list = array("m" => "milestones", "p" => "priorities", "t" => "types", "c" => "components", "r" => "packages",  "v" => "releases");
			static $matching_list2 = array("m" => "milestone_srl", "p" => "priority_srl", "t" => "type_srl", "c" => "component_srl", "r" => "package_srl",  "v" => "release_srl");
			foreach($project_info->{$matching_list[$type]} as $item)
			{
				if($item->{$matching_list2[$type]} == $value)
				{
					return $item->title;
				}
			}
		}

		function _populate(&$comments, $module_srl)
		{
			if(count($comments))
			{
				$oModel =& getModel('issuetracker');
				$project_info =& $oModel->getProjectInfo($module_srl);

				$comment_srls = array_keys($comments);
				$args->comment_srls = implode(",",$comment_srls);
				$output = executeQueryArray("issuetracker.getHistoryChanges", $args);
				if($output->data)
				{
					foreach($output->data as $history)
					{
						if($history->before || $history->type == "s")
						{
							$str = Context::getLang('history_format');
						}
						else $str = Context::getLang('history_format_not_source');

						$str = str_replace('[source]', $this->getHistoryValue($project_info, $history->type, $history->before), $str);
						$str = str_replace('[target]', $this->getHistoryValue($project_info, $history->type, $history->after), $str);
						$str = str_replace('[key]', Context::getLang($this->matching_list[$history->type]), $str);
						$comments[$history->comment_srl]->history[] = $str;
					}
				}
			}
		}

		function getHistories(&$oIssue)
		{
			$comments = $oIssue->getComments();
			$this->_populate($comments, $oIssue->get('module_srl'));

			return $comments;	
		}

        function getPackageList($module_srl, $package_srl=0, $each_releases_count = 0)
        {
            if(!$module_srl) return array();

            if(!$package_srl) {
                $args->module_srl = $module_srl;
                $output = executeQueryArray("issuetracker.getPackages", $args);
            } else {
                $args->package_srl = $package_srl;
                $output = executeQueryArray("issuetracker.getPackages", $args);
            }
            if(!$output->toBool() || !$output->data) return array();

            $packages = array();
            foreach($output->data as $package) {
                $package->release_count = $this->getReleaseCount($package->package_srl);
                $package->releases = $this->getReleaseList($package->package_srl, $each_releases_count);
                $packages[$package->package_srl] = $package;
            }

            return $packages;
        }

        function getReleaseCount($package_srl) {
            if(!$package_srl) return 0;

            $args->package_srl = $package_srl;
            $output = executeQuery("issuetracker.getReleaseCount", $args);
            return $output->data->count;
        }

        function getModuleReleases($module_srl) {
            if(!$module_srl) return array();

            $args->module_srl = $module_srl;
            $output = executeQueryArray("issuetracker.getReleases", $args);
            if(!$output->toBool() || !$output->data) return array();
            return $output->data;
        }

        function getReleasesWithPackageTitle($module_srl) {
            if(!$module_srl) return array();
            $args->module_srl = $module_srl;
            $output = executeQueryArray("issuetracker.getReleasesWithPackage", $args);
            if(!$output->toBool() || !$output->data) return array();
            return $output->data;
        }

        function getReleaseList($package_srl, $list_count =0) {
            if(!$package_srl) return array();

            $args->package_srl = $package_srl;

            if($list_count ) {
                $args->list_count = $list_count;
                $output = executeQueryArray("issuetracker.getReleaseList", $args);
            } else {
                $output = executeQueryArray("issuetracker.getReleases", $args);
            }
            if(!$output->toBool() || !$output->data) return array();

            $list = $output->data;
            $output = array();
            $oFileModel = &getModel('file');
            foreach($list as $release) {
                $files = $oFileModel->getFiles($release->release_srl);
                $release->files = $files;
                $output[$release->release_srl] = $release;
            }
            return $output;
        }

        function getPriorityCount($module_srl)
        {
            if(!$module_srl) return -1;
            $args->module_srl = $module_srl;
            $output = executeQuery("issuetracker.getPriorityCount", $args);
            if(!$output->toBool()) return -1;
            else return $output->data->count;
        }

        function getPriorityMaxListorder($module_srl)
        {
            if(!$module_srl) return -1;
            $args->module_srl = $module_srl;
            $output = executeQuery("issuetracker.getPriorityMaxListorder", $args);
            if(!$output->toBool()) return -1;
            else return $output->data->count;
        }

        function getMilestone($milestone_srl)
        {
            $args->milestone_srl = $milestone_srl;
            $output = executeQuery("issuetracker.getMilestone", $args);
            return $output;
        }

        function getCompletedMilestone($module_srl)
        {
            $args->module_srl = $module_srl;
            $args->is_completed = 'Y';
            $output = executeQueryArray("issuetracker.getMilestones", $args);
            if(!$output->toBool())
            {
                return array();
            }

            if(!$output->data)
            {
                return array();
            }
            return $output->data;
        }

        function getPriority($priority_srl)
        {
            $args->priority_srl = $priority_srl;
            $output = executeQuery("issuetracker.getPriority", $args);
            return $output;
        }

        function getType($type_srl)
        {
            $args->type_srl = $type_srl;
            $output = executeQuery("issuetracker.getType", $args);
            return $output;
        }

        function getComponent($component_srl)
        {
            $args->component_srl = $component_srl;
            $output = executeQuery("issuetracker.getComponent", $args);
            return $output;
        }

        function getPackage($package_srl)
        {
            $args->package_srl = $package_srl;
            $output = executeQuery("issuetracker.getPackage", $args);
            if(!$output->toBool()||!$output->data) return;
            return $output->data;
        }

        function getRelease($release_srl)
        {
            $args->release_srl = $release_srl;
            $output = executeQuery("issuetracker.getRelease", $args);
            if(!$output->toBool()||!$output->data) return;
            $release = $output->data;
            $oFileModel = &getModel('file');
            $files = $oFileModel->getFiles($release->release_srl);
            if($files) $release->files = $files;
            return $release;
        }

        function getGroupMembers($module_srl, $grant_name) {
            $args->module_srl = $module_srl;
            $args->name = $grant_name;
            $output = executeQueryArray('issuetracker.getGroupMembers', $args);
            return $output->data;
        }

        function getLatestRevision($module_srl) {
            $args->module_srl = $module_srl;
            $output = executeQuery('issuetracker.getLatestRevision', $args);
            if($output->data && $output->data->revision)
            {
                return $output->data->revision;
            }
            else return 0;
        }

        function _linkDocument($matches) {
            $document_srl = $matches[1];
            return sprintf('<a href="%s" onclick="window.open(this.href); return false;">#%d</a> ', getUrl('','document_srl',$document_srl), $document_srl);
        }

        function _linkXE($message)
        {
            return preg_replace_callback('/^\#?([0-9]+)( |\:)/', array($this, '_linkDocument'), $message);
        }

        function getChangesets($module_srl, $startdate=null, $enddate = null, $list_count = 20, $targets, $search_value = null)
        {
            if(!$enddate)
            {
                $enddate = date("Ymd");
				$args->enddate = date("Ymd", ztime($enddate)+24*60*60);
            }
			else
			{
				$enddate = str_replace(array("-","/"), array("",""), $enddate);
				if(strlen($enddate) <= 8)
				{
					$args->enddate = date("Ymd", ztime($enddate)+24*60*60);
				}
				else
				{
					$args->enddate = $enddate;
				}
			}

			if($startdate)
			{
				$args->startdate = str_replace(array("-","/"), array("",""), $startdate);
			}

			$oMemberModel =& getModel('member');
			if($search_value)
			{
				$member_srl = $oMemberModel->getMemberSrlByNickName($search_value);
				if($member_srl)
				{
					$member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);
					$args->member_srl = $member_info->member_srl;
				}
				$args->nick_name = $args->author = $search_value;
				$args->id = $search_value;
				if(substr($args->id, 0, 1) == "#") $args->id = substr($args->id, 1);
				$args->id = intval($args->id);
				if(!$args->id) unset($args->id);
			}


            $args->module_srl = $module_srl;
			$args->list_count = $list_count+1;
            if(in_array('commit', $targets))
            {
                $output = executeQueryArray("issuetracker.getChangesets", $args);
                if(!$output->toBool()) return array();
                if(!$output->data) $output->data = array();

                // message에 htmlspecialchars() 적용
                foreach($output->data as $key => $changeset)
				{
					if($changeset->member_srl)
					{
						$member_info = $oMemberModel->getMemberInfoByMemberSrl($changeset->member_srl);
						if($member_info)
						{
							$changeset->author = $member_info->nick_name;
						}
					}
                    $changeset->message = $this->_linkXE(htmlspecialchars($changeset->message));
				}
            }

            if(in_array('issue_changed', $targets))
            {
                $solvedHistory = array();
                $output2 = executeQueryArray("issuetracker.getHistories", $args);
				if($output2->data)
				{
					$comments = array();
					foreach($output2->data as $item)
					{
						$comments[$item->comment_srl] = $item;
					}
					$this->_populate($comments, $module_srl);
					foreach($comments as $history)
					{
						if(!$history->history) continue;
						$res = "";
						$obj = null;
                        $obj->date = $history->regdate;
                        $obj->type = "changed";
                        $obj->message = implode("<br />", $history->history); 
                        $obj->target_srl = $history->document_srl;
                        $obj->author = $history->nick_name;
                        $output->data[] = $obj;
					}
				}
            }

            if(in_array('issue_created', $targets))
            {
                $output2 = executeQueryArray("issuetracker.getDocumentListForChangeset", $args);
                if(count($output2->data)) {
                    foreach($output2->data as $history)
                    {
                        $obj = null;
                        $obj->date = $history->regdate;
                        $obj->type = "created";
                        $obj->author = $history->nick_name;
                        $obj->target_srl = $history->document_srl;
                        $output->data[] = $obj;
                    }
                }
            }

            usort($output->data, _compare);
			$output->data = array_slice($output->data, 0, $list_count+1);
			$res = null;
			if(count($output->data) > $list_count)
			{
				$lastitem = array_pop($output->data);
				$res->lastdate = $lastitem->date;
			}
			$res->data = $output->data;

            return $res;
        }
    }
?>
