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
		if(!$member_srl) return new Object(-1, '잘못된 접근입니다');

		$oModel = getModel('profile');
		$this->profile = $oModel->_getProfile($member_srl);
		debugPrint($this->profile);

		Context::set('profile', $this->profile);

		$this->setTemplatePath($this->module_path . 'tpl/views');
		$this->setTemplateFile(str_replace('disp', '', $this->act));

		if(!$this->profile->is_owner && $this->profile->status != 'PUBLIC')
		{
			if($this->profile->status == 'UNREGISTERED')
			{
				$this->stop('프로필을 등록하지 않은 회원입니다.');
				return;
			}
			else if($this->profile->status == 'PRIVATE')
			{
				$this->stop('프로필을 공개하지 않은 회원입니다.');
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
		$member_srl = Context::get('logged_info')->member_srl;

		$timeline = $oModel->getTimeline($member_srl);
		Context::set('timeline', $timeline);

		$cond = new stdClass;
		$cond->member_srl = $member_srl;
		$package_list = $oModel->getListPackage($cond)->data;
		Context::set('package_list', $package_list);

		$cond = new stdClass;
		$cond->member_srl = $member_srl;
		$showcase_list = $oModel->getShowcaseList($cond)->data;
		Context::set('showcase_list', $showcase_list);

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
}
