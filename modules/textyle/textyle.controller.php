<?php
    /**
     * @class  textyleController
     * @author NHN (developers@xpressengine.com)
     * @brief  textyle module Controller class
     **/

    class textyleController extends textyle {
        /**
         * @brief Initialization
         **/
        function init() {
            $oTextyleModel = &getModel('textyle');
            $oModuleModel = &getModel('module');

            $site_module_info = Context::get('site_module_info');
            $site_srl = $site_module_info->site_srl;
            if($site_srl) {
                $this->module_srl = $site_module_info->index_module_srl;
                $this->module_info = $oModuleModel->getModuleInfoByModuleSrl($this->module_srl);
                Context::set('module_info',$this->module_info);
                Context::set('mid',$this->module_info->mid);
                Context::set('current_module_info',$this->module_info);
            }

            $this->custom_menu = $oTextyleModel->getTextyleCustomMenu();
            $this->textyle = $oTextyleModel->getTextyle($this->module_srl);
            $this->site_srl = $this->textyle->site_srl;
            Context::set('textyle',$this->textyle);

            // deny
            if(!$this->grant->manager){
                $vars = Context::gets('user_name','user_id','homepage','email_address');

                $deny = $oTextyleModel->checkDenyIP($this->module_srl,$_SERVER['REMOTE_ADDR']);
                if($deny) $this->stop('msg_not_permitted');

                $deny = $oTextyleModel->checkDenyUserName($this->module_srl,$vars->user_id);
                if($deny) $this->stop('msg_not_permitted');

                $deny = $oTextyleModel->checkDenyUserName($this->module_srl,$vars->user_name);
                if($deny) $this->stop('msg_not_permitted');

                $deny = $oTextyleModel->checkDenyEmail($this->module_srl,$vars->email_address);
                if($deny) $this->stop('msg_not_permitted');

                $deny = $oTextyleModel->checkDenySite($this->module_srl,$vars->homepage);
                if($deny) $this->stop('msg_not_permitted');
            }
        }

        function procTextyleConfigCommunicationInsert(){
        	$logged_info = Context::get('logged_info');
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');

            if(in_array(strtolower('dispTextyleToolConfigCommunication'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');
			
            $args = Context::getRequestVars();
            $args->module_srl = $this->module_srl;
            $args->member_srl = $logged_info->member_srl;
            $output = $this->updateTextyle($args);
            if(!$output->toBool()) return $output;

            $oRssAdminController = &getAdminController('rss');
            $open_rss = Context::get('rss_type');
            $output = $oRssAdminController->setRssModuleConfig($this->module_srl, $open_rss, 'Y');
            if(!$output->toBool()) return $output;

            $this->updateTextyleCommentEditor($this->module_srl, $args->comment_editor_skin, $args->comment_editor_colorset);

            $config = $oModuleModel->getModulePartConfig('textyle', $this->module_srl);
            $config->me2day_userid = $args->me2day_userid;
            $config->me2day_userkey = $args->me2day_userkey;
            $config->enable_me2day = ($args->me2day_userid && $args->me2day_userkey) ? 'Y' :'N';
			
            //set twitter api parammeters
            $config->twitter_consumer_key = $args->twitter_consumer_key;
            $config->twitter_consumer_secret = $args->twitter_consumer_secret;
            $config->twitter_oauth_token = $args->twitter_oauth_token;
            $config->twitter_oauth_token_secret = $args->twitter_oauth_token_secret;
            $config->enable_twitter = ($config->twitter_consumer_key && $config->twitter_consumer_secret && $config->twitter_oauth_token && $config->twitter_oauth_token_secret) ? 'Y' :'N';
            
            //$config->enable_twitter = $args->enable_twitter=='Y'?'Y':'N';
            //$config->twitter_userid = $args->twitter_userid;
            //$config->twitter_password = $args->twitter_password;
            //$config->enable_twitter = ($args->twitter_userid && $args->twitter_password) ? 'Y' :'N';

            $config->comment_grant = (int)$args->comment_grant;
            $config->guestbook_grant = (int)$args->guestbook_grant;
            $oModuleController->insertModulePartConfig('textyle',$this->module_srl, $config);

			$comment_config->comment_count = $args->comment_list_count;
            $oModuleController->insertModulePartConfig('comment',$this->module_srl, $comment_config);
        }

        function procTextyleLogin() {
            $oMemberController = &getController('member');

            if(!$user_id) $user_id = Context::get('user_id');
            $user_id = trim($user_id);

            if(!$password) $password = Context::get('password');
            $password = trim($password);

            if(!$keep_signed) $keep_signed = Context::get('keep_signed');

            $stat = 0;

            if(!$user_id) {
                $stat = -1;
                $msg = Context::getLang('null_user_id');
            }
            if(!$password) {
                $stat = -1;
                $msg = Context::getLang('null_password');
            }

            if(!$stat) {
                $output = $oMemberController->doLogin($user_id, $password, $keep_signed=='Y'?true:false);
                if(!$output->toBool()) {
                    $stat = -1;
                    $msg = $output->getMessage();
                }
            }

            $this->add('stat',$stat);
            $this->setMessage($msg);
        }

        function procTextyleCheckMe2day() {
            require_once($this->module_path.'libs/me2day.api.php');
            $vars = Context::gets('me2day_userid','me2day_userkey');

            $oMe2 = new me2api($vars->me2day_userid, $vars->me2day_userkey);
            $output = $oMe2->chkNoop($vars->me2day_userid, $vars->me2day_userkey);
            if($output->toBool()) return new Object(-1,'msg_success_to_me2day');
            return new Object(-1,'msg_fail_to_me2day');
        }
        
    	function procTextyleCheckTwitter() {
            require_once($this->module_path.'libs/twitteroauth.php');
            $vars = Context::gets('twitter_consumer_key','twitter_consumer_secret','twitter_oauth_token','twitter_oauth_token_secret');
			$twitteroauth = new TwitterOAuth($vars->twitter_consumer_key, $vars->twitter_consumer_secret , $vars->twitter_oauth_token , $vars->twitter_oauth_token_secret);
			$credentials = $twitteroauth->get("account/verify_credentials");
            $error = $credentials->error;
            
            if($error == '') return new Object(-1,'msg_success_to_twitter');
            return new Object(-1,'msg_fail_to_twitter');
        }

        function updateTextyleCommentEditor($module_srl, $comment_editor_skin, $comment_editor_colorset) {
            $oEditorModel = &getModel('editor');
            $oModuleController = &getController('module');

            $editor_config = $oEditorModel->getEditorConfig($module_srl);

            $editor_config->editor_skin = 'dreditor';
            $editor_config->content_style = 'default';
            $editor_config->content_font = null;
            $editor_config->comment_editor_skin = $comment_editor_skin;
            $editor_config->sel_editor_colorset = null;
            $editor_config->sel_comment_editor_colorset = $comment_editor_colorset;
            $editor_config->enable_html_grant = array(1);
            $editor_config->enable_comment_html_grant = array(1);
            $editor_config->upload_file_grant = array(1);
            $editor_config->comment_upload_file_grant = array(1);
            $editor_config->enable_default_component_grant = array(1);
            $editor_config->enable_comment_default_component_grant = array(1);
            $editor_config->enable_component_grant = array(1);
            $editor_config->enable_comment_component_grant = array(1);
            $editor_config->editor_height = 500;
            $editor_config->comment_editor_height = 100;
            $editor_config->enable_autosave = 'N';
            $oModuleController->insertModulePartConfig('editor',$module_srl,$editor_config);
        }

        function updateTextyle($args){
            $output = executeQuery('textyle.updateTextyle', $args);
            return $output;
        }

        function procTextyleProfileUpdate(){
            $oMemberController = &getController('member');

            if(in_array(strtolower('dispTextyleToolConfigProfile'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            // nickname, email
            $args->member_srl = $this->textyle->member_srl;
            $args->nick_name = Context::get('nick_name');
            $args->email_address = Context::get('email_address');
            $output = $oMemberController->updateMember($args);
            if(!$output->toBool()) return $output;

            $tex->profile_content = Context::get('profile_content');
            $tex->module_srl = $this->module_srl;
			$output = executeQuery('textyle.updateProfileContent',$tex);
            if(!$output->toBool()) return $output;

            if(Context::get('delete_photo')=='Y') {
                $this->deleteTextylePhoto($this->module_srl);
            }
        }

        function procTextyleProfileImageUpload() {
            $oMemberController = &getController('member');

            $photo = Context::get('photo');
            if($this->textyle && Context::isUploaded() && is_uploaded_file($photo['tmp_name'])) {
                $oMemberController->insertProfileImage($this->textyle->member_srl, $photo['tmp_name']);
            }

            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('move_mytextyle');
        }

        function deleteTextylePhoto($module_srl){
            $oMemberController = &getController('member');
            Context::set('member_srl', $this->textyle->member_srl);
            $output = $oMemberController->procMemberDeleteProfileImage();
        }

        function updateTextyleInfo($module_srl,$args){
            $args->module_srl = $module_srl;
            $output = executeQuery('textyle.updateTextyle', $args);
            return $output;
        }

        function procTextyleInfoUpdate(){
            $oModuleController = &getController('module');
            $oModuleModel = &getModel('module');
            $oTextyleModel = &getModel('textyle');

            if(in_array(strtolower('dispTextyleToolConfigInfo'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            $args = Context::gets('textyle_title','textyle_content','timezone');
			$args->module_srl = $this->module_srl;
            $output = executeQuery('textyle.updateTextyleInfo',$args);
            if(!$output->toBool()) return $output;

            $module_info = $oModuleModel->getModuleInfoByModuleSrl($this->module_srl);
            $module_info->browser_title = $args->textyle_title;
            $output = $oModuleController->updateModule($module_info);
            if(!$output->toBool()) return $output;

			unset($args);
            $args->index_module_srl = $this->module_srl;
            $args->default_language = Context::get('language');
            $args->site_srl = $this->site_srl;
            $output = $oModuleController->updateSite($args);
            if(!$output->toBool()) return $output;

            if(Context::get('delete_icon')=='Y') $this->deleteTextyleFavicon($this->module_srl);

            $favicon = Context::get('favicon');
            if(Context::isUploaded()&&is_uploaded_file($favicon['tmp_name'])) $this->insertTextyleFavicon($this->module_srl,$favicon['tmp_name']);

            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('move_mytextyle');
        }

        function insertTextyleFavicon($module_srl, $source) {
            $oTextyleModel = &getModel('textyle');
            $path = $oTextyleModel->getTextyleFaviconPath($module_srl);
            if(!is_dir($path)) FileHandler::makeDir($path);
            $filename = sprintf('%sfavicon.ico', $path);
            move_uploaded_file($source, $filename);
        }

        function deleteTextyleFavicon($module_srl){
            $oTextyleModel = &getModel('textyle');
            $path = $oTextyleModel->getTextyleFaviconPath($module_srl);
            $filename = sprintf('%s/favicon.ico', $path);
            FileHandler::removeFile($filename);
        }

        /**
         * @brief comment insert
         **/
        function procTextyleInsertComment() {
            $oDocumentModel = &getModel('document');
            $oCommentModel = &getModel('comment');
            $oCommentController = &getController('comment');

			$oTextyleInfo = new TextyleInfo($this->textyle->textyle_srl);
			if(!$oTextyleInfo->isEnableComment())
			{
				return new Object(-1, 'msg_not_permitted');
			}

            if(!$this->grant->write_comment) return new Object(-1, 'msg_not_permitted');

            $obj = Context::gets('document_srl','comment_srl','parent_srl','content','password','nick_name','member_srl','email_address','homepage','is_secret','notify_message');
            $obj->module_srl = $this->module_srl;

            $oDocument = $oDocumentModel->getDocument($obj->document_srl);
            if(!$oDocument->isExists()) return new Object(-1,'msg_not_permitted');

            if(!$obj->comment_srl) $obj->comment_srl = getNextSequence();
            else $comment = $oCommentModel->getComment($obj->comment_srl, $this->grant->manager);

            if($comment->comment_srl != $obj->comment_srl) {
                if($obj->parent_srl) {
                    $parent_comment = $oCommentModel->getComment($obj->parent_srl);
                    if(!$parent_comment->comment_srl) return new Object(-1, 'msg_invalid_request');

                    $output = $oCommentController->insertComment($obj);

                } else {
                    $output = $oCommentController->insertComment($obj);
                }

                if($output->toBool() && $this->module_info->admin_mail) {
                    $oMail = new Mail();
                    $oMail->setTitle($oDocument->getTitleText());
                    $oMail->setContent( sprintf("From : <a href=\"%s#comment_%d\">%s#comment_%d</a><br/>\r\n%s", $oDocument->getPermanentUrl(), $obj->comment_srl, $oDocument->getPermanentUrl(), $obj->comment_srl, $obj->content));
                    $oMail->setSender($obj->nick_name, $obj->email_address);

                    $target_mail = explode(',',$this->module_info->admin_mail);
                    for($i=0;$i<count($target_mail);$i++) {
                        $email_address = trim($target_mail[$i]);
                        if(!$email_address) continue;
                        $oMail->setReceiptor($email_address, $email_address);
                        $oMail->send();
                    }
                }

            } else {
                $obj->parent_srl = $comment->parent_srl;
                $output = $oCommentController->updateComment($obj, $this->grant->manager);
                $comment_srl = $obj->comment_srl;
            }
            if(!$output->toBool()) return $output;

            $this->setMessage('success_registed');
            $this->add('mid', Context::get('mid'));
            $this->add('document_srl', $obj->document_srl);
            $this->add('comment_srl', $obj->comment_srl);
        }

        function procTextyleCommentVerificationPassword(){
            $output = $this->checkCommentVerificationPassword();
            if($output) return $output;
        }

        /**
         * @brief chech comment verification password
         **/
        function checkCommentVerificationPassword() {
            $password = Context::get('password');
            $document_srl = Context::get('document_srl');
            $comment_srl = Context::get('comment_srl');

            $oMemberModel = &getModel('member');

            if($comment_srl) {
                $oCommentModel = &getModel('comment');
                $oComment = $oCommentModel->getComment($comment_srl);
                if(!$oComment->isExists()) return new Object(-1, 'msg_invalid_request');

                if(!$oMemberModel->isValidPassword($oComment->get('password'),$password)) return new Object(-1, 'msg_invalid_password');

                $oComment->setGrant();
            }
        }


        function procTextyleGuestbookVerificationPassword() {
            $oTextyleModel = &getModel('textyle');
            $password = Context::get('password');
            $textyle_guestbook_srl = Context::get('textyle_guestbook_srl');

            if(!$password || !$textyle_guestbook_srl) return new Object(-1, 'msg_invalid_request');

            $output = $oTextyleModel->getTextyleGuestbook($textyle_guestbook_srl);
            if($output->data){
                if($output->data[0]->password == md5($password)){
                    $this->addGuestbookGrant($textyle_guestbook_srl);
                }else{
                    return new Object(-1, 'msg_invalid_password');
                }
            }else{
                return new Object(-1, 'msg_invalid_request');
            }
        }

        function addGuestbookGrant($textyle_guestbook_srl){
            $_SESSION['own_textyle_guestbook'][$textyle_guestbook_srl]=true;
        }


        /**
         * @brief Guestbook insert
         **/
        function procTextyleGuestbookWrite(){
            $val = Context::gets('mid','nick_name','homepage','email_address','password','content','parent_srl','textyle_guestbook_srl','page','is_secret');

            // set
            $obj->module_srl = $this->module_srl;
            $obj->content = $val->content;
            $obj->is_secret = $val->is_secret == 'Y' ?1:-1;


            // update
            if($val->textyle_guestbook_srl>0){
                $obj->user_name = $obj->nick_name = $val->nick_name;
                $obj->email_address = $val->email_address;
                $obj->homepage = $obj->homepage;
                $obj->password = md5($val->password);

                $obj->textyle_guestbook_srl = $val->textyle_guestbook_srl;
                $output = executeQuery('textyle.updateTextyleGuestbook', $obj);

            // insert
            }else{
                // if logined
                if(Context::get('is_logged')) {
                    $logged_info = Context::get('logged_info');
                    $obj->member_srl = $logged_info->member_srl;
                    $obj->user_id = $logged_info->user_id;
                    $obj->user_name = $logged_info->user_name;
                    $obj->nick_name = $logged_info->nick_name;
                    $obj->email_address = $logged_info->email_address;
                    $obj->homepage = $logged_info->homepage;
                }else{
                    $obj->user_name = $obj->nick_name = $val->nick_name;
                    $obj->email_address = $val->email_address;
                    $obj->homepage = $obj->homepage;
                    $obj->password = md5($val->password);
                }

                $obj->textyle_guestbook_srl = getNextSequence();
                // reply
                if($val->parent_srl>0){
                    $obj->parent_srl = $val->parent_srl;
                    $obj->list_order = $obj->parent_srl * -1;
                }else{
                    $obj->list_order = $obj->textyle_guestbook_srl * -1;
                }
                $output = executeQuery('textyle.insertTextyleGuestbook', $obj);
            }
            if(!$output->toBool()) return $output;

            $this->addGuestbookGrant($obj->textyle_guestbook_srl);
            $obj->guestbook_count = 1;
            $output = $this->updateTextyleSupporter($obj);
            $this->add('page',$val->page?$val->page:1);
        }

        function procTextyleNotifyItemDelete(){
            $notified_srl = Context::get('notified_srl');
            $child_notified_srl = Context::get('child_notified_srl');
            if(!$notified_srl && !$child_notified_srl) return new Object(-1,'msg_invalid_request');
            $oNotifyAdminController = &getAdminController('tccommentnotify');
            if($notified_srl)
            {
                $parent_list = explode(',', $notified_srl);
                foreach($parent_list as $parent_srl)
                {
                    $oNotifyAdminController->deleteParent($parent_srl);
                }
            }
            if($child_notified_srl)
            {
                $children_list = explode(',', $child_notified_srl);
                foreach($children_list as $child_srl)
                {
                    $oNotifyAdminController->deleteChild($child_srl);
                }
            }
        }


        /**
         * @brief Guestbook item delete
         **/
        function procTextyleGuestbookItemDelete(){
            $textyle_guestbook_srl = Context::get('textyle_guestbook_srl');
            if(!$textyle_guestbook_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');
            if(!($logged_info->is_site_admin || $_SESSION['own_textyle_guestbook'][$textyle_guestbook_srl])) return new Object(-1,'msg_not_permitted');
            $output = $this->deleteGuestbookItem($textyle_guestbook_srl);
            return $output;
        }

        /**
         * @brief Guestbook items delete
         **/
        function procTextyleGuestbookItemsDelete(){
            $oTextyleModel = &getModel('textyle');

            $textyle_guestbook_srl = Context::get('textyle_guestbook_srl');
            if(!$textyle_guestbook_srl) return new Object(-1,'msg_invalid_request');

            $textyle_guestbook_srl = explode(',',trim($textyle_guestbook_srl));
            rsort($textyle_guestbook_srl);
            if(count($textyle_guestbook_srl)<1) return new Object(-1,'msg_invalid_request');

            foreach($textyle_guestbook_srl as $k => $srl){
                $output = $this->deleteGuestbookItem($srl);
                if(!$output->toBool()) return $output;
            }
        }

        function deleteGuestbookItem($textyle_guestbook_srl){
            $oTextyleModel = &getModel('textyle');
            $output = $oTextyleModel->getTextyleGuestbook($textyle_guestbook_srl);
            $oGuest = $output->data;

            if(!$oGuest) return new Object(-1,'msg_invalid_request');

            // delete children
            $pobj->parent_srl = $textyle_guestbook_srl;
            $output = executeQueryArray('textyle.getTextyleGuestbook', $pobj);
            if($output->data){
                foreach($output->data as $k=>$v){
                    $poutput = $this->deleteGuestbookItem($v->textyle_guestbook_srl);
                    if(!$poutput->toBool()) return $poutput;
                }
            }


            $obj->textyle_guestbook_srl = $textyle_guestbook_srl;
            $output = executeQuery('textyle.deleteTextyleGuestbookItem', $obj);
            if(!$output->toBool()) return $output;

            if($oGuest->textyle_guestbook_srl) {
                $obj->module_srl = $oGuest->module_srl;
                $obj->member_srl = $oGuest->member_srl;
                $obj->nick_name = $oGuest->nick_name;
                $obj->homepage = $oGuest->homepage;
                $obj->guestbook_count = -1;
                $output = $this->updateTextyleSupporter($obj);
            }
            return $output;
        }


        /**
         * @brief Guestbook secret on/off
         **/
        function procTextyleGuestbookItemsChangeSecret(){
            $s_args = Context::getRequestVars();
            $textyle_guestbook_srl = $s_args->textyle_guestbook_srl;

            if(preg_match('/^([0-9,]+)$/',$textyle_guestbook_srl)) $textyle_guestbook_srl = explode(',',$textyle_guestbook_srl);
            else $textyle_guestbook_srl = array($textyle_guestbook_srl);
            if(count($textyle_guestbook_srl)<1) return new Object(-1,'error');

            $args->textyle_guestbook_srl = join(',',$textyle_guestbook_srl);
            $output = executeQuery('textyle.updateTextyleGuestbookItemsChangeSecret', $args);
            if(!$output->toBool()) return $output;
        }

        /**
         * @brief Comment item delete
         **/
        function procTextyleCommentItemDelete(){
            $comment_srl = Context::get('comment_srl');

            if($comment_srl<1) return new Object(-1,'error');
            $comment_srl = explode(',',trim($comment_srl));
            if(count($comment_srl)<1) return new Object(-1,'msg_invalid_request');

            $oCommentController = &getController('comment');

            for($i=0,$c=count($comment_srl);$i<$c;$i++){
                $output = $oCommentController->deleteComment($comment_srl[$i], $this->grant->manager);
                if(!$output->toBool()) return $output;
            }

            $this->add('mid', Context::get('mid'));
            $this->add('page', Context::get('page'));
            $this->add('document_srl', $output->get('document_srl'));
            $this->setMessage('success_deleted');
        }

        function procTextyleCommentItemSetSecret(){
            $is_secret = Context::get('is_secret');
            $args->is_secret = $is_secret =='Y' ? 'Y' : 'N';

            $args->comment_srl = Context::get('comment_srl');
            $args->module_srl = Context::get('module_srl');
            $oCommentController = &getController('comment');
            $output = $oCommentController->updateComment($args, $this->grant->manager);
            $this->add('mid', Context::get('mid'));
            $this->add('page', Context::get('page'));
            $this->add('document_srl', $output->get('document_srl'));
        }

        /**
         * @brief Trackback item delete
         **/
        function procTextyleTrackbackItemDelete(){
            $trackback_srl = Context::get('trackback_srl');

            if($trackback_srl<1) return new Object(-1,'error');
            $trackback_srl = explode(',',trim($trackback_srl));
            if(count($trackback_srl)<1) return new Object(-1,'msg_invalid_request');

            $oTrackbackController = &getController('trackback');

            for($i=0,$c=count($trackback_srl);$i<$c;$i++){
                $output = $oTrackbackController->deleteTrackback($trackback_srl[$i], $this->grant->manager);
                if(!$output->toBool()) return $output;
            }

            $this->add('mid', Context::get('mid'));
            $this->add('page', Context::get('page'));
            $this->add('document_srl', $output->get('document_srl'));
            $this->setMessage('success_deleted');
        }

        /**
         * @brief deny insert
         **/
        function procTextyleDenyInsert(){
            if(in_array(strtolower('dispTextyleToolCommunicationSpam'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            $var = Context::getRequestVars();
            if(!$var->deny_type || !$var->deny_content) return new Object(-1,'msg_invalid_request');

            $args->module_srl = $this->module_srl;
            $args->deny_type = $var->deny_type;
            $args->deny_content = $var->deny_content;
            return $this->insertDeny($args);
        }

        /**
         * @brief deny insert
         **/
        function procTextyleDenyInsertList(){
            $var = Context::getRequestVars();
            $deny = array();
            $deny['S'] = explode('|',$var->homepage);
            $deny['M'] = explode('|',$var->email_address);
            $deny['I'] = explode('|',$var->ipaddress);
            $deny['N'] = explode('|',$var->user_name);

            $i=0;
            foreach($deny as $type => $contents){
                foreach($contents as $k => $content){
                    if(!trim($content)) continue;
                    unset($args);
                    $args->textyle_deny_srl = getNextSequence();
                    $args->module_srl = $this->module_srl;
                    $args->deny_type = $type;
                    $args->deny_content = trim($content);

                    $output = $this->insertDeny($args);
                    if(!$output->toBool()) return $output;
                    $i++;
                }
            }

            return $output;
        }

        function insertDeny($obj){
            $oTextyleModel = &getModel('textyle');
            $check = $oTextyleModel->_checkDeny($obj->module_srl,$obj->deny_type,$obj->deny_content);
            if($check) return new Object();

            $this->deleteTextyleDenyFile($obj->module_srl);
            $args->textyle_deny_srl = getNextSequence();
            $args->module_srl = $obj->module_srl;
            $args->deny_type = $obj->deny_type;
            $args->deny_content = $obj->deny_content;
            $output = executeQuery('textyle.insertTextyleDeny', $args);

            return $output;
        }

        /**
         * @brief deny delete
         **/
        function procTextyleDenyDelete(){
            $s_args = Context::getRequestVars();
            if(!$s_args->textyle_deny_srl) return new Object(-1,'msg_invalid_request');
            $this->deleteTextyleDenyFile($this->module_srl);
            $args->textyle_deny_srl = $s_args->textyle_deny_srl;
            $output = executeQuery('textyle.deleteTextyleDeny', $args);
            if(!$output->toBool()) return $output;
        }

        function deleteTextyleDenyFile($module_srl){
            $oTextyleModel = &getModel('textyle');
            $cache_file = $oTextyleModel->getDenyCacheFile($module_srl);
            FileHandler::removeFile($cache_file);
        }

        function procTextylePostsave(){
            $oDocumentModel = &getModel('document');
            $oDocumentController = &getController('document');
            $oTextyleModel = &getModel('textyle');
            
            $var = Context::getRequestVars();
            $site_module_info = Context::get('site_module_info');

            $oDocument = $oDocumentModel->getDocument($var->document_srl);

            //$var->allow_comment = ($oDocument->allowComment()) ? 'Y' : 'N';
            //$var->allow_trackback = ($oDocument->allowTrackback()) ? 'Y' : 'N';
            $var->is_secret = ($oDocument->isSecret()) ? 'Y' : 'N';
            //$var->tags = $oDocument->get('tags');
            
            if($oDocument->isExists()) {
                $output = $this->updatePost($var);
                $document_srl = $oDocument->document_srl;
                $alias = $oDocumentModel->getAlias($output->get('document_srl'));
	            if($var->alias != $alias){
	                $output = $oDocumentController->insertAlias($this->module_srl,$output->get('document_srl'),$var->alias);
	                if(!$output->toBool()) return $output;
	            }
            } else {
                $output = $this->savePost($var);
                if(!$output->toBool()) return $output;
                if(preg_match('/<IMG/', $var->content) || preg_match('/<img/', $var->content)) unset($GLOBALS['XE_DOCUMENT_LIST'][$output->get('document_srl')]);
				$oDocument = $oDocumentModel->getDocument($output->get('document_srl'));
				$vars = $oDocument->getObjectVars();
	            $vars->tags = $var->tags;
	            $output = $this->updatePost($vars);
                    $document_srl = $output->get('document_srl');
	            if(!$output->toBool()) return $output;
	            $alias = $oDocumentModel->getAlias($output->get('document_srl'));
	            if($var->alias != $alias){
	                $output = $oDocumentController->insertAlias($this->module_srl,$output->get('document_srl'),$var->alias);
	                if(!$output->toBool()) return $output;
	            }
            }
            
            $this->add('mid', Context::get('mid'));
            $this->add('document_srl', $document_srl);

            if($var->publish == 'Y') 
            {
                $args->document_srl = $document_srl;
                $output = executeQuery('textyle.getPublishLogs', $args);
                $isPublished = (!$output->data) ? false : true;

                if(!$isPublished){
                    $args->update_order = $args->list_order = getNextSequence()*-1;
                    $args->document_srl = $document_srl;
                    $args->module_srl = $this->module_srl;
                    $output = executeQuery('document.updateDocumentOrder',$args);
                }
                $oPublish = $oTextyleModel->getPublishObject($this->module_srl, $document_srl);
                $oPublish->trackbacks = array();
                
                foreach($var as $key => $val) {
                    if(preg_match('/^trackback_(url|charset)([0-9]*)$/i', $key, $match)&&$val) $publish_option->trackbacks[(int)$match[2]][$match[1]] = $val;
                    else if(preg_match('/^blogapi_([0-9]+)$/i', $key, $match) && $val=='Y') $publish_option->blogapis[$match[1]]->send_api = true;
                    else if(preg_match('/^blogapi_category_([0-9]+)$/i', $key, $match)) $publish_option->blogapis[$match[1]]->category = $val;
                    else if($key == 'send_me2day' && $val == 'Y') $publish_option->send_me2day = true;
                    else if($key == 'send_twitter' && $val == 'Y') $publish_option->send_twitter = true;
                }
                
                if(count($publish_option->trackbacks)) foreach($publish_option->trackbacks as $key => $val) $oPublish->addTrackback($val['url'], $val['charset']);
                if(count($publish_option->blogapis)) foreach($publish_option->blogapis as $key => $val) if($val->send_api) $oPublish->addBlogApi($key, $val->category);
                
                $oPublish->setMe2day($publish_option->send_me2day);
                $oPublish->setTwitter($publish_option->send_twitter);
                $oPublish->save();
                
                $var->publish_date_yyyymmdd = preg_replace("/[^0-9]/",'',$var->publish_date_yyyymmdd);
                if($var->subscription=='Y' && $var->publish_date_yyyymmdd) {
                    $var->publish_date_hh = preg_replace("/[^0-9]/",'',$var->publish_date_hh);
                    $var->publish_date_ii = preg_replace("/[^0-9]/",'',$var->publish_date_ii);
                    $var->publish_date_hh = $var->publish_date_hh ? $var->publish_date_hh : 0;
                    $var->publish_date_ii = $var->publish_date_ii ? $var->publish_date_ii : 0;
                    $var->publish_date = sprintf("%s%02d%02d00",$var->publish_date_yyyymmdd, $var->publish_date_hh , $var->publish_date_ii);

                    if($var->publish_date > date('YmdHis')){
                        $args->document_srl = $document_srl;
                        $args->module_srl = $this->module_srl;
                        $args->publish_date = $var->publish_date;

                        $output = executeQuery('textyle.deleteTextyleSubscriptionByDocumentSrl', $args);
                        $output = executeQuery('textyle.insertTextyleSubscription', $args);
                        if(!$output->toBool()) return $output;

                        // update module_srl for subscription
                        $args->module_srl = abs($this->module_srl) * -1;
                        $args->category_srl = $var->category_srl;
                        $output = executeQuery('document.updateDocumentModule', $args);
                        if(!$output->toBool()) return $output;

                        $this->syncTextyleSubscriptionDate($this->module_srl);
                        $subscripted = true;
                    }
                }
                $oDocumentController->updateCategoryCount($this->module_srl,$var->category_srl);
                if(!$subscripted) {
                    executeQuery('textyle.deleteTextyleSubscriptionByDocumentSrl', $args);
                    $oPublish->publish();
                }
                $this->setRedirectUrl( getSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'), 'act', 'dispTextyleToolPostManageList') );
            }  
            else {
                $this->setRedirectUrl( getSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'), 'act', 'dispTextyleToolPostManageWrite', 'document_srl', $this->get('document_srl'),'preview',$var->preview) );
             }
            $this->setMessage('success_saved');
        }

        function procTextylePostPublish() {
            $oTextyleModel = &getModel('textyle');
            $oDocumentModel = &getModel('document');
            $oDocumentController = &getController('document');
            $subscripted = false;

            $var = Context::getRequestVars();
            $oDocument = $oDocumentModel->getDocument($var->document_srl);
            $vars = $oDocument->getObjectVars();
            $vars->tags = $var->tags;
            $vars->module_srl = $this->module_srl;
            $vars->category_srl = $var->category_srl;
            $vars->allow_comment = $var->allow_comment;
            $vars->allow_trackback = $var->allow_trackback;

            $output = $this->updatePost($vars);
            if(!$output->toBool()) return $output;

            $args->document_srl = $var->document_srl;
            $output = executeQuery('textyle.getPublishLogs', $args);
            $isPublished = (!$output->data) ? false : true;

            if(!$isPublished){
                $args->update_order = $args->list_order = getNextSequence()*-1;
                $args->document_srl = $var->document_srl;
                $args->module_srl = $this->module_srl;
                $output = executeQuery('document.updateDocumentOrder',$args);
            }

            $var->alias = trim($var->alias);
            if($var->use_alias=='Y' && $var->alias){
                $output = $oDocumentController->insertAlias($this->module_srl,$var->document_srl,$var->alias);
                if(!$output->toBool()) return $output;
            }

            $this->add('mid', Context::get('mid'));
            $this->add('document_srl', $output->get('document_srl'));

            $oPublish = $oTextyleModel->getPublishObject($this->module_srl, $var->document_srl);
            $oPublish->trackbacks = array();

            foreach($var as $key => $val) {
                if(preg_match('/^trackback_(url|charset)([0-9]*)$/i', $key, $match)&&$val) $publish_option->trackbacks[(int)$match[2]][$match[1]] = $val;
                else if(preg_match('/^blogapi_([0-9]+)$/i', $key, $match) && $val=='Y') $publish_option->blogapis[$match[1]]->send_api = true;
                else if(preg_match('/^blogapi_category_([0-9]+)$/i', $key, $match)) $publish_option->blogapis[$match[1]]->category = $val;
                else if($key == 'send_me2day' && $val == 'Y') $publish_option->send_me2day = true;
                else if($key == 'send_twitter' && $val == 'Y') $publish_option->send_twitter = true;
            }

            if(count($publish_option->trackbacks)) foreach($publish_option->trackbacks as $key => $val) $oPublish->addTrackback($val['url'], $val['charset']);
            if(count($publish_option->blogapis)) foreach($publish_option->blogapis as $key => $val) if($val->send_api) $oPublish->addBlogApi($key, $val->category);

            $oPublish->setMe2day($publish_option->send_me2day);
            $oPublish->setTwitter($publish_option->send_twitter);
            $oPublish->save();
            $var->publish_date_yyyymmdd = preg_replace("/[^0-9]/",'',$var->publish_date_yyyymmdd);
            if($var->subscription=='Y' && $var->publish_date_yyyymmdd) {
                $var->publish_date_hh = preg_replace("/[^0-9]/",'',$var->publish_date_hh);
                $var->publish_date_ii = preg_replace("/[^0-9]/",'',$var->publish_date_ii);
                $var->publish_date_hh = $var->publish_date_hh ? $var->publish_date_hh : 0;
                $var->publish_date_ii = $var->publish_date_ii ? $var->publish_date_ii : 0;
                $var->publish_date = sprintf("%s%02d%02d00",$var->publish_date_yyyymmdd, $var->publish_date_hh , $var->publish_date_ii);

                if($var->publish_date > date('YmdHis')){
                    $args->document_srl = $var->document_srl;
                    $args->module_srl = $this->module_srl;
                    $args->publish_date = $var->publish_date;

                    $output = executeQuery('textyle.deleteTextyleSubscriptionByDocumentSrl', $args);
                    $output = executeQuery('textyle.insertTextyleSubscription', $args);
                    if(!$output->toBool()) return $output;

                    // update module_srl for subscription
                    $args->module_srl = abs($this->module_srl) * -1;
					$args->category_srl = $var->category_srl;
                    $output = executeQuery('document.updateDocumentModule', $args);
                    if(!$output->toBool()) return $output;

                    $this->syncTextyleSubscriptionDate($this->module_srl);
                    $subscripted = true;
                }
            }

            if(!$subscripted) {
                executeQuery('textyle.deleteTextyleSubscriptionByDocumentSrl', $args);
                $oPublish->publish();
            }
        }

        function savePost($args) {
        	$oDocumentController = &getController('document');

            $logged_info = Context::get('logged_info');
            $args->module_srl = $logged_info->member_srl;

            $output = $oDocumentController->insertDocument($args);
            return $output;
        }

        function updatePost($args){
            $oDocumentModel = &getModel('document');
            $oDocumentController = &getController('document');

            $oDocument = $oDocumentModel->getDocument($args->document_srl);
			if(!$args->module_srl) $args->module_srl = $oDocument->get('module_srl');
            if(!$args->category_srl) $args->category_srl = $oDocument->get('category_srl');
            if(!$oDocument->isExists()) return new Object(-1,'msg_invalid_request');

            $output = $oDocumentController->updateDocument($oDocument, $args);
            return $output;
        }

        function insertPost($args) {
            $oDocumentController = &getController('document');

            $output = $oDocumentController->insertDocument($args);
            return $output;
        }

        function procTextylePostTrashRestore(){
            $document_srl = Context::get('document_srl');

            if(preg_match('/^([0-9,]+)$/',$document_srl)) $document_srl = explode(',',$document_srl);
            else $document_srl = array($document_srl);

            $oDocumentAdminController = &getAdminController('document');
            $oDocumentController = &getController('document');
            $oDocumentModel = &getModel('document');

            $oDB = &DB::getInstance();
            $oDB->begin();

            $args->document_srl = join(',',$document_srl);
            $output = executeQueryArray('document.getTrashByDocumentSrl', $args);

            $trash = array();
            if($output->data){
                foreach($output->data as $k => $v){
                    $trash[] = $v;
                }
            }

            $updated_category_srls = array();
            $logged_info = Context::get('logged_info');
            foreach($trash as $k => $v){
                $output = $oDocumentAdminController->restoreTrash($v->trash_srl);
                if(!$output->toBool()){
                     return new Object(-1, 'fail_to_trash');
                }else{
                    $args->module_srl = $logged_info->member_srl;
                    $args->document_srl = $v->document_srl;
                    $output = executeQuery('document.updateDocumentModule', $args);

                    $oDocument = $oDocumentModel->getDocument($v->document_srl);
                    $obj = $oDocument->getObjectVars();
                    $updated_category_srls[] = $obj->category_srl;
                    $trigger_output = ModuleHandler::triggerCall('document.updateDocument', 'after', $obj);
                    if(!$trigger_output->toBool()) {
                        $oDB->rollback();
                        return $trigger_output;
                    }
                }

                // TO DO : move DocumentController
                unset($trash_args);
                $trash_args->document_srls = $v->document_srl;
                $trash_args->module_srl = $v->module_srl;
                $output = executeQuery('comment.updateCommentModule', $trash_args);
                if(!$output->toBool()){
                    $oDB->rollback();
                    return new Object(-1, 'fail_to_trash');
                }

                $output = executeQuery('trackback.updateTrackbackModule', $trash_args);
                if(!$output->toBool()){
                    $oDB->rollback();
                    return new Object(-1, 'fail_to_trash');
                }

            }

            // TODO : move code document trash
            $updated_category_srls = array_unique($updated_category_srls);
            foreach($updated_category_srls as $k => $srl){
                $oDocumentController->updateCategoryCount($this->module_srl,$srl);
            }

            $oDB->commit();
            return $output;
        }


        function procTextylePostTrash(){
            $document_srl = Context::get('document_srl');

            if(preg_match('/^([0-9,]+)$/',$document_srl)) $document_srls = explode(',',$document_srl);
            else $document_srls = array($document_srl);

            $oDocumentController = &getController('document');
            $oDocumentModel = &getModel('document');
            $oCommentController = &getController('comment');

            $oDB = &DB::getInstance();
            $oDB->begin();

            for($i=0,$c=count($document_srls);$i<$c;$i++) {
                unset($args);
                $args->document_srl = $document_srls[$i];
                $oDocument = $oDocumentModel->getDocument($args->document_srl);

                // if subscription
                if($oDocument->get('module_srl')<0){
                    $args->module_srl = abs($this->module_srl);
                    $oDocument->add('module_srl',$args->module_srl);
                    $output = executeQuery('document.updateDocumentModule', $args);
                    $output = $this->deletePostSubscription($args->document_srl);
                    unset($args->module_srl);
                }

                $output = $oDocumentController->moveDocumentToTrash($args);
                if(!$output->toBool()){
                     return new Object(-1, 'fail_to_trash');
                }else{
                    $obj = $oDocument->getObjectVars();
                    $trigger_output = ModuleHandler::triggerCall('document.updateDocument', 'after', $obj);
                    if(!$trigger_output->toBool()) {
                        $oDB->rollback();
                        return $trigger_output;
                    }
                }

                // TO DO : move DocumentController
                unset($trash_args);
                $trash_args->document_srls = $document_srls[$i];
                $trash_args->module_srl = 0;
                $output = executeQuery('comment.updateCommentModule', $trash_args);
                if(!$output->toBool()){
                    $oDB->rollback();
                    return new Object(-1, 'fail_to_trash');
                }

                $output = executeQuery('trackback.updateTrackbackModule', $trash_args);
                if(!$output->toBool()){
                    $oDB->rollback();
                    return new Object(-1, 'fail_to_trash');
                }


            }

            $oDB->commit();
            $msg_code = 'success_trashed';
            $this->setMessage($msg_code);
        }

        function deletePostSubscription($document_srl){
            $args->document_srl = $document_srl;
            $output = executeQuery('textyle.deleteTextyleSubscriptionByDocumentSrl', $args);

            // sync to textyle
            $this->syncTextyleSubscriptionDate($module_srl);

            return $output;
        }

        function syncTextyleSubscriptionDate($module_srl){
            $oTextyleModel = &getModel('textyle');
            $output = $oTextyleModel->getSubscriptionMinPublishDate($module_srl);

            if($output->data && $output->data->publish_date){
                $args->subscription_date = $output->data->publish_date;
            }else{
                $args->subscription_date = '';
            }
            $output = $this->updateTextyleInfo($module_srl,$args);
        }


        function procTextylePostDelete(){
            $document_srl = Context::get('document_srl');
            if(preg_match('/^([0-9,]+)$/',$document_srl)) $document_srl = explode(',',$document_srl);
            else $document_srl = array($document_srl);
            if(count($document_srl)<1) return new Object(-1,'msg_invalid_request');

            $output = $this->deletePost($document_srl);
            if(!$output->toBool()) return $output;
            $this->setMessage('success_trashed');
        }

        function deletePost($document_srl, $is_admin=false){
            $document_srl = is_array($document_srl) ? $document_srl : array($document_srl);

            // delete document
            $oDocumentController = &getController('document');

            $oDB = &DB::getInstance();
            $oDB->begin();
            for($i=0,$c=count($document_srl);$i<$c;$i++) {
                $output = $oDocumentController->deleteDocument($document_srl[$i], $is_admin);
                if(!$output->toBool()) return new Object(-1, 'fail_to_delete');
            }
            $oDB->commit();
            return $output;
        }

        function triggerInsertComment(&$obj){
            $module_info = Context::get('module_info');
            if($module_info->module != 'textyle') return new Object();
            if(!$obj->comment_srl) return new Object();

            $args->module_srl = $module_info->module_srl;
            $args->nick_name = $obj->nick_name;
            $args->member_srl = $obj->member_srl;
            $args->homepage = $obj->homepage;
            $args->comment_count = 1;
            $this->updateTextyleSupporter($args);
            return new Object();
        }

        function triggerDeleteComment(&$obj){
            $module_info = Context::get('module_info');
            if($module_info->module != 'textyle') return new Object();
            if(!$obj->comment_srl) return new Object();

            $args->module_srl = $module_info->module_srl;
            $args->nick_name = $obj->nick_name;
            $args->member_srl = $obj->member_srl;
            $args->homepage = $obj->homepage;
            $args->comment_count = -1;
            $this->updateTextyleSupporter($args);

            return new Object();
        }

        function triggerInsertTrackback(&$obj){
            $module_info = Context::get('module_info');
            if($module_info->module != 'textyle') return new Object();
            if(!$obj->trackback_srl) return new Object();

            $args->module_srl = $module_info->module_srl;
            $args->nick_name = $obj->blog_name;
            $args->member_srl = 0;
            $args->homepage = $obj->url;
            $args->trackback_count = 1;
            $this->updateTextyleSupporter($args);

            return new Object();
        }

        function triggerDeleteTrackback(&$obj){
            $module_info = Context::get('module_info');
            if($module_info->module != 'textyle') return new Object();
            if(!$obj->trackback_srl) return new Object();

            $args->module_srl = $module_info->module_srl;
            $args->nick_name = $obj->blog_name;
            $args->member_srl = 0;
            $args->homepage = $obj->url;
            $args->trackback_count = -1;
            $this->updateTextyleSupporter($args);

            return new Object();
        }

        function updateTextyleSupporter($obj){
            $oMemberModel = &getModel('member');

            $args->module_srl = $obj->module_srl;
            if($obj->member_srl) $args->member_srl = $obj->member_srl;
            else if($obj->nick_name) $args->nick_name = $obj->nick_name;
            else if($obj->homepage) $args->homepage = $obj->homepage;
            $args->regdate = date("Ym");

            $output = executeQuery('textyle.getTextyleSupporter', $args);
            $sup = $output->data;

            $args->member_srl = $obj->member_srl;
            if($obj->member_srl) {
                $member_info = $oMemberModel->getMemberInfoByMemberSrl($obj->member_srl);
                $args->nick_name = $member_info->nick_name;
                if($member_info->blog) $args->homepage = $member_info->blog;
                else $args->homepage = $member_info->homepage;
            } else {
                $args->nick_name = $obj->nick_name;
                $args->homepage = $obj->homepage;
            }
            $args->comment_count = $sup->comment_count+$obj->comment_count;
            $args->trackback_count = $sup->trackback_count+$obj->trackback_count;
            $args->guestbook_count = $sup->guestbook_count+$obj->guestbook_count;
            $args->total_count = $args->comment_count+$args->trackback_count+$args->guestbook_count;

            if($sup->textyle_supporter_srl) {
                $args->textyle_supporter_srl = $sup->textyle_supporter_srl;
                $output = executeQuery('textyle.updateTextyleSupporter',$args);
            } else {
                $args->textyle_supporter_srl = getNextSequence();
                $output = executeQuery('textyle.insertTextyleSupporter',$args);
            }

            return $output;
        }

        function procTextylePostItemsCategoryMove(){
            $document_srl = Context::get('document_srl');
            $category_srl = Context::get('category_srl');
            if(!$document_srl || !$category_srl) return new Object(-1,'msg_invalid_request');

            if(preg_match('/^([0-9,]+)$/',$document_srl)) $document_srl = explode(',',$document_srl);
            else $document_srl = array($document_srl);

            $oDocumentModel = &getModel('document');
            $oDocumentAdminController = &getAdminController('document');

            // check temp saved documents
            $document_srls = array();
            $temp_saved_document_srls = array();
            $temp_saved_module_srl = 0;

            foreach($document_srl as $k => $v){
                $oDocument = $oDocumentModel->getDocument($v);
                if($oDocument->get('module_srl') == $this->module_srl){
                    $document_srls[] = $v;
                }else{
                    $temp_saved_document_srls[] = $v;
                    $temp_saved_module_srl = $oDocument->get('module_srl');
                }
            }

            // published document
            if(count($document_srls)>0){
                $oDocumentAdminController->moveDocumentModule($document_srls,$this->module_srl,$category_srl);
            }

            // temp saved document
            if(count($temp_saved_document_srls)>0){
                $oDocumentAdminController->moveDocumentModule($temp_saved_document_srls,$temp_saved_module_srl,$category_srl);
            }

        }

        function procTextylePostItemsSetSecret(){
            $document_srl = Context::get('document_srl');
            $set_secret = Context::get('set_secret');
            if(!$document_srl) return new Object(-1,'msg_invalid_request');
            $set_secret = $set_secret=='Y'?'Y':'N';

            if(preg_match('/^([0-9,]+)$/',$document_srl)) $document_srl = explode(',',$document_srl);
            else $document_srl = array($document_srl);

            $output = $this->setTextylePostItemsSecret($document_srl,$set_secret);
            return $output;
        }

        function setTextylePostItemsSecret($document_srls,$set_secret='Y')
		{
			$oDB = &DB::getInstance();
			$oDB->begin();

			$oDocumentModel = getModel('document');
			$oDocumentController = getController('document');

			$documentList = $oDocumentModel->getDocuments($document_srls);

			$status = NULL;
			if($set_secret == 'Y')
			{
				$status = $oDocumentModel->getConfigStatus('secret');
			}
			else
			{
				$status = $oDocumentModel->getConfigStatus('public');
			}

			if(is_array($documentList))
			{
				foreach($documentList AS $key=>$oDocument)
				{
					$obj = $oDocument->getObjectVars();
					$obj->status = $status;

					$output = $oDocumentController->updateDocument($oDocument, $obj);
					if(!$output->toBool())
					{
                        $oDB->rollback();
						return $output;
					}
				}
			}
			$oDB->commit();

            return $output;
        }

        function procTextylePostItemsAllowCommentTrackback(){
            $var = Context::getRequestVars();
            $allow_comment = $var->allow_comment!='Y'?'N':'Y';
            $allow_trackback = $var->allow_trackback!='Y'?'N':'Y';
            $document_srl = $var->document_srl;

            if(preg_match('/^([0-9,]+)$/',$document_srl)) $document_srl = explode(',',$document_srl);
            else $document_srl = array($document_srl);

            $args->allow_comment = $allow_comment;
            $args->trackback = $allow_trackback;
            $args->document_srl = join(',',$document_srl);
            $args->module_srl = $this->module_srl;
            $output = executeQuery('document.updateDocumentsAllowCommentTrackback',$args);
            return $output;
        }


        /**
         * @brief publish subscripted post 
         **/
        function publishSubscriptedPost($module_srl){
            $now = date('YmdHis');
            $oTextyleModel = &getModel('textyle');

            $args->module_srl = $module_srl;
            $args->less_publish_date = $now;
            $output = $oTextyleModel->getSubscription($args);
            $published = false;
            if($output->data){
                foreach($output->data as $k => $v){
                    // publish
                    if($v->publish_date <= $now){
                        $this->_updatePublishPost($v->document_srl,$v->publish_date,$module_srl);
                        $published = true;
                    }
                }
            }

            if($published){
                $this->_deleteSubscription($module_srl,$now);
                $this->syncTextyleSubscriptionDate($module_srl);
            }
        }

        function _updatePublishPost($document_srl,$publish_date,$module_srl){
            $oTextyleModel = &getModel('textyle');

            $args->module_srl = $module_srl;
            $args->document_srl = $document_srl;
            $args->list_order =  getNextSequence() * -1;
            $args->update_order = $args->list_order;
            $args->regdate = $publish_date;

            $output = executeQuery('document.updateDocumentOrder',$args);
            if(!$output->toBool()) return $output;

            $oPublish = $oTextyleModel->getPublishObject($module_srl, $document_srl);
            $oPublish->publish();
        }

        function _deleteSubscription($module_srl,$less_publish_date){
            $args->module_srl = $module_srl;
            $args->publish_date = $less_publish_date;
            $output = executeQuery('textyle.deleteTextyleSubscriptionByPublishDate',$args);
            return $output;

        }

        function procTextyleConfigPostwriteInsert(){
            $oEditorModel = &getModel('editor');
            $oModuleController = &getController('module');

            if(in_array(strtolower('dispTextyleToolConfigPostwrite'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            $vars = Context::getRequestVars();

            $args->post_editor_skin = $vars->post_editor_skin ? $vars->post_editor_skin : $vars->etc_post_editor_skin;
            $args->post_use_prefix = $vars->post_use_prefix =='Y' ? 'Y' : 'N';
            $args->post_use_suffix = $vars->post_use_suffix =='Y' ? 'Y' : 'N';
            $args->post_prefix = $vars->post_prefix;
            $args->post_suffix = $vars->post_suffix;
			$args->module_srl = $this->module_srl;
            $output = executeQuery('textyle.updateTextyleWriteConfig',$args);
            if(!$output->toBool()) return $output;

            $editor_config = $oEditorModel->getEditorConfig($this->module_srl);

            $editor_config->editor_skin = $args->post_editor_skin;
            $editor_config->content_font = $vars->font_family;
            if($editor_config->content_font) {
                $font_list = array();
                $fonts = explode(',',$editor_config->content_font);
                for($i=0,$c=count($fonts);$i<$c;$i++) {
                    $font = trim(str_replace(array('"','\''),'',$fonts[$i]));
                    if(!$font) continue;
                    $font_list[] = $font;
                }
                if(count($font_list)) $editor_config->content_font = '"'.implode('","',$font_list).'"';
            }
            $editor_config->content_font_size = $vars->font_size;

            $oModuleController->insertModulePartConfig('editor',$this->module_srl,$editor_config);
        }

	    /**
		 * @brief Delete Comments: for backward-compatibility
		 */
		function procTextyleDeleteComment() {
			$this->procTextyleCommentDelete();
		}

        /**
         * @brief textyle comment delete
         **/
        function procTextyleCommentDelete() {
            $comment_srl = Context::get('comment_srl');
            if(!$comment_srl) return $this->doError('msg_invalid_request');

            $oCommentController = &getController('comment');

            $output = $oCommentController->deleteComment($comment_srl, $this->grant->manager);
            if(!$output->toBool()) return $output;

            $this->add('comment_srl', $comment_srl);
            $this->add('document_srl', $output->get('document_srl'));
            $this->setMessage('success_deleted');
        }

        /**
         * @brief textyle colorset modify
         **/
        function procTextyleColorsetModify() {
            $oTextyleModel = &getModel('textyle');
            $mytextyle = $oTextyleModel->getMemberTextyle();
            if(!$mytextyle->isExists()) return new Object(-1, 'msg_not_permitted');

            $colorset = Context::get('colorset');
            if(!$colorset) return new Object(-1,'msg_invalid_request');

            $this->updateTextyleColorset($mytextyle->getModuleSrl(), $colorset);

            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('move_mytextyle');
        }

        /**
         * @brief textyle delete tag
         **/
        function procTextyleTagDelete(){
            $selected_tag = trim(Context::get('selected_tag'));
            if(!$selected_tag) return new Object(-1,'msg_invalid_request');

            // get document_srl
            $args->tag = $selected_tag;
            $args->module_srl = $this->module_srl;

            $oTagModel = &getModel('tag');
            $output = $oTagModel->getDocumentSrlByTag($args);
            $document_srl = array();
            if($output->data){
                foreach($output->data as $k => $v) $document_srl[] = $v->document_srl;
            }

            // delete tag table
            $output = executeQuery('tag.deleteTagByTag', $args);
            if(!$output->toBool()) return $output;

            $this->syncDocumentTags($document_srl);
        }


        /**
         * @brief textyle update tag
         * not good;;
         **/
        function procTextyleTagUpdate(){
            $selected_tag = trim(Context::get('selected_tag'));
            $new_tag = trim(Context::get('tag'));

            if(!$selected_tag || !$new_tag) return new Object(-1,'msg_invalid_request');

            // get document_srl
            $args->tag = $selected_tag;
            $args->module_srl = $this->module_srl;

            $oTagModel = &getModel('tag');
            $output = $oTagModel->getDocumentSrlByTag($args);
            $document_srl = array();
            if($output->data){
                foreach($output->data as $k => $v) $document_srl[] = $v->document_srl;
            }

            // delete tag table
            $output = executeQuery('tag.deleteTagByTag', $args);
            if(!$output->toBool()) return $output;

            $args->tag = $new_tag;
            $has_tag_document_srl = array();
            $output = $oTagModel->getDocumentSrlByTag($args);
            if($output->data){
                foreach($output->data as $k => $v) $has_tag_document_srl[] = $v->document_srl;
            }

            for($i=0,$c=count($document_srl);$i<$c;$i++){
                $args->document_srl = $document_srl[$i];

                // already has
                if(in_array($args->document_srl,$has_tag_document_srl)) continue;

                $args->tag_srl = getNextSequence();
                $args->tag = $new_tag;
                $output = executeQuery('tag.insertTag', $args);
            }

            // sync documents table
            $this->syncDocumentTags($document_srl);
            $this->add('selected_tag',$new_tag);
        }

        /**
         * @brief sync documents table tags
         **/
        function syncDocumentTags($document_srls){
            $args->document_srl = join(',',$document_srls);
            $output = executeQueryArray('tag.getAllTagList', $args);

            $tags = array();
            if($output->data){
                foreach($output->data as $k => $v){
                    if(!is_array($tags[$v->document_srl])) $tags[$v->document_srl] = array();
                    $tags[$v->document_srl][] = $v->tag;
                }
            }

            unset($args);
            for($i=0,$c=count($document_srls);$i<$c;$i++){
                $args->document_srl = $document_srls[$i];
                if(is_array($tags[$args->document_srl])) $args->tags = join(',',$tags[$args->document_srl]);
                else $args->tags = "";
                $output = executeQuery('document.updateDocumentTags', $args);
            }
        }

        /**
         * @brief content tag modify
         **/
        function procTextyleContentTagModify(){
            $req = Context::getRequestVars();

            $oDocumentModel = &getModel('document');

            $oDocumentController = &getController('document');
            $oDocument = $oDocumentModel->getDocument($req->document_srl);
            $oDocument->add('tags',$req->textyle_content_tag);
            $obj = $oDocument->getObjectVars();

            $output = $oDocumentController->updateDocument($oDocument, $obj);
            $this->setMessage('success_updated');
        }

        function procTextyleToolLayoutConfigSkin() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oTextyleModel = &getModel('textyle');

            if(in_array(strtolower('dispTextyleToolLayoutConfigSkin'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            $skin = Context::get('skin');
            if(!is_dir($this->module_path.'skins/'.$skin)) return new Object();

            $module_info  = $oModuleModel->getModuleInfoByModuleSrl($this->module_srl);
            $module_info->skin = $skin;
            $output = $oModuleController->updateModule($module_info);
            if(!$output->toBool()) return $output;

            FileHandler::removeDir($oTextyleModel->getTextylePath($this->module_srl));
            FileHandler::copyDir($this->module_path.'skins/'.$skin, $oTextyleModel->getTextylePath($this->module_srl));
        }

        function procTextyleToolLayoutConfigMobileSkin() {
            $oModuleModel = &getModel('module');
            $oModuleController = &getController('module');
            $oTextyleModel = &getModel('textyle');

            if(in_array(strtolower('dispTextyleToolLayoutConfigMobileSkin'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');
            $mskin = Context::get('mskin');

			$module_srls = array($this->module_srl);
			/*
			$args->site_srl = $this->site_srl;
			$output = executeQueryArray('textyle.getExtraMenuModuleSrls',$args);
			if($output->toBool() && $output->data){
				foreach($output->data as $data){
					$module_srls[] = $data->module_srl;
				}
			}
			*/

			if(!$mskin){
				$use_mobile = 'N';
			}else{
				$use_mobile = 'Y';
				if($mskin && !is_dir($this->module_path.'m.skins/'.$mskin)) return new Object();
			}

			foreach($module_srls as $module_srl){
				unset($module_info);
				$module_info  = $oModuleModel->getModuleInfoByModuleSrl($this->module_srl);
				$module_info->mskin = $mskin;

				$module_info->module_srl = $module_srl;
				$module_info->site_srl = $this->site_srl;
				$module_info->use_mobile = $use_mobile;
				$module_info->is_mskin_fix = 'Y';
				$module_info->mskin = $mskin;
				$output = $oModuleController->updateModule($module_info);
			}
        }

        function procTextyleToolLayoutResetConfigSkin() {
            $oModuleModel = &getModel('module');
            $module_info  = $oModuleModel->getModuleInfoByModuleSrl($this->module_srl);
            $skin = $module_info->skin;

            $this->resetSkin($this->module_srl,$skin);
        }

        function resetSkin($module_srl,$skin=null){
            if(!$skin) $skin = $this->skin;
            if(!file_exists($this->module_path.'skins/'.$skin)) $skin = $this->skin;
            $oTextyleModel = &getModel('textyle');
            FileHandler::removeDir($oTextyleModel->getTextylePath($module_srl));
            FileHandler::copyDir($this->module_path.'skins/'.$skin, $oTextyleModel->getTextylePath($module_srl));
        }


        function procTextyleToolLayoutConfigEdit() {
            if(in_array(strtolower('dispTextyleToolLayoutConfigEdit'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            $oTextyleModel = &getModel('textyle');
            $skin_path = $oTextyleModel->getTextylePath($this->module_srl);

            $skin_file_list = $oTextyleModel->getTextyleUserSkinFileList($this->module_srl);
            foreach($skin_file_list as $file){
                $content = Context::get($file);
                if($this->_checkDisabledFunction($content)) return new Object(-1,'msg_used_disabled_function');
                FileHandler::writeFile($skin_path.$file, $content);
            }
        }

        function procTextyleToolUserImageUpload(){
            if(!Context::isUploaded()) exit();
            if(!$this->module_srl) exit();

            $image = Context::get('user_image');
            if(!is_uploaded_file($image['tmp_name'])) exit();
            if(!preg_match('/\.(gif|jpg|jpeg|gif|png|swf|flv)$/i', $image['name'])) return false;

            $oTextyleModel = &getModel('textyle');
            $user_image_path = sprintf('%suser_images/',$oTextyleModel->getTextylePath($this->module_srl));
            if(!is_dir($user_image_path)) FileHandler::makeDir($user_image_path);

            $filename = strtolower($image['name']);
            if($filename != urlencode($filename)){
                $ext = substr(strrchr($filename,'.'),1);
                $filename = sprintf('%s.%s', md5($filename), $ext);
            }

            if(file_exists($user_image_path . $filename)) @unlink($user_image_path . $filename);
            if(!move_uploaded_file($image['tmp_name'], $user_image_path . $filename )) return false;

            Context::set('msg',Context::getLang('success_upload'));
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile("top_refresh.html");
        }

        function procTextyleToolUserImageDelete(){
            if(!$this->module_srl) exit();
            $filename = Context::get('filename');

            $oTextyleModel = &getModel('textyle');
            $user_image_path = sprintf('%suser_images/',$oTextyleModel->getTextylePath($this->module_srl));

            if(file_exists($user_image_path . $filename)) @unlink($user_image_path . $filename);
            $this->setMessage('success_deleted');
        }

         function procTextyleToolUserSkinExport(){
            if(!$this->module_srl) return new Object('-1','msg_invalid_request');

            $oTextyleModel = &getModel('textyle');
            $skin_path = FileHandler::getRealPath($oTextyleModel->getTextylePath($this->module_srl));

            $tar_list = FileHandler::readDir($skin_path,'/(\.css|\.html|\.htm|\.js)$/');

            $img_list = FileHandler::readDir($skin_path."img",'/(\.png|\.jpeg|\.jpg|\.gif|\.swf)$/');
            for($i=0,$c=count($img_list);$i<$c;$i++) $tar_list[] = 'img/' . $img_list[$i];

            $userimages_list = FileHandler::readDir($skin_path."user_images",'/(\.png|\.jpeg|\.jpg|\.gif|\.swf)$/');
            for($i=0,$c=count($userimages_list);$i<$c;$i++) $tar_list[] = 'user_images/' . $userimages_list[$i];

            require_once(_XE_PATH_.'libs/tar.class.php');
            chdir($skin_path);
            $tar = new tar();

            $replace_path = getNumberingPath($this->module_srl,3);
            foreach($tar_list as $key => $file) $tar->addFile($file,$replace_path,'__TEXTYLE_SKIN_PATH__');

            $stream = $tar->toTarStream();
            $filename = 'TextyleUserSkin_' . date('YmdHis') . '.tar';
            header("Cache-Control: ");
            header("Pragma: ");
            header("Content-Type: application/x-compressed");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header('Content-Disposition: attachment; filename="'. $filename .'"');
            header("Content-Transfer-Encoding: binary\n");
            echo $stream;

            Context::close();
            exit();
         }

         function procTextyleToolUserSkinImport(){
            if(!$this->module_srl) exit();

            // check upload
            if(!Context::isUploaded()) exit();
            $file = Context::get('file');
            if(!is_uploaded_file($file['tmp_name'])) exit();
            if(!preg_match('/\.(tar)$/i', $file['name'])) exit();

            $oTextyleModel = &getModel('textyle');
            $skin_path = FileHandler::getRealPath($oTextyleModel->getTextylePath($this->module_srl));

            $tar_file = $skin_path . 'textyle_skin.tar';

            FileHandler::removeDir($skin_path);
            FileHandler::makeDir($skin_path);

            if(!move_uploaded_file($file['tmp_name'], $tar_file)) exit();

            require_once(_XE_PATH_.'libs/tar.class.php');

            $tar = new tar();
            $tar->openTAR($tar_file);

            if(!$tar->getFile('textyle.html')) return;

            $replace_path = getNumberingPath($this->module_srl,3);
            foreach($tar->files as $key => $info) {
                FileHandler::writeFile($skin_path . $info['name'],str_replace('__TEXTYLE_SKIN_PATH__',$replace_path,$info['file']));
            }

            FileHandler::removeFile($tar_file);
        }




        function _checkDisabledFunction($str){
            if(preg_match('!<\?.*\?>!is',$str,$match)) return true;

            $disabled = array(
                    // file
                    'fopen','link','unlink','popen','symlink','touch','readfile','rmdir','mkdir','rename','copy','delete','file_get_contents','file_put_contents','tmpname','parse_ini_file'
                    // dir
                    ,'dir'
                   // database
                   ,'mysql','sqlite','PDO','cubird','ibase','pg_','_pconnect','_connect','oci'
                   // network /etc
                   ,'fsockopen','pfsockopen','shmop_','shm_','sem_','dl','ini_','php','zend','pear','header','create_function','call_*','imap','openlog','socket','ob_','cookie','eval','exec','shell_exec','passthru'
                   // XE
                   ,'filehandler','displayhandler','xehttprequest','context','getmodel','getcontroller','getview','getadminmodel','getadmincontroller','getadminview','getdbinfo','executequery','executequeryarray'
            );
            unset($match);

            $disabled = '/('.implode($disabled, '|').')/i';
            preg_match_all('!<\!--@(.*?)-->!is', $str, $match1);
            preg_match_all('/ ([^(^ ]*) ?\(/i', ' '.join(' ',$match1[1]),$match_func1);
            preg_match_all('/{([^{]*)}/i',$str,$match2);
            preg_match_all('/ ([^(^ ]*) ?\(/i', ' '.join(' ',$match2[1]),$match_func2);
            $match1 = array_unique($match_func1[1]);
            $match2 = array_unique($match_func2[1]);
            preg_match($disabled, implode('|', $match1), $matches1);
            preg_match($disabled, implode('|', $match2), $matches2);

            if(count($matches1) || count($matches2)) return true;

            return false;
        }

        /**
         * @brief textyle insert config
         **/
        function insertTextyleConfig($textyle) {
            $oModuleController = &getController('module');
            $oModuleController->insertModuleConfig('textyle', $textyle);
        }

        /**
         * @brief textyle update browser title
         **/
        function updateTextyleBrowserTitle($module_srl, $browser_title) {
            $args->module_srl = $module_srl;
            $args->browser_title = $browser_title;
            return executeQuery('textyle.updateTextyleBrowserTitle', $args);
        }
        function procTextyleEnableRss() {
            $oTextyleModel = &getModel('textyle');
            $mytextyle = $oTextyleModel->getMemberTextyle();
            if(!$mytextyle->isExists()) return new Object(-1,'msg_not_permitted');

            $oRssAdminController = &getAdminController('rss');
            $oRssAdminController->setRssModuleConfig($mytextyle->getModuleSrl(), 'Y');
        }

        function procTextyleDisableRss() {
            $oTextyleModel = &getModel('textyle');
            $mytextyle = $oTextyleModel->getMemberTextyle();
            if(!$mytextyle->isExists()) return new Object(-1,'msg_not_permitted');

            $oRssAdminController = &getAdminController('rss');
            $oRssAdminController->setRssModuleConfig($mytextyle->getModuleSrl(), 'N');
        }

       /**
         * @brief trigger member menu
         **
        function triggerMemberMenu(&$obj) {
            $member_srl = Context::get('target_srl');
            if(!$member_srl) return new Object();

            $args->member_srl = $member_srl;
            $output = executeQuery('textyle.getTextyle', $args);
            if(!$output->toBool() || !$output->data) return new Object();

            $site_module_info = Context::get('site_module_info');
            $default_url = Context::getDefaultUrl();

            if($site_module_info->site_srl && !$default_url) return new Object();

            $url = getSiteUrl($default_url, '','mid',$output->data->mid);
            $oMemberController = &getController('member');
            $oMemberController->addMemberPopupMenu($url, 'textyle', './modules/textyle/tpl/images/textyle.gif');

            return new Object();
        }
        */

        /**
         * @brief action forward apply layout
         **/
        function triggerApplyLayout(&$oModule) {
            if(!$oModule || $oModule->getLayoutFile()=='popup_layout.html') return new Object();

            if(Context::get('module')=='admin') return new Object();

            if(in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) return new Object();

			if($oModule->act == 'dispMemberLogout') return new Object();

            $site_module_info = Context::get('site_module_info');
            if(!$site_module_info || !$site_module_info->site_srl || $site_module_info->mid != $this->textyle_mid) return new Object();

            $oModuleModel = &getModel('module');
            $xml_info = $oModuleModel->getModuleActionXml('textyle');
            if($oModule->mid == $this->textyle_mid && isset($xml_info->action->{$oModule->act})) return new Object();

            $oTextyleModel = &getModel('textyle');
            $oTextyleView = &getView('textyle');

            Context::set('layout',null);

            if($oTextyleModel->isAttachedMenu($oModule->act)) {
                $oTextyleView->initTool($oModule, true);
            } else {
				if(Mobile::isFromMobilePhone())
				{
					$oTextyleView = &getMobile('textyle');
				}
                $oTextyleView->initService($oModule, true);
            }
            return new Object();
        }

        /**
         * @brief insert referer
         **/
        function insertReferer($oDocument) {
            if($_SESSION['textyleReferer'][$oDocument->document_srl]) return;
            $_SESSION['textyleReferer'][$oDocument->document_srl] = true;
            $referer = $_SERVER['HTTP_REFERER'];
            if(!$referer) return;

            $_url = parse_url(Context::getRequestUri());
            $url_info = parse_url($referer);
            if($_url['host']==$url_info['host']) return;

            $args->module_srl = $oDocument->get('module_srl');
            $args->document_srl = $oDocument->get('document_srl');
            $args->regdate = date("Ymd");
            $args->host = $url_info['host'];
            $output = executeQuery('textyle.getRefererHost', $args);
            if(!$output->data->textyle_host_srl) {
                $args->textyle_host_srl = getNextSequence();
                $output = executeQuery('textyle.insertRefererHost', $args);
            } else {
                $args->textyle_host_srl = $output->data->textyle_host_srl;
                $output = executeQuery('textyle.updateRefererHost', $args);
            }
            if(!$output->toBool()) return;

            if(preg_match('/(query|q|search_keyword)=([^&]+)/i',$referer, $matches)) $args->link_word = trim($matches[2]);
            $args->link_word = detectUTF8($args->link_word, true);
            $args->referer_url = $referer;

            $output = executeQuery('textyle.getReferer', $args);
            if($output->data->textyle_referer_srl) {
                $uArgs->textyle_referer_srl = $output->data->textyle_referer_srl;
                return executeQuery('textyle.updateReferer', $uArgs);
            } else {
                $args->textyle_referer_srl = getNextSequence();
                return executeQuery('textyle.insertReferer', $args);
            }
        }

        function procTextyleToolImportPrepare() {
            $oImporterAdminController = &getAdminController('importer');
            $oImporterAdminController->procImporterAdminPreProcessing();

            if(in_array(strtolower('dispTextyleToolConfigData'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            $xml_file = Context::get('xml_file');
            if(!$xml_file || $xml_file == 'http://') return new Object(-1,'msg_migration_file_is_null');

            $this->setError($oImporterAdminController->getError());
            $this->setMessage($oImporterAdminController->getMessage());
            $this->adds($oImporterAdminController->getVariables());
        }

        function procTextyleToolImport() {
            $oImporterAdminController = &getAdminController('importer');
            $oImporterAdminController->procImporterAdminImport();
            $this->setError($oImporterAdminController->getError());
            $this->setMessage($oImporterAdminController->getMessage());
            $this->adds($oImporterAdminController->getVariables());
        }

        function procTextyleInsertBlogApi() {
            if(in_array(strtolower('dispTextyleToolConfigBlogApi'),$this->custom_menu->hidden_menu)) return new Object(-1,'msg_invalid_request');

            $msg = Context::getLang('msg_blogapi_registration');
            $vars = Context::getRequestVars();

            $vars->module_srl = $this->module_srl;
            $check_vars = array('blogapi_site_url', 'blogapi_site_title', 'blogapi_url', 'blogapi_user_id', 'blogapi_password');
            foreach($check_vars as $key => $val) {
                if(!$vars->{$val}) return new Object(-1,$msg[$key]);
            }
            $output = $this->insertBlogApiService($vars);
            return $output;
        }

        function insertBlogApiService($vars){
            if(!preg_match('/^(http|https)/',$vars->blogapi_url)) $vars->blogapi_url = 'http://'.$vars->blogapi_url;

            if($vars->api_srl) {
                $output = executeQuery('textyle.getApiInfo',$vars);
                if($output->data->api_srl) return executeQuery('textyle.updateBlogAPI', $vars);
            }
            $vars->api_srl = getNextSequence();
            if(!isset($vars->blogapi_host_provider)) $vars->blogapi_host_provider = 0;
            return executeQuery('textyle.insertBlogAPI', $vars);
        }

        function procTextyleToggleEnableAPI() {
            $vars->api_srl = Context::get('api_srl');
            $vars->module_srl = $this->module_srl;
            $output = executeQuery('textyle.getApiInfo',$vars);
            if(!$output->data) return new Object(-1,'msg_invalid_request');

            if($output->data->enable == 'Y') $vars->enable = 'N';
            else $vars->enable = 'Y';

            $output = executeQuery('textyle.updateEnableBlogAPI', $vars);
            if(!$output->toBool()) return $output;

            $this->add('enable', $vars->enable);
        }

        function procTextyleDeleteBlogApi() {
            $api_srl = Context::get('api_srl');
            if(!$api_srl) return new Object(-1,'msg_invalid_request');

            $output = $this->deleteBlogApi($this->module_srl,$api_srl);
            return $output;
        }

        function deleteBlogApis($module_srl){
            $args->module_srl = $module_srl;

            $output = executeQuery('textyle.deleteTextyleApis',$args);
            return $output;
        }

        function deleteBlogApi($module_srl,$api_srl){
            $args->module_srl = $module_srl;
            $args->api_srl = $api_srl;

            $output = executeQuery('textyle.deleteTextyleApi',$args);
            return $output;
        }


        function procTextyleToolInit(){
            if(!$this->site_srl) return new Object(-1,'msg_invalid_request');

            $oTextyleAdminController = &getAdminController('textyle');
            $output = $oTextyleAdminController->initTextyle($this->site_srl);
            return $output;
        }

		function procTextyleRequestExport(){
            if(!$this->site_srl) return new Object(-1,'msg_invalid_request');

			$oTextyleAdminController = &getAdminController('textyle');
			$oTextyleAdminController->deleteExport($this->site_srl);

			$args->export_type = Context::get('export_type');
			if(!$args->export_type || $args->export_type!='xexml') $args->export_type='ttxml';

			$logged_info = Context::get('logged_info');
			$args->module_srl = $this->module_srl;
			$args->site_srl = $this->site_srl;
			$args->member_srl = $logged_info->member_srl;

			$output = executeQuery('textyle.insertExport',$args);
			return $output;
		}

		function procTextyleToolExtraMenuInsert(){
            $args = Context::getRequestVars();
            $menu_name = trim(Context::get('menu_name'));
			$menu_mid = Context::get('menu_mid');
			
			$oModuleModel = &getModel('module');
			$oTextyleModel = &getModel('textyle');
			$oModuleController = &getController('module');
            $oDocumentController = &getController('document');
			$config = $oTextyleModel->getModulePartConfig($this->module_srl);
			
            if($args->insert_type == "module_page"){
            	$menu->type = 'module_page';
				$module_type = Context::get('module_type');
				if(!$menu_name || !$module_type || !$menu_mid) return new Object(-1,'msg_invalid_request');
	
				$module_count = $oModuleModel->getModuleCount($this->site_srl, $module_type);
				if($module_count >= $config->allow_service[$module_type]) return new Object(-1,'msg_module_count_exceed');
				$args->site_srl = $this->site_srl;
				$args->mid = $menu_mid;
				$args->browser_title = $menu_name;
				$args->module = $module_type;
				$output = $oModuleController->insertModule($args);
				if(!$output->toBool()) return $output;
            }else {
            	$menu->type = 'text_page';
				if(!$menu_name || !$menu_mid) return new Object(-1,'msg_invalid_request');
				
				$module_count = $oModuleModel->getModuleCount($this->site_srl, 'page');
				if($module_count >= $config->allow_service['page']) return new Object(-1,'msg_module_count_exceed');
	            	
	            $output = $oDocumentController->insertDocument($args);
				
				$args->site_srl = $this->site_srl;
				$args->mid = $menu_mid;
				$args->browser_title = $menu_name;
				$args->module = 'page';
				$args->page_type = 'WIDGET';
	            $args->content = '<img src="./common/tpl/images/widget_bg.jpg" class="zbxe_widget_output" widget="widgetContent" style="float: left; width: 100%;" body="" document_srl="'.$output->get('document_srl').'" widget_padding_left="0" widget_padding_right="0" widget_padding_top="0" widget_padding_bottom="0"  /> ';
				$output = $oModuleController->insertModule($args);
				if(!$output->toBool()) return $output;
            }
            
			$menu->name = $menu_name;
			$menu->site_srl = $this->site_srl;
			$menu->module_srl = $output->get('module_srl');
			$menu->list_order = $menu->module_srl;
			$output = executeQuery('textyle.insertExtraMenu',$menu);
		}

		function procTextyleToolExtraMenuUpdate(){
                    $args = Context::getRequestVars();
                    $menu_name = trim(Context::get('menu_name'));
                    $menu_mid= Context::get('menu_mid');
                    if(!$menu_name || !$menu_mid) return new Object(-1,'msg_invalid_request');

                    $oModuleModel = &getModel('module');
                    $oDocumentModel = &getModel('document');
                    $oDocumentController = &getController('document');
                    $module_info = $oModuleModel->getModuleInfoByMid($menu_mid,$this->site_srl);
                    if(!$module_info) return new Object(-1,'msg_invalid_request');
                    
                    $buff = trim($module_info->content);
                    $oXmlParser = new XmlParser();
                    $xml_doc = $oXmlParser->parse(trim($buff));
                    $document_srl = $xml_doc->img->attrs->document_srl;
                    $args->document_srl = $document_srl;
                    $oDocument = $oDocumentModel->getDocument($document_srl);
                    $args->module_srl = $oDocument->module_srl;
                    $args->category_srl = $oDocument->category_srl;
                    $output = $oDocumentController->updateDocument($oDocument, $args);
                    
                    $args->name = $menu_name;
                    $args->module_srl = $module_info->module_srl;
                    $output = executeQuery('textyle.updateExtraMenuName',$args);
		}

		function procTextyleToolExtraMenuDelete(){
            $menu_mid = Context::get('menu_mid');
			if(!$menu_mid) return new Object(-1,'msg_invalid_request');

            $oModuleModel = &getModel('module');
			$oModuleController = &getController('module');

			$module_info = $oModuleModel->getModuleInfoByMid($menu_mid, $this->site_srl);
			if($module_info && $module_info->module_srl) $output = $oModuleController->deleteModule($module_info->module_srl);

			$args->module_srl = $module_info->module_srl;
			$output = executeQuery('textyle.deleteExtraMenu',$args);
		}

		function procTextyleToolExtraMenuSort(){
			$menu_mids = Context::get('menu_mids');
			if(!$menu_mids) return new Object(-1,'msg_invalid_request');

			$order = array();
			$menu_mids = explode(',',$menu_mids);
			foreach($menu_mids as $k => $mid){
				$order[$mid] = $k;
			}

			$args->site_srl = $this->site_srl;
			$output = executeQueryArray('textyle.getExtraMenus',$args);
			if(!$output->toBool() || !$output->data) return $output;

			foreach($output->data as $k => $menu){
				$order[$menu->mid] = $menu;
			}

			$list_order = 0;
			foreach($order as $menu){
				if($list_order != $menu->list_order){
					$args->module_srl = $menu->module_srl;
					$args->list_order = $list_order;
					$output = executeQuery('textyle.updateExtraMenuListOrder',$args);
				}
				$list_order++;
			}
		}

		function procTextyleToolLive(){
			$_SESSION['live'] = time();
		}
    }
?>
