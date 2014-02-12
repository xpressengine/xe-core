<?php
    /**
     * @class  resourceAdminView
     * @author NHN (developers@xpressengine.com)
     * @brief  resource admin view class
     **/

    class resourceAdminView extends resource {

        function init() {
            $oModuleModel = &getModel('module');
            $module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);

            $this->setTemplatePath(sprintf("%stpl/",$this->module_path));
            $this->setTemplateFile(strtolower(str_replace('dispResourceAdmin','',$this->act)));

            $module_srl = Context::get('module_srl');
            if($module_srl) {
                $module_info = $oModuleModel->getModuleInfoByModuleSrl($module_srl);
                if(!$module_info) {
                    Context::set('module_srl','');
                    $this->act = 'dispResourceAdminList';
                } else {
                    ModuleModel::syncModuleToSite($module_info);
                    $this->module_info = $module_info;
                    Context::set('module_info',$module_info);
                }
            }
            Context::set('module_srl', $this->module_info->module_srl);

			$security = new Security();
			$security->encodeHTML('module_info.');
        }

        function dispResourceAdminList() {
            $args->sort_index = "module_srl";
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;
            $args->s_module_category_srl = Context::get('module_category_srl');
            $output = executeQueryArray('resource.getResourceList', $args);
            ModuleModel::syncModuleToSite($output->data);

            Context::addJsFile($this->module_path.'tpl/js/resource_admin.js');

            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('resource_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

			$security = new Security();
			$security->encodeHTML('resource_list..');
        }

        function dispResourceAdminInsert() {
            $oModuleModel = &getModel('module');
            $oLayoutModel = &getModel('layout');

            Context::set('skin_list', $oModuleModel->getSkins($this->module_path));
            Context::set('layout_list', $oLayoutModel->getLayoutList());

			$mobile_layout_list = $oLayoutModel->getLayoutList(0,"M");
			Context::set('mlayout_list', $mobile_layout_list);

			$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
			Context::set('mskin_list', $mskin_list);
        }

        function dispResourceAdminCategory() {
            $oDocumentModel = &getModel('document');
            Context::set('category_content', $oDocumentModel->getCategoryHTML($this->module_info->module_srl));
        }

        function dispResourceAdminGrant() {
            $oModuleAdminModel = &getAdminModel('module');
            Context::set('grant_content', $oModuleAdminModel->getModuleGrantHTML($this->module_info->module_srl, $this->xml_info->grant));
        }

        function dispResourceAdminAdditions() {
            $content = '';

            $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'before', $content);
            $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'after', $content);
            Context::set('addition_content', $content);
        }

        function dispResourceAdminSkin() {
            $oModuleAdminModel = &getAdminModel('module');
            Context::set('skin_content', $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl));

			$security = new Security();
			$security->encodeHTML('module_info.');
        }

        function dispResourceAdminMobileSkin() {
        	$oModuleAdminModel = &getAdminModel('module');
        	Context::set('skin_content', $oModuleAdminModel->getModuleMobileSkinHTML($this->module_info->module_srl));

        	$security = new Security();
        	$security->encodeHTML('module_info.');
        }


        function dispResourceAdminDelete() {
            $oDocumentModel = &getModel('document');

            if(!$this->module_info) return new Object(-1,'msg_invalid_request');

            Context::set('document_count', $oDocumentModel->getDocumentCount($this->module_info->module_srl));
        }
    }
?>
