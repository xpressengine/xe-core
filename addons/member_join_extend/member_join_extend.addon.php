<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file member_join_extend.addon.php
     * @author sol (sol@ngleader.com)
     * @brief 회원 가입 화면 출력시 (dispMemberSignUpForm) 14세 이상/미만 구분, 이용약관 출력
     **/

    if($called_position == 'before_module_init'){

		// 실제 가입시 체크
		if(Context::get('act')=='procMemberInsert'){
			// session 체크
			if(!$_SESSION['member_join_extend_authed_act']){
				$this->error = "msg_not_permitted";
			}

		// 동의시 action 
		}else if(Context::get('act') =='MemberJoinExtendAgree'){
			// session 추가 
			$_SESSION['member_join_extend_authed'] = true;

			// xml_rpc return
			header("Content-Type: text/xml; charset=UTF-8");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache");
			print("<response>\r\n<error>0</error>\r\n<message>success</message>\r\n</response>");

			Context::close();
			exit();
		}

	} else if($called_position == 'after_module_proc') {

		if(Context::get('act') == "dispMemberSignUpForm"){
			if(!$_SESSION['member_join_extend_authed']){

				// addon options
				if($addon_info->use_agreement=='Y' && $addon_info->agreement){
					Context::set('agreement',$addon_info->agreement);
				}
				if($addon_info->use_private_agreement=='Y' && $addon_info->private_agreement){
					Context::set('private_agreement',$addon_info->private_agreement);
					Context::set('private_gathering_agreement',$addon_info->private_gathering_agreement);
				}
				if($addon_info->use_junior_join=='Y'){
					Context::set('use_junior_join',$addon_info->use_junior_join);
				}

				// load addon lang 
				Context::loadLang(_XE_PATH_.'addons/member_join_extend/lang');
				Context::addHtmlHeader(sprintf('<script type="text/javascript"> var msg_junior_join ="%s"; var msg_check_agree ="%s";</script>',trim($addon_info->msg_junior_join),Context::getLang('msg_check_agree')));

				// change module template
				Context::addJsFile('./addons/member_join_extend/member_join_extend.js',false);
				$addon_tpl_path = './addons/member_join_extend/tpl';
				$addon_tpl_file = 'member_join_extend.html';
				
				$this->setTemplatePath($addon_tpl_path);
				$this->setTemplateFile($addon_tpl_file);
			}else{
    			unset($_SESSION['member_join_extend_authed']);
                $_SESSION['member_join_extend_authed_act'] = true;
            }

		// delete session
		}else if(in_array(Context::get('act'),array('procMemberInsert'))){
			unset($_SESSION['member_join_extend_authed_act']);
		}
	}
?>
