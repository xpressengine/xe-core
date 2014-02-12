<?php
    class contestAdminView extends contest {

        function init() {
            $oModuleModel = &getModel('module');

            $this->setTemplatePath(sprintf("%stpl/",$this->module_path));
            $this->setTemplateFile(strtolower(str_replace('dispContestAdmin','',$this->act)));

            $module_srl = Context::get('module_srl');
            if($module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
                if(!$module_info) {
                    Context::set('module_srl','');
                    $this->act = 'dispKinAdminList';
                } else {
                    ModuleModel::syncModuleToSite($module_info);
                    $this->module_info = $module_info;
                    Context::set('module_info',$module_info);
                }
            }
        }

        function dispContestAdminList() {
			$oModuleMode = &getModel('module');
			$oDocumentModel = &getModel('document');
			$module_srl = implode(',',$oModuleMode->getModuleSrlByMid('contestUpload'));

            $args->sort_index = "module_srl";
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;
            $args->s_module_category_srl = Context::get('module_category_srl');

			$args->module_srl = $module_srl;
			$output = $oDocumentModel->getDocumentList($args);

            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('contest_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);
        }

        function dispContestAdminView() {
			$oModuleMode = &getModel('module');
			$oDocumentModel = &getModel('document');
			$module_srl = implode(',',$oModuleMode->getModuleSrlByMid('contestUpload'));
			$document_srl = Context::get('document_srl');
			
			$output = $oDocumentModel->getDocument($document_srl);
			if($output->get('module_srl') != $module_srl) exit;
            Context::set('val', $output);
        }
    }
?>
