<?php
class profileModel extends profile
{
	// 쇼케이스
	// 자료실
	// 회원
	function _getProfile($member_srl)
	{
		$oMemberModel = &getModel('member');
		$logged_info = Context::get('logged_info');

		$profile = new stdClass;
		$profile->member_srl = $member_srl;
		$profile->is_owner = ($logged_info->member_srl == $member_srl) ? true : false;

		$cond = new stdClass;
		$cond->member_srl = $member_srl;
		$result = executeQuery('profile.getProfile', $cond)->data;
debugPrint($result);
		if(!$result)
		{
			$profile->status = 'UNREGISTERED';
		}
		else
		{
			$member = new stdClass;
			$member->nick_name = $result->nick_name;
			$member->user_name = $result->user_name;
			$member->email_address = $result->email_address;
			$member->limit_date = $result->limit_date;
			$member->limit_date = $result->limit_date;
			$member->profile_image = $oMemberModel->getProfileImage($member_srl)->src;
			$member->signature = $oMemberModel->getSignature($member_srl);

			$profile->type = $result->profile_type;
			$profile->status = $result->status;
			$profile->likes = $result->likes;
			$profile->config = json_decode($result->config);
			$profile->member = $member;

		}

		if($result->denied != 'N')
		{
			$profile->status = 'BLOCK';
		}
		debugPrint($profile);

		// $status
// UNREGISTERED = 미등록
// PUBLIC = 공개
// PRIVATE = 비공개
// BLOCK = 차단

		return $profile;
	}

	public function getListPackage($args)
	{
		$output = executeQueryArray('profile.getPackageList', $args);
		if(!$output->data) $output->data = array();

		return $output;
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

	public function getPromotionBanner($args = null)
	{
		$output = executeQueryArray('profile.getPromotionBanner', $args);
		if(!$output->data) $output->data = array();

		return $output->data;
	}

	public function getShowcaseList($args = null)
	{
		$output = executeQueryArray('profile.getShowcaseList', $args);
		if(!$output->data) $output->data = array();

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
