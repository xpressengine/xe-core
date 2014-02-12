<?php
    /**
     * @class  kinAdminView
     * @author zero (skklove@gmail.com)
     * @brief  kin admin view class
     **/

    class kinAdminView extends kin {

        function init() {
            $oModuleModel = &getModel('module');
            $module_category = $oModuleModel->getModuleCategories();
            Context::set('module_category', $module_category);

            $this->setTemplatePath(sprintf("%stpl/",$this->module_path));
            $this->setTemplateFile(strtolower(str_replace('dispKinAdmin','',$this->act)));

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

			$security = new Security();
			$security->encodeHTML('module_info.');
			$security->encodeHTML('module_category..');
        }

        function dispKinAdminList() {
            $args->sort_index = "module_srl";
            $args->page = Context::get('page');
            $args->list_count = 20;
            $args->page_count = 10;
            $args->s_module_category_srl = Context::get('module_category_srl');
            $output = executeQueryArray('kin.getKinList', $args);
            ModuleModel::syncModuleToSite($output->data);

			// get the skin path
			$oModuleModel = getModel('module');
			$skin_list = $oModuleModel->getSkins($this->module_path);
			Context::set('skin_list', $skin_list);

			$mskin_list = $oModuleModel->getSkins($this->module_path, 'm.skins');
			Context::set('mskin_list', $mskin_list);

			$oModuleAdminModel = getAdminModel('module');
			$selectedManageContent = $oModuleAdminModel->getSelectedManageHTML($this->xml_info->grant);
			Context::set('selected_manage_content', $selectedManageContent);

            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('kin_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

			$security = new Security();
			$security->encodeHTML('kin_list..browser_title', 'kin_list..mid');
			$security->encodeHTML('skin_list..title', 'mskin_list..title');
			$security->encodeHTML('layout_list..title', 'layout_list..layout');
			$security->encodeHTML('mlayout_list..title', 'mlayout_list..layout');
        }

        function dispKinAdminInsert() {
            $oModuleModel = &getModel('module');
            $oLayoutModel = &getModel('layout');

			$mobile_layout_list = $oLayoutModel->getLayoutList(0,"M");
			Context::set('mlayout_list', $mobile_layout_list);

			$mskin_list = $oModuleModel->getSkins($this->module_path, "m.skins");
			Context::set('mskin_list', $mskin_list);

            Context::set('skin_list', $oModuleModel->getSkins($this->module_path));
            Context::set('layout_list', $oLayoutModel->getLayoutList());

			$security = new Security();
			$security->encodeHTML('skin_list..title', 'mskin_list..title');
			$security->encodeHTML('layout_list..title', 'layout_list..layout');
			$security->encodeHTML('mlayout_list..title', 'mlayout_list..layout');
        }

        function dispKinAdminCategory() {
            $oDocumentModel = &getModel('document');
            Context::set('category_content', $oDocumentModel->getCategoryHTML($this->module_info->module_srl));
        }

	
		//show advance config page
        function dispKinAdminAdditions() {
            $content = '';

            $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'before', $content);
            $output = ModuleHandler::triggerCall('module.dispAdditionSetup', 'after', $content);
            Context::set('addition_content', $content);
        }

        function dispKinAdminDelete() {
            $oDocumentModel = &getModel('document');

            if(!$this->module_info) return new Object(-1,'msg_invalid_request');

            Context::set('document_count', $oDocumentModel->getDocumentCount($this->module_info->module_srl));
        }
        
		function dispKinAdminSkinInfo(){
			$oModuleAdminModel = &getAdminModel('module');
            $skin_content = $oModuleAdminModel->getModuleSkinHTML($this->module_info->module_srl);

            Context::set('skin_content', $skin_content);

            $this->setTemplateFile('skin_info');
		}

		function dispKinAdminMobileSkinInfo()
		{
			$oModuleAdminModel = getAdminModel('module');
			$skin_content = $oModuleAdminModel->getModuleMobileSkinHTML($this->module_info->module_srl);
			Context::set('skin_content', $skin_content);
			$this->setTemplateFile('skin_info');
		}

    }

