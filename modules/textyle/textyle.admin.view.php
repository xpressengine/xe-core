<?php
    /**
     * @class  textyleAdminView
     * @author NHN (developers@xpressengine.com)
     * @brief  textyle module admin view class
     **/

    class textyleAdminView extends textyle {

        /**
         * @brief Initialization
         **/
        function init() {
            $oTextyleModel = &getModel('textyle');

            $this->setTemplatePath($this->module_path."/tpl/");
            $template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);
        }

        function dispTextyleAdminList() {
            $vars = Context::getRequestVars();
            $oTextyleModel = &getModel('textyle');

            $page = Context::get('page');
            if(!$page) $page = 1;

            if($vars->search_target && $vars->search_keyword) {
                $args->{'s_'.$vars->search_target} = strtolower($vars->search_keyword);
            }

            $args->list_count = 20;
            $args->page = $page;
            $args->list_order = 'regdate';
            $output = $oTextyleModel->getTextyleList($args);
            if(!$output->toBool()) return $output;

            Context::set('textyle_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            $this->setTemplateFile('list');
        }

        function dispTextyleAdminInsert() {
            $oModuleModel = &getModel('module');
            $oMemberModel = &getModel('member');
			
            //set identifier type of admin
        	$memberConfig = $oMemberModel->getMemberConfig();
            foreach($memberConfig->signupForm as $item){
            	if($item->isIdentifier) $identifierName = $item->name;
            }
            Context::set('identifier',$identifierName);
            
            $module_srl = Context::get('module_srl');
            if($module_srl) {
                $oTextyleModel = &getModel('textyle');
                $textyle = $oTextyleModel->getTextyle($module_srl);
                Context::set('textyle', $textyle);

                $admin_list = $oModuleModel->getSiteAdmin($textyle->site_srl);
                $site_admin = array();
                if(is_array($admin_list)){
                    foreach($admin_list as $k => $v){
                    	if($identifierName == 'user_id')  $site_admin[] = $v->user_id;
                    	   else $site_admin[] = $v->email_address;
                    }

                    Context::set('site_admin', join(',',$site_admin));
                }
            }
            
            
            $skin_list = $oModuleModel->getSkins($this->module_path);
            Context::set('skin_list',$skin_list);

            $this->setTemplateFile('insert');
        }

        function dispTextyleAdminDelete() {
            if(!Context::get('module_srl')) return $this->dispTextyleAdminList();
            $module_srl = Context::get('module_srl');

            $oTextyleModel = &getModel('textyle');
            $oTextyle = $oTextyleModel->getTextyle($module_srl);
            $textyle_info = $oTextyle->getObjectVars();

            $oDocumentModel = &getModel('document');
            $document_count = $oDocumentModel->getDocumentCount($textyle_info->module_srl);
            $textyle_info->document_count = $document_count;

            Context::set('textyle_info',$textyle_info);

            $this->setTemplateFile('textyle_delete');
        }

        function dispTextyleAdminCustomMenu() {
            $oTextyleModel = &getModel('textyle');
            $custom_menu = $oTextyleModel->getTextyleCustomMenu();
            Context::set('custom_menu', $custom_menu);

            $this->setTemplateFile('textyle_custom_menu');
        }

        function dispTextyleAdminBlogApiConfig(){
            $textyle_blogapi_services_srl = Context::get('textyle_blogapi_services_srl');

            $oTextyleModel = &getModel('textyle');
            $output = $oTextyleModel->getBlogApiService();
            if($output->toBool() && $output->data){
                if($textyle_blogapi_services_srl){
                    foreach($output->data as $k => $v){
                        if($v->textyle_blogapi_services_srl == $textyle_blogapi_services_srl){
                            Context::set('service',$v);
                        }
                    }
                }else{
                    Context::set('blogapi_services_list',$output->data);
                }
            }
            $this->setTemplateFile('textyle_blogapi_config');
        }

        function dispTextyleAdminExportList(){
			$args->page = Context::get('page');
			$output = executeQueryArray('textyle.getExportList',$args);			
			Context::set('export_list',$output->data);
			Context::set('page_navigation',$output->page_navigation);
            $this->setTemplateFile('textyle_export_list');
        }

		function dispTextyleAdminExtraMenu(){
            $module_srl = Context::get('module_srl');

			$oTextyleModel = &getModel('textyle');
			$config = $oTextyleModel->getModulePartConfig($module_srl);
			Context::set('config',$config);

            $oModuleModel = &getModel('module');
            $installed_module_list = $oModuleModel->getModulesXmlInfo();
            foreach($installed_module_list as $key => $val) {
                if($val->category != 'service') continue;
                $service_modules[] = $val;
            }
            Context::set('service_modules', $service_modules);
            $this->setTemplateFile('textyle_extra_menu_config');
		}
    }
?>
