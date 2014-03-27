<?php
class profileModel extends profile
{
	// 쇼케이스
	// 자료실
	// 회원
	function _getProfile($member_srl)
	{
		$oMemberModel = &getModel('member');

		$cond = new stdClass;
		$cond->member_srl = $member_srl;
		$profile = executeQuery('profile.getProfile', $cond)->data;

		if(!$profile) {
			$profile = new stdClass;
			$profile->status = 'UNREGISTERED';
		} else {
			$profile->profile_image = $oMemberModel->getProfileImage($member_srl)->src;
			$profile->signature = $oMemberModel->getSignature($member_srl);
		}

		debugPrint($profile);
		// $status
// UNREGISTERED = 미등록
// PUBLIC = 공개
// PRIVATE = 비공개
// BLOCK = 차단

		return $profile;
	}

	public function getTimeline($member_srl, $args = null)
	{
		if(!$args) {
			$args = new stdClass;
			$args->member_srl = $member_srl;
		}

		$output = executeQueryArray('profile.getTimeline', $args);
		if(!$output->data) $output->data = array();

		foreach($output->data as $item) {
			$item->metadata = json_decode($item->metadata);
		}

		return $output;
	}


	public function insertTimeline($args)
	{
		debugPrint('### insertTimeline');
		debugPrint($args);
		$logged_info = Context::get('logged_info');

		$args->metadata = json_encode($args->metadata);
		$args->list_order = $args->regdate * -1;
		$output = executeQuery('profile.insertTimeline', $args);
		debugPrint($output);
	}

	public function updateTimeline($obj)
	{
		debugPrint('### updateTimeline');
		debugPrint($obj);
	}

	public function deleteTimeline($obj)
	{
		debugPrint('### deleteTimeline');
		debugPrint($obj);

		$args = $obj;
		$output = executeQuery('profile.deleteTimeline', $args);
		debugPrint($output);
	}
}
