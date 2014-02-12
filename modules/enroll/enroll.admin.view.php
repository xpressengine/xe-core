<?php
    /**
     * @class  enrollAdminView
     * @author sol (sol@nhn.com)
     * @brief  enroll admin view class
     **/

    class enrollAdminView extends enroll {

        function init() {
            $oModuleModel = &getModel('module');

            $this->setTemplatePath(sprintf("%stpl/",$this->module_path));
            $this->setTemplateFile(strtolower(str_replace('dispEnrollAdmin','',$this->act)));

            $module_srl = Context::get('module_srl');
            if($module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
                if(!$module_info) {
                    Context::set('module_srl','');
                    $this->act = 'dispEnrollAdminList';
                } else {
                    ModuleModel::syncModuleToSite($module_info);
                    $this->module_info = $module_info;
                    Context::set('module_info',$module_info);
                }
            }
        }

        function dispEnrollAdminList() {
            $args->sort_index = "module_srl";
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;
            $args->s_module_category_srl = Context::get('module_category_srl');
            $output = executeQueryArray('enroll.getEnrollModuleList', $args);
            ModuleModel::syncModuleToSite($output->data);

            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('enroll_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);
        }



        function dispEnrollAdminItemList() {
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;

            $args->module_srl = Context::get('module_srl');
            $args->status = Context::get('status');
	    $oEnrollModel = &getModel('enroll');
            $output = $oEnrollModel->getEnrollItemList($args);

	    //파일정보
	    $oFileModel = &getModel('file');
	    if($output->data) {
	    	foreach($output->data as &$val) {
			if($val->file_srl) $val->file_info = $oFileModel->getFile($val->file_srl);

	   	 }
	    }

            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('enroll_list', $output->data);
			$security = new Security();
			$security->encodeHTML('enroll_list..');
            Context::set('page_navigation', $output->page_navigation);
        }


        function dispEnrollAdminInsert() {
            $oModuleModel = &getModel('module');
            $oLayoutModel = &getModel('layout');

            Context::set('skin_list', $oModuleModel->getSkins($this->module_path));
            Context::set('layout_list', $oLayoutModel->getLayoutList());
            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_enroll.xml');
        }

        function dispEnrollAdminGrant() {
            $oModuleAdminModel = &getAdminModel('module');
            Context::set('grant_content', $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant));
        }

        function dispEnrollAdminAdditions() {
            $content = '';

            $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'before', $content);
            $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'after', $content);
            Context::set('addition_content', $content);
        }

        function dispEnrollAdminSkin() {
            $oModuleAdminModel = &getAdminModel('module');
            Context::set('skin_content', $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl));
        }

        function dispEnrollAdminDelete() {
            if(!$this->module_info) return new Object(-1,'msg_invalid_request');
            $oEnrollModel = &getModel('enroll');
            $count = $oEnrollModel->getEnrollItemCount($this->module_info->module_srl);
            Context::set('document_count', $count);
        }
    }
?>
