<?php
class profileView extends profile
{
	private $member_srl;
	private static $profile;

	public function init()
	{
		$logged_info = Context::get('logged_info');

		$member_srl = (int)Context::get('member_srl');
		if(!$member_srl) {
			$member_srl = Context::get('logged_info')->member_srl;
		}
		if(!$member_srl) return new Object(-1, '잘못된 접근');

		$this->profile = new stdClass;
		$this->profile->member_srl = $member_srl;

		$oModel = getModel('profile');
		$this->profile->member = $oModel->_getProfile($this->profile->member_srl);
debugPrint('set');
		Context::set('profile', $this->profile);

		$this->setTemplatePath($this->module_path . 'tpl/views');
		$this->setTemplateFile(str_replace('disp', '', $this->act));

		if($this->profile->member->status != 'PUBLIC') {
			if($logged_info->member_srl == $member_srl) {
				// if($this->act != 'dispProfileSetting') header('Location: ' . getNotEncodedFullUrl('act','dispProfileSetting'));
			} else if($this->profile->member->status == 'NONE') {
				$this->stop('프로필을 등록하지 않은 회원입니다.');
				return;
			}
		}
	}

	public function dispProfileView()
	{
		// ...&detail=showcase
		$detail = Context::get('detail');
		if($detail == 'showcase') {
			// 쇼케이스
			$this->_dispShowcase();
		} else if ($detail == 'resource') {
			// 자료실
			$this->_dispResource();
		} else if ($detail == 'document') {
			// 게시물
			$this->_dispDocument();
		} else if ($detail == 'comment') {
			// 댓글
			$this->_dispComment();
		} else {
			$this->_dispIndex();
		}
	}

	public function dispProfileSetting()
	{

	}

	private function _dispIndex()
	{

		$oModel = getModel('profile');
		$vars = Context::getRequestVars();

		$cond = new stdClass;
		$cond->page = 1;
		$cond->list_count = 5;
		$this->profile->document = $oModel->_getDocument($this->profile->member_srl, $cond);
		debugPrint($this->profile->document->data);

		$oModel = getModel('profile');
		$vars = Context::getRequestVars();

		$cond = new stdClass;
		$cond->page = ($vars->page) ? $vars->page : 1;
		$this->profile->showcase = $oModel->_getComment($this->profile->member_srl, $cond);
	}

	// 쇼케이스
	private function _dispShowcase()
	{
		$oModel = getModel('profile');
		$vars = Context::getRequestVars();

		$cond = new stdClass;
		$cond->page = ($vars->page) ? $vars->page : 1;
		$this->profile->showcase = $oModel->_getShowcase($this->profile->member_srl, $cond);
	}

	// 자료실
	private function _dispResource()
	{
		$oModel = getModel('profile');
		$vars = Context::getRequestVars();

		$cond = new stdClass;
		$cond->page = ($vars->page) ? $vars->page : 1;
		$this->profile->showcase = $oModel->_getResource($this->profile->member_srl, $cond);
	}

	// 게시물
	private function _dispDocument()
	{
		$oModel = getModel('profile');
		$vars = Context::getRequestVars();

		$cond = new stdClass;
		$cond->page = ($vars->page) ? $vars->page : 1;
		$this->profile->showcase = $oModel->_getDocument($this->profile->member_srl, $cond);
	}

	// 댓글
	private function _dispComment()
	{
		$oModel = getModel('profile');
		$vars = Context::getRequestVars();

		$cond = new stdClass;
		$cond->page = ($vars->page) ? $vars->page : 1;
		$this->profile->showcase = $oModel->_getComment($this->profile->member_srl, $cond);
	}
}
