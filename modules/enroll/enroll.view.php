<?php
    /**
     * @class  enrollView
     * @author sol (sol@nhn.com)
     * @brief  enroll view class
     **/

    class enrollView extends enroll {

        function init() {
		//$this->stop('마감되었습니다');
			$login_act = array('dispEnrollInsert','dispEnrollModify');
			if((in_array(Context::get('act'),$login_act) || !Context::get('act')) && !Context::get('is_logged'))
			{
				$returnUrl = getNotEncodedUrl('','mid',Context::get('mid'),'act', 'dispMemberLoginForm');
				header('location:'.$returnUrl);
				return;
			}
            $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            if(!is_dir($template_path)||!$this->module_info->skin) {
                $this->module_info->skin = 'hw_seminar_2012';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            }
            $this->setTemplatePath($template_path);
            $this->setTemplateFile(strtolower(str_replace('dispEnroll','',$this->act)));

			Context::set('layout','none');
        }

        function dispEnrollInsert() {
            Context::addJsFile($this->module_path.'tpl/js/enroll.js');
			$logged_info = Context::get('logged_info');
			$oEnrollModel = &getModel('enroll');
			$args->email_address = $logged_info->email_address;
			$args->module_srl = $this->module_info->module_srl;

			$output = $oEnrollModel->getEnrollItemByEmailAddress($args);
			if($output->data)
			{
				Context::set('ck_enroll',true);
			}
			$this->dispEnrollItemList();
        }
		function dispEnrollModifyComplete() {
		}
        function dispEnrollModify() {

			$oEnrollController = &getController('enroll');
			$oEnrollModel = &getModel('enroll');

			$logged_info = Context::get('logged_info');
			
			$obj->module_srl = $this->module_info->module_srl; 
			$obj->email_address = $logged_info->email_address;

			$output = $oEnrollModel->getEnrollItemByEmailAddress($obj);	
			if(!$output->toBool()) return $output;
			
			$enrollItem = $output->data;
			Context::set('enrollItem',$enrollItem);
			Context::set('file',$file);
	       	Context::addJsFile($this->module_path.'tpl/js/enroll.js');
		}

		function setErrorDisplay($msg,$tpl) {
				Context::set('password','');
				Context::set('email_address','');
				Context::set('confirm','');
				$this->setTemplateFile($tpl);
			return;
		}

		function dispEnrollInsertComplete(){
		}
		function dispEnrollItemList() {
            $args->page = Context::get('page');
            $args->list_count = 10;
            $args->page_count = 10;

            $args->module_srl = Context::get('module_srl');
			if(!$args->module_srl) $args->module_srl = $this->module_info->module_srl;
            $args->status = Context::get('status');
			$oEnrollModel = &getModel('enroll');
            $output = $oEnrollModel->getEnrollItemListUser($args);

			$item_count = array();
			$cnt_args->module_srl = $args->module_srl;
			$cnt_args->status = 'Y';
            $item_count['Y'] = $oEnrollModel->getEnrollItemCount($cnt_args);
			$cnt_args->status = 'W';
            $item_count['W'] = $oEnrollModel->getEnrollItemCount($cnt_args);

            ModuleModel::syncModuleToSite($output->data);
			if($output->data) {
				foreach($output->data as &$data) {
					$email_info = explode('@',$data->email_address);
					$data->email_address = substr($email_info[0],0,4).'...@'.substr($email_info[1],0,4).'...';
				}
			}
			$enroll_list = $output->data;
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
			Context::set('enroll_list', $enroll_list);
			Context::set('item_count', $item_count);
			$security = new Security();
			$security->encodeHTML('enroll_list..');

            Context::set('page_navigation', $output->page_navigation);

		}
	}
?>
