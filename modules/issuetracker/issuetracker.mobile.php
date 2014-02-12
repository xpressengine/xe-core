<?php

require_once(_XE_PATH_.'modules/issuetracker/issuetracker.view.php');

class issuetrackerMobile extends issuetrackerView {

	function init() {
		// 템플릿에서 사용할 변수를 Context::set()
		if($this->module_srl) Context::set('module_srl',$this->module_srl);
		if(!$this->module_info->svn_cmd) $this->module_info->svn_cmd = '/usr/bin/svn';

		// 현재 호출된 게시판의 모듈 정보를 module_info 라는 이름으로 context setting
		Context::set('module_info',$this->module_info);
		
		$template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
		if(!$this->module_info->mskin || !is_dir($template_path)) {
			$this->module_info->mskin = 'default';
			$template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
		}
		$this->setTemplatePath($template_path);
		foreach($this->search_option as $opt) $search_option[$opt] = Context::getLang($opt);

		$oDocumentModel = &getModel('document');
		$extra_keys = $oDocumentModel->getExtraKeys($this->module_srl);
		Context::set('extra_keys', $extra_keys);
		if(count(Context::get('extra_keys'))) {
			foreach(Context::get('extra_keys') as $key => $val) {
				if($val->search == 'Y') $search_option['extra_vars'.$val->idx] = $val->name;
			}
		}
		Context::set('search_option', $search_option);
		$oModuleModel = &getModel('module');
		$module_config = $oModuleModel->getModulePartConfig('issuetracker',$this->module_srl);
		if($module_config) $this->default_enable = $module_config->display_option;

		// 템플릿에서 사용할 노출옵션 세팅
		foreach($this->display_option as $opt) {
			$obj = null;
			$obj->title = Context::getLang($opt);
			$checked = Context::get('d_'.$opt);
			if($opt == 'title' || $checked==1 || (Context::get('d')!=1&&in_array($opt,$this->default_enable))) $obj->checked = true;
			$display_option[$opt] = $obj;
		}
		Context::set('display_option', $display_option);

		if(!Context::get('act')) {
			if (!Context::get('document_srl')) {
				$this->act = 'dispIssuetrackerViewMilestone';
				Context::set('act','dispIssuetrackerViewMilestone');
			} else {
				$this->act = 'dispIssuetrackerViewIssue';
				Context::set('act','dispIssuetrackerViewIssue');
			}
		}
	}

	function dispIssuetrackerViewMilestone() {
		$oIssuetrackerModel = &getModel('issuetracker');
		$args->page = Context::get('page');
		if(!$args->page) 
		{
			$args->page = 1;
			Context::set('page', 1);
		}
		
		$args->module_srl = $this->module_info->module_srl;
		$output = executeQueryArray("issuetracker.getMilestonesPaged", $args);
		Context::set('page_navigation', $output->page_navigation);
		$output = $output->data;
		if(!$output) $output = array();
		$milestones = array();
		if($args->page == 1)
		{
			$notassigned = null;
			$notassigned->milestone_srl = 0;
			$notassigned->is_completed = "N";
			array_unshift($output, $notassigned);
		}

		if($output) {
			$status_list = array("new", "reviewing", "assign", "resolve", "reopen", "invalid", "duplicated", "postponed");
			foreach($output as $key => $milestone) {
				$issues = $oIssuetrackerModel->getIssueCountByStatus($milestone->milestone_srl, $this->module_srl);
				foreach($status_list as $status)
				{
					if(!$issues[$status]) $issues[$status] = 0;
				}

				$milestone->issues = $issues;
				$milestones[$milestone->milestone_srl] = $milestone;

			}
		}
		Context::set('milestones',$milestones);

		// 프로젝트 메인 페이지 출력
		$this->setTemplateFile('milestone');
	}

	function getIssuetrackerMoreChangesetsM() {
		$template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
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
			$lastItems = $templateHandler->compile($template_path, "changeset.html");
			$this->add('lastitems', $lastItems);
			$changesets = array_slice($changesets, $count);
		}
		if($res->lastdate) $this->add('lastdate', $res->lastdate);
		if(count($changesets > 0))
		{
			Context::set('changesets', $changesets);
			$changesetsCompiled = $templateHandler->compile($template_path, "changeset");
			$this->add("changesets", $changesetsCompiled);
		}
	}
}

?>
