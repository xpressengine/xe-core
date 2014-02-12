<?php
    /**
     * @class  enrollController
     * @author sol (sol@nhn.com)
     * @brief  enroll controller class
     **/

    class enrollController  extends enroll {

        function init() {
        }

	function procEnrollItemUpdateStatus() {
	    $obj = Context::gets('enroll_srl','status','module_srl');
	    $oEnrollController = &getController('enroll');
	    $output = $oEnrollController->updateEnrollItem($obj);
		return $output;
	}
        function procEnrollItemUpdate() {
		Context::set('error_return_url',getNotEncodedUrl('', 'module', 'enroll', 'act', 'dispEnrollModify','mid',Context::get('mid')));

		$obj = Context::getRequestVars();
	    $obj->module_srl = $this->module_srl;
	    if(!$obj->enroll_srl) return new Object(-1,'잘못된 요청입니다.');

	    $oEnrollModel = &getModel('enroll');
		
		$logged_info = Context::get('logged_info');
		$enroll_obj->module_srl = $this->module_srl;
		$enroll_obj->enroll_srl = $enroll_srl;
		$enroll_obj->member_srl = $logged_info->member_srl;

		$output = $oEnrollModel->getEnrollItem($enroll_obj);

		//데티어 없고,관리자가 아니고, 자신의 글이 아닐 경우 update 할 수 없다.
		// 파일 확장자를 확인하다.
/*		$file = Context::get('file');
		if($file) {
			$NallowedFileTypes = array('jpg','gif','jpeg','png');
			$fileInfo = pathinfo($file['name']);
			$ext = strtolower($fileInfo['extension']);
			if(!in_array($ext, $NallowedFileTypes))
			{
				return new Object(-1,'첨부 가능한 파일이 아닙니다.');
			}
			$oFileController = &getController('file');
			if($enroll_info->file_srl) $oFileController->deleteFile($enroll_info->file_srl);
			$output = $oFileController->insertFile($file, $obj->module_srl, $obj->enroll_srl);
			$obj->file_srl = $output->get('file_srl');
			$obj->status = 'Y';
		}
		*/
	    //수정
			$output = $this->updateEnrollItem($obj,$enroll_info);	    
	 		if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
				$this->setMessage('수정했습니다');
                	$returnUrl = getNotEncodedUrl('', 'module', 'enroll', 'act', 'dispEnrollModify','mid',Context::get('mid'));

                        $this->setRedirectUrl($returnUrl);
                        return;
               }
	}

	function updateEnrollItem($obj,$enroll_info = NULL) {
		if($obj->enroll_srl) $enroll_info->enroll_srl = $obj->enroll_srl;
		if($obj->module_srl) $enroll_info->module_srl = $obj->module_srl;
		if($obj->member_srl) $enroll_info->member_srl = $obj->member_srl;
		if($obj->job) $enroll_info->job = $obj->job;
		if($obj->homepage) $enroll_info->homepage = $obj->homepage;	
		if($obj->job_content) $enroll_info->job_content = $obj->job_content;	
		if($obj->user_name) $enroll_info->user_name = $obj->user_name;	
		if($obj->phone) $enroll_info->phone = $obj->phone;	
		if($obj->content) $enroll_info->content = $obj->content;	
		if($obj->status) $enroll_info->status = $obj->status;	
		$output = executeQuery('enroll.updateEnrollItem',$enroll_info);
		return $output;
	}

        function procEnrollItemInsert() {
		//모집기간 확인 (모집기간:YmdHis)
		if(!Context::get('is_logged'))
		{
			return new Object(-1,'로그인 후 다시 시도하세요.');
		}

		$now_date = date('YmdHis');
		$to_regdate = $this->module_info->to_regdate;
		$until_regdate = $this->module_info->until_regdate;
		if(!$to_regdate || !$until_regdate || $now_date > $until_regdate || $now_date < $to_regdate) return new Object(-1,'신청 기간이 아닙니다.');

		$oEnrollModel = &getModel('enroll');
		//set obj
		$obj = Context::getRequestVars();
		$obj->module_srl = $this->module_srl;

		$cnt_obj->module_srl = $this->module_srl;
		$cnt_obj->status = 'Y';
		$item_count = $oEnrollModel->getEnrollItemCount($cnt_obj);
		if($this->module_info->limit_count && $item_count >= $this->module_info->limit_count) return new Object(-1,'신청 인원이 많아 조기 마감되었습니다.');

		if($obj->provision != 'Y') return new Object(-1,'약관에 동의하셔야합니다.'); 

		//신청 권한에 따라 setObj
		//(lang->grant_enroll)
		// member, nonmember, autojoin, all
		//$grant_enroll = $this->module_info->grant_enroll;

		$logged_info = Context::get('logged_info');
		$obj->member_srl = $logged_info->member_srl;
		$obj->email_address = $logged_info->email_address;

		//check duplicate
		$oEnrollModel = &getModel('enroll');
		$cnt = $oEnrollModel->getCntEnrollItemByEmailAddress($obj);

		if($cnt > 0) {
			return new Object(-1,'이미 등록된 메일주소입니다.');
		}
		// 파일 확장자를 확인하다.
/*		$file = Context::get('file');
		if($file) {
			$NallowedFileTypes = array('jpg','gif','jpeg','png');
			$fileInfo = pathinfo($file['name']);
			$ext = strtolower($fileInfo['extension']);
			if(!in_array($ext, $NallowedFileTypes))
			{
				return new Object(-1,'첨부 가능한 파일이 아닙니다.');
			}
			$oFileController = &getController('file');
			if($enroll_info->file_srl) $oFileController->deleteFile($enroll_info->file_srl);
			$output = $oFileController->insertFile($file, $obj->module_srl, $obj->enroll_srl);
			$obj->file_srl = $output->get('file_srl');
			$obj->status = 'Y';
		}
*/
			$obj->status = 'Y';
            $obj->enroll_srl = getNextSequence();
            $obj->list_order = $obj->enroll_srl * -1;
            $output = executeQuery('enroll.insertEnrollItem',$obj);
            if(!$output->toBool()) return $output;

	 		if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
                	$returnUrl = Context::get('success_return_url') ? Context::get('success_return_url') : getNotEncodedUrl('', 'module', 'enroll', 'act', 'dispEnrollInsertComplete','mid',Context::get('mid'));
                        $this->setRedirectUrl($returnUrl);
                        return;
               }
        }
    }
?>
