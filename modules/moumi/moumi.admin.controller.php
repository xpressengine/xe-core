<?php
class moumiAdminController extends moumi
{
	function init()
	{
	}

	function procMoumiAdminInsertPackage()
	{
		$args = Context::gets('package_srl', 'include_news');

		$oMoumiModel = getModel('moumi');

		if($args->package_srl)
		{
			$oModel = &getModel('moumi');
			$output = $oModel->getPackage($args->package_srl);
			if(!$output->toBool()) return $output;

			$package_info = $output->get('package_info');
		}

		if($package_info)
		{
			$output = executeQuery('moumi.updatePackage', $args);
		}
		else
		{
			$output = executeQuery('moumi.insertPackage', $args);
		}
		if(!$output->toBool()) return $output;
		
		$this->setRedirectUrl(getUrl('', 'module', 'admin', 'act', 'dispMoumiAdminIndex'));
	}

	function procMoumiAdminDeletePackage()
	{
		$args->package_srl = Context::get('package_srl');
		$output = executeQuery('moumi.deletePackage', $args);
		if(!$output->toBool()) return $output;
	}

	/**
	 * @brief set promotion group member
	 */
	function cronPromotionMemberGroup($cronPath="")
	{
		if($cronPath=="")
			return false;
		require $cronPath. '/setting.php';

		$illegalData = array(
			"set_document"=>array(),
			"set_comment"=>array(),
			"word_document"=>array(),	// 글 글자수 모자람
			"word_comment"=>array(),	// 댓글 글자수 모자람
			"ip_comment"=>array(),	// 작성글 댓글 아이피 중복
			"own_doc_comment"=>array(),	// 본인글에 댓글
			"dupl_comment"=>array(),	// 하나의 글에 하나의 댓글만 인정
		);

		$oMoumiModel = getModel('moumi');

		$oMemberModel = getModel('member');
		// 관리자 권한 얻어옴. 관리자 권한으로 검색 하여 권한있는 관리자로 로그인 시킴

		// 그룹정보를 가져와 '프로모션회원'에 관한 group 정보 가져옴
		$group_list = $oMemberModel->getGroups();
		$groupInfo = new stdClass;
		foreach($group_list as $value)
		{
			if(trim($value->title) == trim($setGroupName)) {
				$groupInfo = $value;
				break;
			}
		}

		$arrMemberInfo = array();
		$arrMember = array();

		// 확인 할 documents 정보 가져옴
		$oModuleModel = getModel('module');
		$arrModuleSrl = $oModuleModel->getModuleSrlByMid($searchDocuments);

		// documents 가져옴
		$oDocumentModel = getModel('document');
		$columnList = array('document_srl', 'member_srl', 'email_address', 'nick_name', 'content');

		$args = new stdClass;

		$args->statusList = array('PUBLIC');
		$args->module_srl = $arrModuleSrl;

		$args->list_count = 100;

		for($day=0; $day<$searchDay; $day++) {
			$args->page = 1;

			//$args->s_regdate = date("Ymd", strtotime(sprintf("%s -%s days",$stdDate, $day)));

			$args->search_target = "regdate";
			$args->search_keyword = date("Ymd", strtotime(sprintf("%s -%s days",$stdDate, $day)));

			$loopPage = true;	// for break page

			while($loopPage) {

				$output = $oDocumentModel->getDocumentList($args, false, true, $columnList);
				if($output->data == false)
				{
					$loopPage = false;
					continue;
				} else {
					$args->page += 1;
				}

				foreach($output->data as $data)
				{
					$row = $data->variables;
					if($row['member_srl'] == "0")
						continue;
				
					if(isset($arrMember[$row['member_srl']])===false) 
						$arrMember[$row['member_srl']] = 0;
					
					if(!isset($arrMemberInfo[$row['member_srl']])) {
						$arrMemberInfo[$row['member_srl']] = $illegalData;
						$arrMemberInfo[$row['member_srl']]['nick_name'] = $row['nick_name'];
					}

					if($this->checkWordCount($row['content'], $checkDocumentWordCount) === false) {
						$arrMemberInfo[$row['member_srl']]['word_document'][] = array("d"=> $row['document_srl'], "str"=>$this->getCutString($row['content']));
						continue;
					}

					$arrMember[$row['member_srl']] += $pointDocument;
					$arrMemberInfo[$row['member_srl']]['set_document'][] = array("d"=> $row['document_srl'], "str"=>$this->getCutString($row['content']));
				}	// EOF foreach($ouput->data...

			}	// EOF while($loopPage)

		}	// EOF for($day..

		// 댓글 처리
		$arrModuleSrl = $oModuleModel->getModuleSrlByMid($searchComments);

		// comments 가져옴
		$oCommentModel = getModel('comment');
		$columnList = array('comment_srl', 'member_srl', 'email_address', 'nick_name', 'content','ipaddress');

		$args = new stdClass;

		$args->s_is_published = 1;
		$args->module_srl = $arrModuleSrl;

		$args->list_count = 100;

		$cacheDocument = array();
		$setPointDocument = array();
		for($day=0; $day<$searchDay; $day++) {
			$args->page = 1;
			$args->search_target = "regdate";
			$args->search_keyword = date("Ymd", strtotime(sprintf("%s -%s days",$stdDate, $day)));
			$loopPage = true;	// for break page
			while($loopPage)
			{
				$output = $oCommentModel->getTotalCommentList($args, false, true, $columnList);
				if($output->data == false)
				{
					$loopPage = false;
					continue;
				} else {
					$args->page += 1;
				}

				foreach($output->data as $data)
				{
					$row = $data->variables;
					if($row['member_srl'] == "0")
						continue;
				
					if(isset($arrMember[$row['member_srl']])===false)
						$arrMember[$row['member_srl']] = 0;

					if(!isset($arrMemberInfo[$row['member_srl']])) {
						$arrMemberInfo[$row['member_srl']] = $illegalData;
						$arrMemberInfo[$row['member_srl']]['nick_name'] = $row['nick_name'];
					}

					if($this->checkWordCount($row['content'], $checkCommentWordCount) === false) {
						$arrMemberInfo[$row['member_srl']]['word_comment'][] = array(
							"d"=>$row['document_srl'],
							"c"=>$row['comment_srl'],
							"str"=>$this->getCutString($row['content']),
						);
						continue;
					}

					if(!isset($cacheDocument[$row['document_srl']])) {
						$tmpDocument = $oDocumentModel->getDocuments($row['document_srl']);
						$cacheDocument[$row['document_srl']] = $tmpDocument;
					}

					if($row['member_srl'] == $cacheDocument[$row['document_srl']]['member_srl']) {
						$arrMemberInfo[$row['member_srl']]['own_doc_comment'][] =  array(
							"d"=>$row['document_srl'],
							"c"=>$row['comment_srl'],
							"str"=>$this->getCutString($row['content']),
						);
						continue;
					}

					if(isset($setPointDocument[$row['member_srl']]) && in_array($row['document_srl'], $setPointDocument[$row['member_srl']])) {
						$arrMemberInfo[$row['member_srl']]['dupl_comment'][] =  array(
							"d"=>$row['document_srl'],
							"c"=>$row['comment_srl'],
							"str"=>$this->getCutString($row['content']),
						);
						continue;
					}

					if($row['ipaddress'] == $cacheDocument[$row['document_srl']]['ipaddress']) {
						$arrMemberInfo[$row['member_srl']]['ip_comment'][] =  array(
							"d"=>$row['document_srl'],
							"c"=>$row['comment_srl'],
							"str"=>$this->getCutString($row['content']),
						);
						continue;
					}

					$setPointDocument[$row['member_srl']][] = $row['document_srl'];
					$arrMember[$row['member_srl']] += $pointComment;
					$arrMemberInfo[$row['member_srl']]['set_comment'][] = array("d"=>$row['document_srl'], "c"=> $row['comment_srl'],"str"=>$this->getCutString($row['content']));
				}	// EOF foreach($ouput->data...

			}	// EOF while($loopPage)
		}	// EOF for($day...

		// 등급 부여할 회원 번호 필터링
		$arrInsertGroupMember = array();
		$arrRejectGroupMember = array();
		foreach($arrMember as $key=>$value)
		{
			if($value>=$checkPoint) {
				$arrInsertGroupMember[] = $key;
				$arrMemberInfo[$key]['result'] = "insert";
			}
			else {
				$arrRejectGroupMember[] = array("m"=>$key, "p"=>$value);
				$arrMemberInfo[$key]['result'] = "reject";
				$arrMemberInfo[$key]['str'] = "Get point = " . $value;
			}
		}

		$oMemberController = getController('member');

		// delete old member group member
		$args = new stdClass;
		$args->less_regdate = date("Ymd", strtotime(sprintf("%s -%s days",$stdDate, $searchDay))) . '000000';
		$args->group_srl = $groupInfo->group_srl;
		$args->site_srl = $site_module_info->site_srl;
		$delGroupMember = $oMoumiModel->getMemberGroupMember($args);
		$arrDelGroupMember = array();
		foreach($delGroupMember as $row)
		{
			$args = new stdClass;
			$args->group_srl = $groupInfo->group_srl;
			$args->site_srl = $site_module_info->site_srl;
			$args->member_srl = $row->member_srl;

			$output = executeQuery('member.deleteMemberGroupMember', $args);
			$oMemberController->_clearMemberCache($args->member_srl);

			$arrDelGroupMember[] = $row->member_srl;
			if(!isset($arrMemberInfo[$row->member_srl]))
				$arrMemberInfo[$row->member_srl] = array();
			if(isset($arrMemberInfo[$row->member_srl]['result']))
				$arrMemberInfo[$row->member_srl]['result_add'] = "delete";
			else
				$arrMemberInfo[$row->member_srl]['result'] = "delete";
		}


		// set new member group member
		if($arrInsertGroupMember) foreach($arrInsertGroupMember as $memberSrl)
		{
			$output = $oMemberController->addMemberToGroup($memberSrl,$groupInfo->group_srl,$site_module_info->site_srl);
		}

		// set result to database
		$arrResult = array();
		foreach($arrMemberInfo as $key=>$value)
		{
			$result = $value;
			$result['member_srl'] = $key;
			$arrResult[] = $result;
		}
		$args = new stdClass;
		$args->regdate = date("YmdHis");
		$args->unique_date = 1;
		$args->result_type = "promotion_group";
		$args->result = json_encode($arrResult);
		$oMoumiModel->insertCronResult($args);
		
		return true;	
	}

	function checkWordCount($content, $checkCount)
	{
		$content = $this->getReadableText($content);
		$strCount = mb_strlen($content, "utf-8");
		if($checkCount>$strCount)
			return false;
		return true;
	}

	function getCutString($content, $len=20)
	{
		$content = $this->getReadableText($content);
		if(!$content)
			return "";
		return mb_strlen($content, 0, 20, "utf-8");
	}

	function getReadableText($content)
	{
		// ..TODO.. remove html tags
		$content = strip_tags($content);
		$content = preg_replace('/ ( +)/is', ' ', $content);
		$content = str_replace(array('\n','\r','\n\r','\r\n'), array('','','',''), $content);

		return $content;
	}

}
