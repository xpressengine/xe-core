<?php
    class publishObject {
        var $module_srl = null;
        var $document_srl = null;
        var $oDocument = null;

        var $trackbacks = array(); // [url]->charset, log
        var $blogapis = array(); // [api_srl]->category, postid, log
        var $publish_me2day = false; // true/false
        var $published_me2day = false; // true/false
        var $publish_twitter = false; // true/false
        var $published_twitter = false; // true/false

        function publishObject($module_srl, $document_srl = 0) {
            $this->module_srl = $module_srl;
            $this->document_srl = $document_srl;
            if(!$document_srl) return;

            $oDocumentModel = &getModel('document');
            $this->oDocument = $oDocumentModel->getDocument($document_srl);
            if(!$this->oDocument->isExists()) return;

            $args->document_srl = $this->document_srl = $document_srl;
            $output = executeQuery('textyle.getPublishLogs', $args);
            if(!$output->data) return;
            $data = unserialize($output->data->logs);

            $this->trackbacks = is_array($data->trackbacks)?$data->trackbacks:array();
            $this->blogapis = is_array($data->blogapis)?$data->blogapis:array();
            $this->publish_me2day = $data->publish_me2day==true?true:false;
            $this->published_me2day = $data->publish_me2day==true?true:false;
            $this->publish_twitter = $data->published_twitter==true?true:false;
            $this->published_twitter = $data->published_twitter==true?true:false;
        }

        function getBlogAPIInfo($type, $url, $user_id, $password, $blogid) {
            if(!preg_match('/^(http|https)/',$url)) $url = 'http://'.$url;

            $msg_lang = Context::getLang('msg_blogapi_registration');

            if(!$user_id) return new Object(-1,$msg_lang[3]);
            if(!$password ) return new Object(-1,$msg_lang[4]);
            if(!$url) return new Object(-1,$msg_lang[2]);

            switch($type) {
                case 'blogger' :
                        require_once(_XE_PATH_.'modules/textyle/libs/blogger.class.php');
                        $oBlogger = new blogger($url, $user_id, $password);
                        $output = $oBlogger->getUsersBlogs();
                        if(!$output->toBool()) return $output;
                    break;
                case 'movalbletype' :
                    break;
                default :
                        require_once(_XE_PATH_.'modules/textyle/libs/metaweblog.class.php');
                        $oMeta = new metaWebLog($url, $user_id, $password, $blogid);
                        $output = $oMeta->getUsersBlogs();
                        if(!$output->toBool()) return $output;
                    break;
            }
            return $output;
        }

        function getTrackbacks() {
            if(!$this->oDocument->isExists()) return array();
            return $this->trackbacks;
        }

        function getApis() {
            if(!$this->oDocument->isExists()) return array();

            $args->module_srl = $this->module_srl;
            $output = executeQueryArray('textyle.getApis', $args);
            if(!$output->data) return array();

            foreach($output->data as $key => $val) {
                switch($val->blogapi_type) {
                    case 'blogger' :
                            require_once(_XE_PATH_.'modules/textyle/libs/blogger.class.php');
                            $oBlogger = new blogger($val->blogapi_url, $val->blogapi_user_id, $val->blogapi_password);
                            $output = $oBlogger->getCategories();
                            if(!$output->toBool()) return $output;
                        break;
                    case 'movalbletype' :
                        break;
                    default :
                            require_once(_XE_PATH_.'modules/textyle/libs/metaweblog.class.php');
                            $oMeta = new metaWebLog($val->blogapi_url, $val->blogapi_user_id, $val->blogapi_password);
                            $val->categories = $oMeta->getCategories();
                        break;
                }
                if($this->blogapis[$val->api_srl]) {
                    $val->log = $this->blogapis[$val->api_srl]->log;
                    $val->category = $this->blogapis[$val->api_srl]->category;
                }
                $apis[$val->api_srl] = $val;
            }
            return $apis;
        }

        function isMe2dayPublished() {
            return $this->published_me2day;
        }

        function isTwitterPublished() {
            return $this->published_twitter;
        }

        function addTrackback($trackback_url, $charset = 'UTF-8') {
            if(!$trackback_url || isset($this->trackbacks[$trackback_url])) return;
            $this->trackbacks[$trackback_url]->charset = $charset;
            $this->trackbacks[$trackback_url]->log = '';
        }

        function addBlogApi($api_srl, $category = null) {
            if(!$api_srl) return;
            $this->blogapis[$api_srl]->reserve = true;
            $this->blogapis[$api_srl]->category = $category;
        }

        function setMe2day($flag = false) {
            $this->publish_me2day = $flag;
        }

        function setTwitter($flag = false) {
            $this->publish_twitter = $flag;
        }

        function save() {
            $logs->trackbacks = $this->trackbacks;
            $logs->blogapis = $this->blogapis;
            $logs->publish_me2day = $this->publish_me2day;
            $logs->published_me2day = $this->published_me2day;
            $logs->publish_twitter = $this->publish_twitter;
            $logs->published_twitter = $this->published_twitter;

            $args->document_srl = $this->document_srl;
            $args->module_srl = $this->module_srl;
            $args->logs = serialize($logs);
            $output = executeQuery('textyle.deletePublishLog', $args);
            $output = executeQuery('textyle.insertPublishLog', $args);
        }

        function publish() {
            $oTextyleModel = &getModel('textyle');
            $oTrackbackController = &getController('trackback');

            if(!$this->oDocument->isExists()) return;

            $oTextyle = $oTextyleModel->getTextyle($this->module_srl);

            if(count($this->trackbacks)) {
                foreach($this->trackbacks as $trackback_url => $val) {
                    $output = $oTrackbackController->sendTrackback($this->oDocument, $trackback_url, $val->charset);
                    if($output->toBool()) $this->trackbacks[$trackback_url]->log = Context::getLang('published').' ('.date("Y-m-d H:i").')';
                    else $this->trackbacks[$trackback_url]->log = $output->getMessage().' ('.date("Y-m-d H:i").')';
                }
            }

            // fixed link
            $original_content = $this->oDocument->get('content');
            $original_content = preg_replace('/href="(\.\/)([^"]*)"/i','href="'.getFullUrl().'$2"',$original_content);
            if(count($this->blogapis)) {
                $apis = $this->getApis();
                foreach($this->blogapis as $api_srl => $val) {
                    if(!$apis[$api_srl] || !$val->reserve) continue;

                    $this->oDocument->add('content',$original_content);
                    if($val->postid) $output = $this->modifyBlogApi($apis[$api_srl], $val->postid, $val->category);
                    else $output = $this->sendBlogApi($apis[$api_srl], $val->category);

                    if($output->toBool()) {
                        $this->blogapis[$api_srl]->postid = $output->get('postid');
                        $this->blogapis[$api_srl]->log = Context::getLang('published').' ('.date("Y-m-d H:i").')';
                    } else {
                        $this->blogapis[$api_srl]->postid = null;
                        $this->blogapis[$api_srl]->log = $output->getMessage().' ('.date("Y-m-d H:i").')';
                    }
                    $this->blogapis[$api_srl]->reserve = false;
                }
            }

            if($this->publish_me2day && $oTextyle->getEnableMe2day()) $this->sendMe2day($oTextyle->getMe2dayUserID(), $oTextyle->getMe2dayUserKey());
            if($this->publish_twitter && $oTextyle->getEnableTwitter()) $this->sendTwitter($oTextyle->getTwitterConsumerKey(), $oTextyle->getTwitterConsumerSecret(), $oTextyle->getTwitterOauthToken(), $oTextyle->getTwitterOauthTokenSecret());

            $this->save();
        }


        function sendBlogApi($api, $category) {
            if(!$this->oDocument->isExists()) return;
            switch($api->blogapi_type) {
                case 'blogger' :
                        require_once(_XE_PATH_.'modules/textyle/libs/blogger.class.php');
                        $oBlogger = new blogger($api->blogapi_url, $api->blogapi_user_id, $api->blogapi_password);
                        $output = $oBlogger->newPost($this->oDocument, $category);
                    break;
                case 'movalbletype' :
                    break;
                default :
                        require_once(_XE_PATH_.'modules/textyle/libs/metaweblog.class.php');
                        $oMeta = new metaWebLog($api->blogapi_url, $api->blogapi_user_id, $api->blogapi_password);
                        $output = $oMeta->newPost($this->oDocument, $category);
                    break;

            }

            $args->textyle_blogapi_logs_srl = getNextSequence();
            $args->document_srl = $this->oDocument->document_srl;
            $args->module_srl = $this->oDocument->get('module_srl');
            $args->blogapi_url = $api->blogapi_url;
            $args->blogapi_id = $api->blogapi_user_id;
            $args->sended = $output->toBool() ? 'Y' : 'N';
            executeQuery('textyle.insertBlogApiLog',$args);
            return $output;
        }

        function modifyBlogApi($api, $postid, $category) {
            if(!$this->oDocument->isExists()) return;

            switch($api->blogapi_type) {
                case 'blogger' :
                        require_once(_XE_PATH_.'modules/textyle/libs/blogger.class.php');
                        $oBlogger = new blogger($api->blogapi_url, $api->blogapi_user_id, $api->blogapi_password);
                        $output = $oBlogger->editPost($postid, $this->oDocument, $category);
                    break;
                case 'movalbletype' :
                    break;
                default :
                        require_once(_XE_PATH_.'modules/textyle/libs/metaweblog.class.php');
                        $oMeta = new metaWebLog($api->blogapi_url, $api->blogapi_user_id, $api->blogapi_password);
                        $output = $oMeta->editPost($postid, $this->oDocument, $category);

                    break;

            }
            return $output;
        }

        function sendMe2day($user_id, $user_key) {
            require_once(_XE_PATH_.'modules/textyle/libs/me2day.api.php');

            if(!$user_id || !$user_key) return;
            $oMe2 = new me2api($user_id, $user_key);
            $output = $oMe2->doPost( sprintf('"%s":%s', $this->oDocument->getTitleText(), $this->oDocument->getPermanentUrl()) , $this->oDocument->get('tags'));
            if($output->toBool()) $this->published_me2day = true;
        }

        function sendTwitter($consumer_key, $consumer_secret, $oauth_token, $oauth_token_secret) {
			require_once(_XE_PATH_.'modules/textyle/libs/twitteroauth.php');
            if(!$consumer_key || !$consumer_secret || !$oauth_token || !$oauth_token_secret) return;
            $twitteroauth = new TwitterOAuth($consumer_key, $consumer_secret , $oauth_token , $oauth_token_secret);
			$shortURL= file_get_contents("http://tinyurl.com/api-create.php?url=" . $this->oDocument->getPermanentUrl()); 
            $status = sprintf('%s %s', $this->oDocument->getTitleText(), $shortURL);
            $response = $twitteroauth->post('statuses/update', array("status" => $status));
          
            //$buff = FileHandler::getRemoteResource($url, 'status='.urlencode(sprintf('%s %s', $this->oDocument->getTitleText(), $this->oDocument->getPermanentUrl())), 3, 'POST', 'application/x-www-form-urlencoded');
            $this->published_twitter = true;
        }

    }
?>
