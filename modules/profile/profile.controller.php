<?php
class profileController extends profile
{
	public function procProfileSetup()
	{
		$vars = Context::getRequestVars();
		$logged_info = Context::get('logged_info');
		debugPrint($vars);

		$args = new stdClass;
		$args->member_srl = $logged_info->member_srl;
		$args->status = 'PUBLIC';
		$args->profile_type = ($vars->is_biz == 'Y') ? 'BIZ' : 'NORMAL';
		$output = executeQuery('profile.insertProfile', $args);
		debugPrint($output);

		if(Context::get('success_return_url'))
		{
			$this->setRedirectUrl(Context::get('success_return_url'));
		}
	}

	public function procProfileInsertShowcase()
	{
		// 스샷 첨부
		// 사이트 이름, URL, 짧은 설명
	}

	// 회원 메뉴
	public function triggerMemberMenu($obj)
	{
		$oMemberController = getController('member');
		$vars = Context::getRequestVars();

		$mid = Context::get('cur_mid');
		$member_srl = Context::get('target_srl');

		$oMemberController->addMemberPopupMenu(getUrl('', 'mid',$mid, 'module','profile', 'act','dispProfileView', 'member_srl',$member_srl), '프로필 보기', '');
	}

	public function triggerAfterInsertDocument($obj)
	{
		debugPrint('@@@ triggerAfterInsertDocument');
		debugPrint($obj);
		if($obj->status != 'PUBLIC') return;
		$oProfileModel = getModel('profile');

		$logged_info = Context::get('logged_info');

		$metadata = new stdClass;
		$metadata->title = $obj->title;
		$metadata->module_srl = $obj->module_srl;
		$metadata->summary = preg_replace("/(\t|\n)/", '', $obj->content);
		$metadata->summary = cut_str(trim(strip_tags($metadata->summary)), 200);

		$data = new stdClass;
		$data->member_srl = $logged_info->member_srl;
		$data->msg_type = 'xe.add-document';
		$data->ref_id = 'xe.document-' . $obj->document_srl;
		$data->metadata = $metadata;
		$data->regdate = time();
		$output = $oProfileModel->insertTimeline($data);
	}

	public function triggerAfterDeleteDocument($obj)
	{
		debugPrint('@@@ triggerAfterDeleteDocument');
		debugPrint($obj);
		$oProfileModel = getModel('profile');

		$cond = new stdClass;
		$cond->member_srl = $obj->member_srl;
		$cond->ref_id = 'xe.document-' . $obj->document_srl;
		$output = $oProfileModel->deleteTimeline($cond);

		$cond = new stdClass;
		$cond->member_srl = $obj->member_srl;
		$cond->ref_id = 'xe.comment-' . $obj->document_srl;
		$output = $oProfileModel->deleteTimeline($cond);
	}

	public function triggerAfterInsertComment($obj)
	{
		debugPrint('@@@ triggerAfterInsertComment');
		debugPrint($obj);
		if($obj->is_secret == 'Y') return;

		$oProfileModel = getModel('profile');
		$oDocumentModel = getModel('document');

		$logged_info = Context::get('logged_info');

		$oDocument =  $oDocumentModel->getDocument($obj->document_srl);

		$metadata = new stdClass;
		$metadata->document_srl = $obj->document_srl;
		$metadata->title = $oDocument->getTitleText();
		$metadata->module_srl = $obj->module_srl;
		$metadata->module = $obj->module;
		$metadata->summary = preg_replace("/(\t|\n)/", '', $obj->content);
		$metadata->summary = cut_str(trim(strip_tags($metadata->summary)), 200);

		$data = new stdClass;
		$data->member_srl = $logged_info->member_srl;
		$data->msg_type = 'xe.add-comment';
		$data->ref_id = 'xe.comment-' . $obj->comment_srl;
		$data->metadata = $metadata;
		$data->regdate = time();
		debugPrint($data);
		$output = $oProfileModel->insertTimeline($data);
	}

	public function triggerAfterDeleteComment($obj)
	{
		debugPrint('@@@ triggerAfterDeleteComment');
		debugPrint($obj);
		$oProfileModel = getModel('profile');

		$cond = new stdClass;
		$cond->ref_id = 'xe.comment-' . $obj->comment_srl;
		$output = $oProfileModel->deleteTimeline($cond);
	}

}
