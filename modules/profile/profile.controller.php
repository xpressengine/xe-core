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
}
