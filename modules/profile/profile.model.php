<?php
class profileModel extends profile
{
	// 쇼케이스
	// 자료실
	// 게시물
	// 댓글
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

	public function _getDocument($member_srl, $cond)
	{
		$oDocumentModel = getModel('document');
		$cond->member_srl = $member_srl;
		$output = $oDocumentModel->getDocumentList($cond, false, false);
		debugPrint($output);
		return $output;
	}

	public function _getComment($member_srl, $cond)
	{
		$oCommentModel = &getModel('comment');
		$output = $oCommentModel->getCommentListByMemberSrl($member_srl, array(), 1, false, 5);
		debugPrint($output);
	}
}
