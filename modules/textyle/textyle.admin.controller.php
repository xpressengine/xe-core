<?php
    /**
     * @class  textyleAdminController
     * @author NHN (developers@xpressengine.com)
     * @brief  textyle module admin controller class
     **/

    class textyleAdminController extends textyle {

        /**
         * @brief Initialization
         **/
        function init() {
        }

        /**
         * @brief Textyle Admin Create
         **/
        function procTextyleAdminCreate() {
            $oModuleModel = &getModel('module');

            $user_id = Context::get('user_id');
            $domain = preg_replace('/^(http|https):\/\//i','', trim(Context::get('domain')));
            $vid = trim(Context::get('site_id'));

            if($domain && $vid) unset($vid);
            if(!$domain && $vid) $domain = $vid;

            if(!$user_id) return new Object(-1,'msg_invalid_request');
            if(!$domain) return new Object(-1,'msg_invalid_request');

            $tmp_user_id_list = explode(',',$user_id);
            $user_id_list = array();
            foreach($tmp_user_id_list as $k => $v){
                $v = trim($v);
                if($v) $user_id_list[] = $v;
            }
            if(count($user_id_list)==0) return new Object(-1,'msg_invalid_request');

            $output = $this->insertTextyle($domain, $user_id_list);
            if(!$output->toBool()) return $output;

            $this->add('module_srl', $output->get('module_srl'));
            $this->setMessage('msg_create_textyle');
        }

        function insertTextyle($domain, $user_id_list, $settings = null) {
            if(!is_array($user_id_list)) $user_id_list = array($user_id_list);

            $oAddonAdminController = &getAdminController('addon');
            $oMemberModel = &getModel('member');
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oRssAdminController = &getAdminController('rss');
            $oTextyleModel = &getModel('textyle');
            $oTextyleController = &getController('textyle');
            $oDocumentController = &getController('document');
			
            $memberConfig = $oMemberModel->getMemberConfig();
            foreach($memberConfig->signupForm as $item){
            	if($item->isIdentifier) $identifierName = $item->name;
            }
            if($identifierName == "user_id") {
            	$member_srl = $oMemberModel->getMemberSrlByUserID($user_id_list[0]);
            	}
            else {
            	$member_srl = $oMemberModel->getMemberSrlByEmailAddress($user_id_list[0]);
            }
            if(!$member_srl) return new Object(-1,'msg_not_user');

            $member_info = $oMemberModel->getMemberInfoByMemberSrl($member_srl);

            if(strpos($domain, '.') !== false) $domain = strtolower($domain);
            $output = $oModuleController->insertSite($domain, 0);
            if(!$output->toBool()) return $output;
            $site_srl = $output->get('site_srl');

            $textyle->site_srl = $site_srl;
            $textyle->mid = $this->textyle_mid;
            $textyle->module = 'textyle';
            $textyle->module_srl = getNextSequence();
            $textyle->skin = ($settings->skin) ? $settings->skin : $this->skin;
            $textyle->browser_title = ($settings->title) ? $settings->title : sprintf("%s's Textyle", $member_info->nick_name);
            $output = $oModuleController->insertModule($textyle);

            if(!$output->toBool()) return $output;
            //$module_srl = $output->get('module_srl');
            $module_srl = $textyle->module_srl;

            $site->site_srl = $site_srl;
            $site->index_module_srl = $module_srl;
			$site->domain = $domain;
            $output = $oModuleController->updateSite($site);

            $output = $oModuleController->insertSiteAdmin($site_srl, $user_id_list);

            $args->textyle_title = $textyle->browser_title;
            $args->module_srl = $module_srl;
            $args->member_srl = $member_srl;
            $args->post_style = $this->post_style;
            $args->post_list_count = $this->post_list_count;
            $args->comment_list_count = $this->comment_list_count;
            $args->guestbook_list_count = $this->guestbook_list_count;
            $args->input_email = $this->input_email;//'R'; // Y, N
            $args->input_website = $this->input_website;//'R'; // Y, N
            $args->post_editor_skin = $this->post_editor_skin;
            $args->post_use_prefix = $this->post_use_prefix;
            $args->post_use_suffix = $this->post_use_suffix;
            $args->comment_editor_skin = 'xpresseditor';
            $args->comment_editor_colorset = 'white';
            $args->guestbook_editor_skin = 'xpresseditor';
            $args->guestbook_editor_colorset = 'white';
            $args->timezone = $GLOBALS['_time_zone'];
            $output = executeQuery('textyle.insertTextyle', $args);
            if(!$output->toBool()) return $output;

            $oTextyleController->updateTextyleCommentEditor($module_srl, $args->comment_editor_skin, $args->comment_editor_colorset);

            $output = $oRssAdminController->setRssModuleConfig($module_srl, 'Y', 'Y');
            if(!$output->toBool()) return $output;

            $oAddonAdminController->doInsert('autolink', $site_srl);
            $oAddonAdminController->doInsert('counter', $site_srl);
            $oAddonAdminController->doInsert('member_communication', $site_srl);
            $oAddonAdminController->doInsert('member_extra_info', $site_srl);
            $oAddonAdminController->doInsert('mobile', $site_srl);
            $oAddonAdminController->doInsert('smartphone', $site_srl);
            $oAddonAdminController->doInsert('referer', $site_srl);
            $oAddonAdminController->doInsert('resize_image', $site_srl);
            $oAddonAdminController->doInsert('blogapi', $site_srl);
            $oAddonAdminController->doActivate('autolink', $site_srl);
            $oAddonAdminController->doActivate('counter', $site_srl);
            $oAddonAdminController->doActivate('member_communication', $site_srl);
            $oAddonAdminController->doActivate('member_extra_info', $site_srl);
            $oAddonAdminController->doActivate('mobile', $site_srl);
            $oAddonAdminController->doActivate('smartphone', $site_srl);
            $oAddonAdminController->doActivate('referer', $site_srl);
            $oAddonAdminController->doActivate('resize_image', $site_srl);
            $oAddonAdminController->doActivate('blogapi', $site_srl);
            $oAddonAdminController->makeCacheFile($site_srl);

            $oEditorController = &getAdminController('editor');
            $oEditorController->insertComponent('colorpicker_text',true, $site_srl);
            $oEditorController->insertComponent('colorpicker_bg',true, $site_srl);
            $oEditorController->insertComponent('emoticon',true, $site_srl);
            $oEditorController->insertComponent('url_link',true, $site_srl);
            $oEditorController->insertComponent('image_link',true, $site_srl);
            $oEditorController->insertComponent('multimedia_link',true, $site_srl);
            $oEditorController->insertComponent('quotation',true, $site_srl);
            $oEditorController->insertComponent('table_maker',true, $site_srl);
            $oEditorController->insertComponent('poll_maker',true, $site_srl);
            $oEditorController->insertComponent('image_gallery',true, $site_srl);

            // set category
            $obj->module_srl = $module_srl;
            $obj->title = Context::getLang('init_category_title');
            $oDocumentController->insertCategory($obj);

            FileHandler::copyDir($this->module_path.'skins/'.$textyle->skin, $oTextyleModel->getTextylePath($module_srl));

            foreach($user_id_list as $k => $v){
                $output = $oModuleController->insertAdminId($module_srl, $v);
                if(!$output->toBool()) return $output;
            }

            $langType = Context::getLangType();
            $file = sprintf('%ssample/%s.html',$this->module_path,$langType);
            if(!file_exists(FileHandler::getRealPath($file))){
                $file = sprintf('%ssample/ko.html',$this->module_path);
            }
            $oMemberModel = &getModel('member');
            $member_info = $oMemberModel->getMemberInfoByEmailAddress($user_id_list[0]);

            $doc->module_srl = $module_srl;
            $doc->title = Context::getLang('sample_title');
            $doc->tags = Context::getLang('sample_tags');
            $doc->content = FileHandler::readFile($file);
            $doc->member_srl = $member_info->member_srl;
            $doc->user_id = $member_info->user_id;
            $doc->user_name = $member_info->user_name;
            $doc->nick_name = $member_info->nick_name;
            $doc->email_address = $member_info->email_address;
            $doc->homepage = $member_info->homepage;
            $oDocumentController->insertDocument($doc, true);

            $output = new Object();
            $output->add('module_srl',$module_srl);
            return $output;
        }

        function procTextyleAdminUpdate(){
            $vars = Context::gets('site_srl','user_id','domain','access_type','vid','module_srl','member_srl');
            if(!$vars->site_srl) return new Object(-1,'msg_invalid_request');

            if($vars->access_type == 'domain') $args->domain = strtolower($vars->domain);
            else $args->domain = $vars->vid;
            if(!$args->domain) return new Object(-1,'msg_invalid_request');

            $oMemberModel = &getModel('member');
			$member_config = $oMemberModel->getMemberConfig();
			
            $tmp_member_list = explode(',',$vars->user_id);
            $admin_list = array();
            $admin_member_srl = array();
            foreach($tmp_member_list as $k => $v){
                $v = trim($v);
                if($v){
	                if($member_config->identifier == "user_id") {
		            	$member_srl = $oMemberModel->getMemberSrlByUserID($v);
		            	}
		            else {
		            	$member_srl = $oMemberModel->getMemberSrlByEmailAddress($v);
		            }
                    if($member_srl){
                        $admin_list[] = $v;
                        $admin_member_srl[] = $member_srl;
                    }else{
                        return new Object(-1,'msg_not_user');
                    }
                }
            }

            $oModuleModel = &getModel('module');
            $site_info = $oModuleModel->getSiteInfo($vars->site_srl);
            if(!$site_info) return new Object(-1,'msg_invalid_request');

            $oModuleController = &getController('module');
            $output = $oModuleController->insertSiteAdmin($vars->site_srl, $admin_list);
            if(!$output->toBool()) return $output;

            $oModuleController->deleteAdminId($vars->module_srl);

            foreach($admin_list as $k => $v){
                $output = $oModuleController->insertAdminId($vars->module_srl, $v);
                // TODO : insertAdminId return value
                if(!$output) return new Object(-1,'msg_not_user');
                if(!$output->toBool()) return $output;
            }

            $args->site_srl = $vars->site_srl;
            $output = $oModuleController->updateSite($args);
            if(!$output->toBool()) return $output;

            unset($args);
            $args->module_srl = $vars->module_srl;
            $args->member_srl = $admin_member_srl[0];
            $output = executeQuery('textyle.updateTextyle', $args);
            if(!$output->toBool()) return $output;

            $output = new Object(1,'success_updated');
            $output->add('module_srl',$vars->module_srl);
            return $output;
        }

        function procTextyleAdminDelete() {
            $oModuleController = &getController('module');
            $oCounterController = &getController('counter');
            $oAddonController = &getController('addon');
            $oEditorController = &getController('editor');
            $oTextyleModel = &getModel('textyle');
            $oModuleModel = &getModel('module');

            $site_srl = Context::get('site_srl');
            if(!$site_srl) return new Object(-1,'msg_invalid_request');

            $site_info = $oModuleModel->getSiteInfo($site_srl);
            $module_srl = $site_info->index_module_srl;

            $oTextyle = new TextyleInfo($module_srl);
            if($oTextyle->module_srl != $module_srl) return new Object(-1,'msg_invalid_request');

            $output = $oModuleController->deleteModule($module_srl);
            if(!$output->toBool()) return $output;

            $args->site_srl = $oTextyle->site_srl;
            executeQuery('module.deleteSite', $args);
            executeQuery('module.deleteSiteAdmin', $args);
            executeQuery('member.deleteMemberGroup', $args);
            executeQuery('member.deleteSiteGroup', $args);
            executeQuery('module.deleteLangs', $args);
            
        	//clear cache for default mid
            $vid = $site_info->domain;
            $mid = $site_info->mid;
            $oCacheHandler = &CacheHandler::getInstance('object');
            if($oCacheHandler->isSupport()){
            	$cache_key = 'object_default_mid:'.$vid.'_'.$mid;
            	$oCacheHandler->delete($cache_key);
            	$cache_key = 'object_default_mid:'.$vid.'_';
            	$oCacheHandler->delete($cache_key);
            }
            
            $lang_supported = Context::get('lang_supported');
            foreach($lang_supported as $key => $val) {
                $lang_cache_file = _XE_PATH_.'files/cache/lang_defined/'.$args->site_srl.'.'.$key.'.php';
                FileHandler::removeFile($lang_cache_file);
            }
            $oCounterController->deleteSiteCounterLogs($args->site_srl);
            $oAddonController->removeAddonConfig($args->site_srl);
            $oEditorController->removeEditorConfig($args->site_srl);

            $args->module_srl = $module_srl;
            executeQuery('textyle.deleteTextyle', $args);
            executeQuery('textyle.deleteTextyleFavorites', $args);
            executeQuery('textyle.deleteTextyleTags', $args);
            executeQuery('textyle.deleteTextyleVoteLogs', $args);
            executeQuery('textyle.deleteTextyleMemos', $args);
            executeQuery('textyle.deleteTextyleReferer', $args);
            executeQuery('textyle.deleteTextyleApis', $args);
            executeQuery('textyle.deleteTextyleGuestbook', $args);
            executeQuery('textyle.deleteTextyleSupporters', $args);
            executeQuery('textyle.deleteTextyleDenies', $args);
            executeQuery('textyle.deleteTextyleSubscriptions', $args);
            executeQuery('textyle.deletePublishLogs', $args);

            @FileHandler::removeFile(sprintf("files/cache/textyle/textyle_deny/%d.php",$module_srl));

            FileHandler::removeDir($oTextyleModel->getTextylePath($module_srl));

            $this->add('module','textyle');
            $this->add('page',Context::get('page'));
            $this->setMessage('success_deleted');
        }

        function procTextyleAdminInsertCustomMenu() {
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');

            $config = $oModuleModel->getModuleConfig('textyle');
            $second_menus = Context::getLang('textyle_second_menus');

            $args = Context::getRequestVars();
            foreach($args as $key => $val) {
                if(strpos($key, 'hidden_')===false || $val!='Y') continue;
                $k = substr($key, 7);
                if(preg_match('/^([0-9]+)$/', $k)) {
                    $subs = $second_menus[$k];
                    if(count($subs)) {
                        $h = array_keys($subs);
                        for($i=0,$c=count($h);$i<$c;$i++) $hidden_menu[] = strtolower($h[$i]);
                    }
                }
                $hidden_menu[] = $k;
            }

            $config->hidden_menu = $hidden_menu;

            if(!$config->attached_menu || !is_array($config->attached_menu)) $config->attached_menu = array();

            $attached = array();
            foreach($args as $key => $val) {
                if(strpos($key, 'custom_act_')!==false && $val) {
                    $idx = substr($key, 11);
                    $attached[$idx]->act = $val;
                } elseif(strpos($key, 'custom_name_')!==false && $val) {
                    $idx = substr($key, 12);
                    $attached[$idx]->name = $val;

                }
            }

            if(count($attached)) {
                foreach($attached as $key => $val) {
                    if(!$val->act || !$val->name) continue;
                    $config->attached_menu[$key][$val->act] = $val->name;
                }
            }

            foreach($args as $key => $val) {
                if(strpos($key, 'delete_')===false || $val!='Y') continue;
                $delete_menu[] = substr($key, 7);
            }

            if(count($delete_menu)) {
                foreach($config->attached_menu as $key => $val) {
                    if(!count($val)) continue;
                    foreach($val as $k => $v) {
                        if(in_array(strtolower($k), $delete_menu)) unset($config->attached_menu[$key][$k]);
                    }
                }
            }
            $oModuleController->insertModuleConfig('textyle', $config);
        }

        function procTextyleAdminInsertBlogApiServices(){
            $args = Context::getRequestVars();

            if($args->textyle_blogapi_services_srl){
                $output = executeQuery('textyle.updateBlogApiService',$args);
            }else{
                $args->textyle_blogapi_services_srl = getNextSequence();
                $args->list_order = $args->textyle_blogapi_services_srl * -1;
                $output = executeQuery('textyle.insertBlogApiService',$args);
            }
        }

        function procTextyleAdminDeleteBlogApiServices(){
            $args->textyle_blogapi_services_srl = Context::get('textyle_blogapi_services_srl');
            $output = executeQuery('textyle.deleteBlogApiService',$args);
            return $output;
        }

        function initTextyle($site_srl){
            $oCounterController = &getController('counter');
            $oDocumentController = &getController('document');
            $oCommentController = &getController('comment');
            $oTagController = &getController('tag');
            $oAddonController = &getController('addon');
            $oEditorController = &getController('editor');
            $oTrackbackController = &getController('trackback');
            $oModuleModel = &getModel('module');
            $oTextyleModel = &getModel('textyle');
            $oMemberModel = &getModel('member');

            $site_info = $oModuleModel->getSiteInfo($site_srl);
            $module_srl = $site_info->index_module_srl;
            $args->site_srl = $site_srl;

            $oTextyle = new TextyleInfo($module_srl);
            if($oTextyle->module_srl != $module_srl) return new Object(-1,'msg_invalid_request');

            $oCounterController->deleteSiteCounterLogs($args->site_srl);
            $oAddonController->removeAddonConfig($args->site_srl);

            $args->module_srl = $module_srl;
            $output = executeQuery('textyle.deleteTextyleFavorites', $args);
            $output = executeQuery('textyle.deleteTextyleTags', $args);
            $output = executeQuery('textyle.deleteTextyleVoteLogs', $args);
            $output = executeQuery('textyle.deleteTextyleMemos', $args);
            $output = executeQuery('textyle.deleteTextyleReferer', $args);
            $output = executeQuery('textyle.deleteTextyleApis', $args);
            $output = executeQuery('textyle.deleteTextyleGuestbook', $args);
            $output = executeQuery('textyle.deleteTextyleSupporters', $args);
            $output = executeQuery('textyle.deletePublishLogs', $args);

            FileHandler::removeFile(sprintf("./files/cache/textyle/textyle_deny/%d.php",$module_srl));
            FileHandler::removeDir($oTextyleModel->getTextylePath($module_srl));

            // delete document comment tag
            $output = $oDocumentController->triggerDeleteModuleDocuments($args);
            $output = $oCommentController->triggerDeleteModuleComments($args);
            $output = $oTagController->triggerDeleteModuleTags($args);
            $output = $oTrackbackController->triggerDeleteModuleTrackbacks($args);
            $args->module_srl = $args->module_srl *-1;

            $output = $oDocumentController->triggerDeleteModuleDocuments($args);
            $output = $oCommentController->triggerDeleteModuleComments($args);
            $output = $oTagController->triggerDeleteModuleTags($args);
            $args->module_srl = $args->module_srl *-1;

            // set category
            $obj->module_srl = $module_srl;
            $obj->title = Context::getLang('init_category_title');
            $oDocumentController->insertCategory($obj);

            FileHandler::copyDir($this->module_path.'skins/'.$this->skin, $oTextyleModel->getTextylePath($module_srl));

            $langType = Context::getLangType();
            $file = sprintf('%ssample/%s.html',$this->module_path,$langType);
            if(!file_exists(FileHandler::getRealPath($file))){
                $file = sprintf('%ssample/ko.html',$this->module_path);
            }

            $member_info = $oMemberModel->getMemberInfoByEmailAddress($oTextyle->getUserId());

            $doc->module_srl = $module_srl;
            $doc->title = Context::getLang('sample_title');
            $doc->tags = Context::getLang('sample_tags');
            $doc->content = FileHandler::readFile($file);
            $doc->member_srl = $member_info->member_srl;
            $doc->user_id = $member_info->user_id;
            $doc->user_name = $member_info->user_name;
            $doc->nick_name = $member_info->nick_name;
            $doc->email_address = $member_info->email_address;
            $doc->homepage = $member_info->homepage;
            $output = $oDocumentController->insertDocument($doc, true);

            return new Object(1,'success_textyle_init');
        }

		function exportTextyle($site_srl,$export_type='ttxml'){
            require_once($this->module_path.'libs/exportTextyle.php');
			//$this->deleteExport($site_srl);

			$path = './files/cache/textyle/export/' . getNumberingPath($site_srl);
			FileHandler::makeDir($path);
			$file = $path.sprintf('tt-%s.xml',date('YmdHis'));

			// $textyle_srl 
			$oModuleModel = &getModel('module');
			$site_info = $oModuleModel->getSiteInfo($site_srl);
			$textyle_srl = $site_info->index_module_srl;

			$oExport = new TTXMLExport($file);
			$oExport->setTextyle($textyle_srl);
			$oExport->exportFile();

			$args->site_srl = $site_srl;
			$args->export_file = $file;
			$output = executeQuery('textyle.updateExport',$args);
			if(!$output->toBool()) return $output;
		}

		function procTextyleAdminExport(){
			$site_srl = Context::get('site_srl');
			if(!$site_srl) $site_srl = $this->module_info->site_srl;
			if(!$site_srl) return new Object(-1,'msg_invalid_request');
			$export_type = Context::get('export_type');
			if(!$export_type) $export_type = 'ttxml';
			
			$args->site_srl = $site_srl;
			$output = executeQuery('textyle.getExport',$args);
			if(!$output->data){
				if(!$args->export_type || $args->export_type!='xexml') $args->export_type='ttxml';
				$logged_info = Context::get('logged_info');
				$args->module_srl = $this->module_srl;
				$args->member_srl = $logged_info->member_srl;
				$output = executeQuery('textyle.insertExport',$args);
			}

			$this->exportTextyle($site_srl,$export_type);
		}

		function procTextyleAdminDeleteExportTextyle(){
			$site_srl = Context::get('site_srl');
			if(!$site_srl) return new Object(-1,'msg_invalid_request');

			$this->deleteExport($site_srl);
		}

		function deleteExport($site_srl){
			$args->site_srl = $site_srl;
			$output = executeQuery('textyle.getExport',$args);

			if($output->data){
				FileHandler::removeFile($output->data->export_file);
				$args->site_srl = $site_srl;
				$output = executeQuery('textyle.deleteExport',$args);
				if(!$output->toBool()) return false;
			}
		}

		function procTextyleAdminInsertExtraMenuConfig(){
			$module_srl = Context::get('module_srl');

            $oModuleController = &getController('module');
            $oTextyleModel = &getModel('textyle');

			$vars = Context::getRequestVars();
			$allow_service = array();
            foreach($vars as $key => $val) {
                if(strpos($key,'allow_service_')===false) continue;
                $allow_service[substr($key, strlen('allow_service_'))] = $val;
            }

			$config = $oTextyleModel->getModulePartConfig($module_srl);
			$config->allow_service = $allow_service;

			if($module_srl){
                $oModuleController->insertModulePartConfig('textyle', $module_srl, $config);

			}else{
                $oModuleController->insertModuleConfig('textyle', $config);
			}
		}
    }
?>
