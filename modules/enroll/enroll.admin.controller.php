<?php
    /**
     * @class  enrollAdminController
     * @author sol (sol@nhn.com)
     * @brief  enroll admin controller class
     **/

    class enrollAdminController extends enroll {

        function init() {
        }
        function procEnrollAdminInsert() {
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');

            $args = Context::getRequestVars();
            $args->module = 'enroll';
            $args->mid = $args->enroll_name;
            unset($args->body);
            unset($args->enroll_name);
		$args->to_regdate_time = str_replace(':','',$args->to_regdate_time);
		$args->until_regdate_time = str_replace(':','',$args->until_regdate_time);

		if($args->to_regdate) {
			if(strlen($args->to_regdate_time) == 4)
				$args->to_regdate .= $args->to_regdate_time.'00';
			else 
				$args->to_regdate .= '000000';
			
		}
		if($args->until_regdate) {
			if(strlen($args->until_regdate_time) == 4)
				$args->until_regdate .= $args->until_regdate_time.'00';
			else 
				$args->to_regdate .= '000000';
		}
		unset($args->to_regdate_time);
		unset($args->until_regdate_time);

            if($args->module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($args->module_srl);
                if($module_info->module_srl != $args->module_srl) unset($args->module_srl);
            }

            if($args->module_srl) {
                $output = $oModuleController->updateModule($args);
                $msg_code = 'success_updated';
            } else {
                $output = $oModuleController->insertModule($args);
                $msg_code = 'success_registed';
            }

            if(!$output->toBool()) return $output;

            $this->setRedirectUrl(getUrl('','module','admin','act','dispEnrollAdminInsert','module_srl',$output->get('module_srl')));
            $this->setMessage($msg_code);
        }

        function procEnrollAdminDelete() {
            $oModuleController = &getController('module');

            $module_srl = Context::get('module_srl');
            $output = $oModuleController->deleteModule($module_srl);
            if(!$output->toBool()) return $output;

            $this->setRedirectUrl(getUrl('','module','admin','act','dispEnrollAdminList'));
            $this->setMessage('success_deleted');
        }

        function procEnrollAdminItemDelete() {
            $args->enroll_srl = Context::get('enroll_srl');
            if(!$args->enroll_srl) return new Object(-1,'msg_invalid_request');
            $args->module_srl = $this->module_srl;

            $output = executeQuery('enroll.deleteEnrollItem',$args);
            if(!$output->toBool()) return $output;

            $this->setMessage('success_deleted');
        }

    }
?>
