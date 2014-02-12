<?php
    /**
     * @class  resourceController
     * @author NHN (developers@xpressengine.com)
     * @brief  resource controller class
     **/

    class resourceController extends resource {

        function init() {
        }

        function procResourceInsertPackage() {
            if(!$this->module_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');
            $site_module_info = Context::get('site_module_info');

            $args = Context::gets('category_srl','title','license','homepage','description', 'path');
            if($this->module_info->resource_use_path=='Y') $args->path = Context::get('path');
            if($this->module_info->module_category) $have_instance = Context::get('have_instance');

            // 카테고리 확인
            $oDocumentModel = getModel('document');
            $category = $oDocumentModel->getCategoryList($this->module_srl);
            if(!$category[$args->category_srl]->grant)
            {
            	return new Object(-1, 'msg_invalid_request');
            }
            
			// path 확인
			if($args->path)
			{
				$oModel = getModel('resource');
				$package = $oModel->getPackageByPath($args->path);
				if($package)
				{
					return new Object(-1, 'msg_exists_path');
				}
			}

            foreach($args as $key => $val) if(!trim($val)) return new Object(-1,'msg_invalid_request');
            if($args->homepage&&!preg_match('/:\/\//',$args->homepage)) $args->homepage = 'http://'.$args->homepage;

			// 페이지 생성 여부 확인
			if($this->module_info->module_category == $args->category_srl)
			{
				$args->have_instance = $have_instance == 'Y' ? 'Y' : 'N';
			}

			// 모듈 스킨일 경우 모듈 정보 추출
			$skin_category = explode(',', $this->module_info->module_skin_category);
			if(in_array($args->category_srl, $skin_category))
			{
				preg_match('@/([^/]+)/(?:skins|m.skins)/@', $args->path, $m);
				if($m[1])
				{
					$args->parent_program = $m[1];
				}
			}

            $args->package_srl = getNextSequence();
            $args->module_srl = $this->module_srl;
            $args->member_srl = $logged_info->member_srl;
            $args->list_order = -1*$args->package_srl;

            if($this->grant->manager) $args->status = 'accepted';
            else {
                $output = executeQuery('resource.isAcceptedOnce', $args);
                if($output->data->count>0) $args->status = 'accepted';
            }

            $output = executeQuery('resource.insertPackage', $args);
            if(!$output->toBool()) return $output;

            if($this->module_info->resource_notify_mail) {
                $message = '';
                foreach($args as $key => $val) $message .= $key." : ".$val."<br/>\r\n";
                $message.= "URL : ".getFullSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'), 'act', 'dispResourceManage');
                $this->notify($this->module_info->resource_notify_mail, Context::getLang('resource_new_notify_title'), $message);
            }

            $this->setMessage('success_registed');
            $this->setRedirectUrl(getNotEncodedSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','dispResourcePackage','package_srl',$args->package_srl));
        }

        function procResourceModifyPackage() {
            $oResourceModel = &getModel('resource');

            if(!$this->module_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');
            $site_module_info = Context::get('site_module_info');

            $args = Context::gets('package_srl', 'title','license','homepage','description', 'path');
            if($this->module_info->resource_use_path=='Y') $args->path = Context::get('path');
            foreach($args as $key => $val) if(!trim($val)) return new Object(-1,'msg_invalid_request');
            if($args->homepage&&!preg_match('/:\/\//',$args->homepage)) $args->homepage = 'http://'.$args->homepage;

            $selected_package = $oResourceModel->getPackage($this->module_srl, $args->package_srl);
            if(!$selected_package->package_srl) return new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            $category_srl = Context::get('package_category');
            if($category_srl && $this->grant->manager) $args->category_srl = $category_srl;

            // 카테고리 확인
            if(isset($args->category_srl))
            {
	            $oDocumentModel = getModel('document');
	            $category = $oDocumentModel->getCategoryList($this->module_srl);
	            if(!$category[$args->category_srl]->grant)
	            {
	            	return new Object(-1, 'msg_invalid_request');
	            }
            }
            
            // path 확인
            if($args->path)
            {
            	$oModel = getModel('resource');
            	$package = $oModel->getPackageByPath($args->path);
            	if($package && $package->package_srl != $selected_package->package_srl)
            	{
            		return new Object(-1, 'msg_exists_path');
            	}
            }
            
			// 페이지 생성 여부 확인
			$category_srl = $args->category_srl ? $args->category_srl : $selected_package->category_srl;
			if($this->module_info->module_category == $category_srl)
			{
			 	$args->have_instance = Context::get('have_instance') == 'Y' ? 'Y' : 'N'; 
			}

            $output = executeQuery('resource.modifyPackage', $args);
            if(!$output->toBool()) return $output;

            if($this->module_info->resource_notify_mail) {
                $message = '';
                foreach($args as $key => $val) $message .= $key." : ".$val."<br/>\r\n";
                $message.= "URL : ".getFullSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'), 'package_srl', $args->package_srl);
                $this->notify($this->module_info->resource_notify_mail, Context::getLang('resource_modify_notify_title'), $message);
            }

            $this->setMessage('success_updated');
            $this->setRedirectUrl(getNotEncodedSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','dispResourcePackage','package_srl',$args->package_srl));
        }

        function procResourceDeletePackage() {
            $oResourceModel = &getModel('resource');

            if(!$this->module_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');
            $site_module_info = Context::get('site_module_info');

            $package_srl = Context::get('package_srl');
            if(!$package_srl) return new Object(-1,'msg_invalid_request');
            $selected_package = $oResourceModel->getPackage($this->module_srl, $package_srl);
            if(!$selected_package->package_srl) return new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            $args->package_srl = $package_srl;
            $args->module_srl = $this->module_srl;
            $args->member_srl = $logged_info->member_srl;
            $output = executeQuery('resource.deletePackage', $args);
            if(!$output->toBool()) return $output;

            if($this->module_info->resource_notify_mail) {
                $message = '';
                foreach($args as $key => $val) $message .= $key." : ".$val."<br/>\r\n";
                $message.= "URL : ".getFullSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'), 'package_srl', $args->package_srl);
                $this->notify($this->module_info->resource_notify_mail, Context::getLang('resource_delete_notify_title'), $message);
            }


            $this->setMessage('success_deleted');
            $this->setRedirectUrl(getNotEncodedSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','dispResourcePackageList'));
        }

        function procResourceChangeStatus() {
            $oCommunicationController = &getController('communication');
            $oResourceModel = &getModel('resource');

            if(!$this->module_srl) return new Object(-1,'msg_invalid_request');

            $args = Context::gets('package_srl', 'status');
            foreach($args as $key => $val) if(!trim($val)) return new Object(-1,'msg_invalid_request');
            if(!in_array($args->status, array('accepted','reservation','waiting'))) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');

            $selected_package = $oResourceModel->getPackage($this->module_srl, $args->package_srl);
            if(!$selected_package->package_srl) return new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            $output = executeQuery('resource.updatePackageStatus', $args);
            if(!$output->toBool()) return $output;

            $logged_info = Context::get('logged_info');

            $content = str_replace(array('[title]','[status]'), array($selected_package->title, Context::getLang('package_'.$args->status)), Context::getLang('resource_status_changed_message'));
            $oCommunicationController->sendMessage($logged_info->member_srl, $selected_package->member_srl, Context::getLang('resource_status_changed'), $content, false);
        }

		public function procResourceAttachOneTime()
		{
			$oDB = DB::getInstance();
			$oDB->begin();

			$output = $this->procResourceAttach(FALSE);
			if(!$output->toBool())
			{
				$oDB->rollback();
				return $output;
			}

			$item_srl = $output->get('item_srl');
			Context::set('item_srl', $output->get('item_srl'), TRUE);
			Context::set('document_srl', $output->get('document_srl'), TRUE);

			$output = $this->procResourceAttachFile(FALSE);
			if(!$output->toBool())
			{
				$oFileController = getController('file'); /* @var $oFileController fileController */
				$oFileController->deleteFiles($item_srl);
				$oDB->rollback();
				return $output;
			}

			$oDB->commit();
		}

        function procResourceAttach($proc = TRUE) {
            $oResourceModel = getModel('resource');
            $oDocumentController = getController('document');

            $args = Context::gets('package_srl','version','description');
            foreach($args as $key => $val) if(!trim($val)) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');

            $selected_package = $oResourceModel->getPackage($this->module_srl, $args->package_srl);
            if(!$selected_package) return new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            if($proc)
            {
				$oDB = DB::getInstance();
				$oDB->begin();
            }

            $doc_args = new stdClass();
            $doc_args->document_srl = Context::get('document_srl') ? Context::get('document_srl') : getNextSequence();
            $doc_args->category_srl = $selected_package->category_srl;
            $doc_args->module_srl = $this->module_srl;
            $doc_args->content = $args->description;
            $doc_args->title = sprintf('%s ver. %s', $selected_package->title, $args->version);
            $doc_args->list_order = $doc_args->document_srl*-1;
            $doc_args->tags = Context::get('tag');
            $doc_args->allow_comment = 'Y';
			$doc_args->commentStatus = 'ALLOW';
            $output = $oDocumentController->insertDocument($doc_args);
            if(!$output->toBool())
            {
            	if($proc)
            	{
            		$oDB->rollback();
            	}
            	return $output;
            }

            $args->item_srl = getNextSequence();
            $args->document_srl = $doc_args->document_srl;
            $args->module_srl = $this->module_srl;
            $args->list_order = -1 * $args->item_srl;
            $output = executeQuery('resource.insertItem', $args);
            if(!$output->toBool())
            {
            	if($proc)
            	{
            		$oDB->rollback();
            	}
            	return $output;
            }

            $pargs = new stdClass();
            $pargs->module_srl = $this->module_srl;
            $pargs->package_srl = $args->package_srl;
            $pargs->update_order = $args->list_order;
            $pargs->latest_item_srl = $args->item_srl;
            $output = executeQuery('resource.updatePackage', $pargs);
            if(!$output->toBool())
			{
				if($proc)
				{
					$oDB->rollback();
				}
				return $output;
			}

            if($this->module_info->resource_notify_mail) {
                $message = '';
                foreach($args as $key => $val) $message .= $key." : ".$val."<br/>\r\n";
                $message.= "URL : ".getFullSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'), 'package_srl', $args->package_srl);
                $this->notify($this->module_info->resource_notify_mail, Context::getLang('resource_attach_notify_title'), $message);
            }

            $this->insertDependency($this->module_srl, $args->package_srl, $args->item_srl, trim(Context::get('dependency')));

            if($proc)
            {
				$oDB->commit();
        }

            // for backward compatibility
            $this->add('document_srl', $args->document_srl);
            $this->add('item_srl', $args->item_srl);

            return $this;
        }

        function insertDependency($module_srl, $package_srl, $item_srl, $targets) {
            $args->module_srl = $module_srl;
            $args->item_srl = $item_srl;
            executeQuery('resource.deleteDependency', $args);

            $d = explode(',',$targets);
            $arr_dependency = array();
            for($i=0,$c=count($d);$i<$c;$i++) {
                if((int)trim($d[$i])) $arr_dependency[] = (int)trim($d[$i]);
            }
            if(!count($arr_dependency)) return;

            $dargs->item_srl = implode(',',$arr_dependency);
            $output = executeQueryArray('resource.getItemByItemSrl', $dargs);
            if(!$output->data) return;

            foreach($output->data as $key => $val) {
                if($val->package_srl == $package_srl || $val->item_srl == $item_srl) continue;
                unset($args);
                $args->module_srl = $module_srl;
                $args->item_srl = $item_srl;
                $args->dependency_item_srl = $val->item_srl;
                $output = executeQuery('resource.insertDependency', $args);
            }
        }

        function procResourceAttachFile($proc = TRUE) {
            $oResourceModel = getModel('resource');
            $oFileController = getController('file');

            $args = Context::gets('package_srl','item_srl','attach_file','attach_screenshot', 'latest_item_srl');
            if(!$this->module_srl) return  new Object(-1,'msg_invalid_request');
            if(!$args->package_srl || !$args->item_srl) return  new Object(-1,'msg_invalid_request');

            if(!is_uploaded_file($args->attach_file['tmp_name']))  new Object(-1,'msg_invalid_request');
            if(!is_uploaded_file($args->attach_screenshot['tmp_name']))  new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');

            $package = $oResourceModel->getPackage($this->module_srl, $args->package_srl);
            if(!$package) return  new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $package->member_srl) return new Object(-1,'msg_not_permitted');

            $output = executeQuery('resource.getItemByItemSrl', $args);
            $item = $output->data;
            if(!$item) return  new Object(-1,'msg_invalid_request');

            $output = $oFileController->insertFile($args->attach_file, $this->module_srl, $args->item_srl);
            if(!$output || !$output->toBool()) 
			{
				if($proc)
				{
				$pargs->module_srl = $this->module_srl;
				$pargs->package_srl = $args->package_srl;
				$pargs->update_order = $args->latest_item_srl * -1;
				$pargs->latest_item_srl = $args->latest_item_srl;
				$poutput = executeQuery('resource.updatePackage', $pargs);

				$dargs->module_srl = $this->module_srl;
				$dargs->package_srl = $args->package_srl;
				$dargs->item_srl = $args->item_srl;
				$doutput = executeQuery('resource.deleteItems', $dargs);
				}

				return $output;
			}
            $args->file_srl = $output->get('file_srl');

            $output = $oFileController->insertFile($args->attach_screenshot, $this->module_srl, $args->item_srl);
            if(!$output || !$output->toBool()) return $output;
            $args->screenshot_url = $output->get('uploaded_filename');
            if($args->screenshot_url) FileHandler::createImageFile($args->screenshot_url, $args->screenshot_url, 100,100,'jpg');
            
            $args->module_srl = $this->module_srl;
            $output = executeQuery('resource.updateItemFile', $args);
            if(!$output->toBool()) return $output;

            $oFileController->setFilesValid($args->item_srl);

            $this->setMessage('success_registed');
            $site_module_info = Context::get('site_module_info');
            $this->setRedirectUrl(getNotEncodedSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','dispResourcePackage','package_srl',$args->package_srl));

            return $this;
        }

        public function procResourceModifyAttachOneTime()
        {
        	//return new Object(-1, 'test');
        	$oDB = DB::getInstance();
        	$oDB->begin();

        	$output = $this->procResourceModifyAttach();
        	if(!$output->toBool())
        	{
        		$oDB->rollback();
        		return $output;
        }

        	$output = $this->procResourceModifyAttachFile();
        	if(!$output->toBool())
        	{
        		$oDB->rollback();
        		return $output;
        	}

        	$oDB->commit();
        }

        function procResourceModifyAttach() {
            $oResourceModel = getModel('resource');
            $oFileController = getController('file');
            $oDocumentController = getController('document');
            $oDocumentModel = getModel('document');

            $package_srl = Context::get('package_srl');
            $item_srl = Context::get('item_srl');
            $document_srl = Context::get('document_srl');
            if(!$this->module_srl || !$package_srl || !$item_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');

            $package = $oResourceModel->getPackage($this->module_srl, $package_srl);
            if(!$package) return new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $package->member_srl) return new Object(-1,'msg_not_permitted');

            $item = $oResourceModel->getItem($this->module_srl, $package_srl, $item_srl);
            if(!$item) return new Object(-1,'msg_invalid_request');
            if($item->document_srl != $document_srl) return new Object(-1,'msg_invalid_request');

            $args = new stdClass();
            $args->module_srl = $this->module_srl;
            $args->package_srl = $package_srl;
            $args->item_srl = $item_srl;
            $args->version = trim(Context::get('version'));
            $args->description = trim(Context::get('description'));
            $output = executeQuery('resource.updateItem', $args);
            if(!$output->toBool()) return $output;

            $doc_args->document_srl = $item->document_srl;
            $doc_args->content = $args->description;
            $doc_args->tags = Context::get('tag');
            $doc_args->title = sprintf('%s ver. %s', $package->title, $args->version);
			$doc_args->commentStatus = 'ALLOW';
            $oDocumentController->updateDocument($oDocumentModel->getDocument($item->document_srl), $doc_args);

            $this->insertDependency($this->module_srl, $args->package_srl, $args->item_srl, trim(Context::get('dependency')));

            return $this;
        }

        function procResourceModifyAttachFile() {
            $oResourceModel = getModel('resource');
            $oFileController = getController('file');

            $args = Context::gets('package_srl','item_srl','attach_file','attach_screenshot');
            if(!$this->module_srl) return  new Object(-1,'msg_invalid_request');
            if(!$args->package_srl || !$args->item_srl) return  new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');

            $package = $oResourceModel->getPackage($this->module_srl, $args->package_srl);
            if(!$package) return  new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $package->member_srl) return new Object(-1,'msg_not_permitted');

            $item = $oResourceModel->getItem($this->module_srl, $args->package_srl, $args->item_srl);
            if(!$item) return  new Object(-1,'msg_invalid_request');

            if($args->attach_file['tmp_name']) {
                if(!is_uploaded_file($args->attach_file['tmp_name']))  new Object(-1,'msg_invalid_request');
                $oFileController->deleteFile($item->file_srl);
                $output = $oFileController->insertFile($args->attach_file, $this->module_srl, $args->item_srl);
                if(!$output || !$output->toBool()) return $output;
                $args->file_srl = $output->get('file_srl');
            }

            if($args->attach_screenshot['tmp_name']) {
                if(!is_uploaded_file($args->attach_screenshot['tmp_name']))  new Object(-1,'msg_invalid_request');
                $output = $oFileController->insertFile($args->attach_screenshot, $this->module_srl, $args->item_srl);
                if(!$output || !$output->toBool()) return $output;
                $args->screenshot_url = $output->get('uploaded_filename');
                if($args->screenshot_url) FileHandler::createImageFile($args->screenshot_url, $args->screenshot_url, 100,100,'jpg');
            }

            if($args->file_srl || $args->screenshot_url) {
                $output = executeQuery('resource.updateItemFile', $args);
                if(!$output->toBool()) return $output;
            }

            $site_module_info = Context::get('site_module_info');

            $oFileController->setFilesValid($args->item_srl);


            $this->setMessage('success_registed');
            $this->setRedirectUrl(getNotEncodedSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','dispResourcePackage','package_srl',$args->package_srl));

            return $this;
        }

        function procResourceDeleteAttach() {
            $oResourceModel = &getModel('resource');
            $oFileController = &getController('file');
            $oDocumentController = &getController('document');

            $package_srl = Context::get('package_srl');
            $item_srl = Context::get('item_srl');
            if(!$this->module_srl || !$package_srl || !$item_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');

            $item = $oResourceModel->getItem($this->module_srl, $package_srl, $item_srl);
            if(!$item) return new Object(-1,'msg_invalid_request');

            $package = $oResourceModel->getPackage($this->module_srl, $package_srl);
            if(!$package || (!$this->grant->manager && $package->member_srl != $logged_info->member_srl)) return new Object(-1,'msg_invalid_request');

            $args->module_srl = $this->module_srl;
            $args->package_srl = $package_srl;
            $args->item_srl = $item_srl;
            $output = executeQuery('resource.deleteItems', $args);
            if(!$output->toBool()) return $output;

            $output = $oFileController->deleteFiles($item_srl);
            if(!$output->toBool()) return $output;

            $output = executeQuery('resource.getMaxLatestItem', $args);
            if(!$output->toBool()) return $output;
            $latest_item_srl = (int)$output->data->item_srl;

            $largs->module_srl = $this->module_srl;
            $largs->package_srl = $package_srl;
            $largs->latest_item_srl = $latest_item_srl;
            $output = executeQuery('resource.updatePackageLatestItem', $largs);
            if(!$output->toBool()) return $output;

            $output = $oDocumentController->deleteDocument($item->document_srl);
            if(!$output->toBool()) return $output;
        }

        function triggerUpdateDownloadedCount($obj) {
            $oResourceModel = &getModel('resource');

            $args->item_srl = $obj->upload_target_srl;
            $output = executeQuery('resource.getItemByItemSrl', $args);
            if(!$output->data) return new Object();

            $item = $output->data;
            $args->package_srl = $item->package_srl;
            $args->module_srl = $item->module_srl;

            $output = executeQuery('resource.updateItemDownloadedCount', $args);
            $output = executeQuery('resource.updatePackageDownloadedCount', $args);

            return new Object();
        }

        function procResourceInsertComment() {
            $oCommentController = &getController('comment');
            $oResourceModel = &getModel('resource');

            if(!$this->grant->write_comment) return new Object(-1, 'msg_not_permitted');
            if(!$this->module_srl) return new Object(-1,'msg_invalid_request');

            $args = Context::gets('package_srl', 'item_srl','star_point','content');
            $args->module_srl = $this->module_srl;

            if(!$args->star_point || !$args->content || !$args->package_srl || !$args->item_srl) return new Object(-1,'msg_invalid_request');

            $item = $oResourceModel->getItem($args->module_srl, $args->package_srl, $args->item_srl);
            if(!$item->document_srl) return new Object(-1,'msg_invalid_request');

            $package = $oResourceModel->getPackage($args->module_srl, $args->package_srl);
            if(!$package->package_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');
            if($oResourceModel->hasVoted($this->module_srl, $package->package_srl, $args->item_srl, $logged_info->member_srl)) return new Object(-1,'msg_already_voted');

            $args->document_srl = $item->document_srl;
            $args->comment_srl = getNextSequence();
            $args->content = nl2br($args->content);
            $args->voted_count = $args->star_point;
            $output = $oCommentController->insertComment($args);
            if(!$output->toBool()) return $output;

            $star_args->module_srl = $this->module_srl;
            $star_args->package_srl = $args->package_srl;
            $star_args->voted = $package->voted+$args->star_point;
            $output = executeQuery('resource.plusPackageStar', $star_args);

            $star_args->module_srl = $this->module_srl;
            $star_args->package_srl = $args->package_srl;
            $star_args->item_srl = $args->item_srl;
            $star_args->voted = $item->voted+$args->star_point;
            $output = executeQuery('resource.plusItemStar', $star_args);

            $this->setMessage('success_registed');

            if(Context::get('success_return_url'))
            {
            	$this->setRedirectUrl(Context::get('success_return_url') . '#comment_' . $args->comment_srl);
        }
            else
            {
            	$this->setRedirectUrl(getNotEncodedSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','','package_srl',$item->package_srl) . '#comment_' . $args->comment_srl);
            }
        }

        function procResourceDeleteComment() {
            $oCommentModel = &getModel('comment');
            $oCommentController = &getController('comment');
            $oResourceModel = &getModel('resource');

            if(!$this->grant->write_comment) return new Object(-1, 'msg_not_permitted');
            if(!$this->module_srl) return new Object(-1,'msg_invalid_request');

            $args = Context::gets('package_srl', 'item_srl','comment_srl');
            $args->module_srl = $this->module_srl;

            $comment_srl = Context::get('comment_srl');
            $oComment = $oCommentModel->getComment($comment_srl);
            if(!$oComment->isExists() || !$oComment->isGranted()) return new Object(-1,'msg_invalid_request');

            $item = $oResourceModel->getItem($args->module_srl, $args->package_srl, $args->item_srl);
            if(!$item->document_srl) return new Object(-1,'msg_invalid_request');

            $package = $oResourceModel->getPackage($args->module_srl, $args->package_srl);
            if(!$package->package_srl) return new Object(-1,'msg_invalid_request');

            $output = $oCommentController->deleteComment($oComment->comment_srl);
            if(!$output->toBool()) return $output;

            $p_args->module_srl = $this->module_srl;
            $p_args->package_srl = $package->package_srl;
            $output = executeQuery('resource.getPackageSumStars', $p_args);

            $p_star_args->module_srl = $this->module_srl;
            $p_star_args->package_srl = $args->package_srl;
            $p_star_args->voted = (int)$output->data->voted;
            $p_star_args->voter = (int)$output->data->voter;
            $output = executeQuery('resource.minusPackageStar', $p_star_args);

            $p_args->module_srl = $this->module_srl;
            $p_args->package_srl = $package->package_srl;
            $p_args->item_srl = $item->item_srl;
            $output = executeQuery('resource.getItemSumStars', $p_args);

            $i_star_args->module_srl = $this->module_srl;
            $i_star_args->package_srl = $args->package_srl;
            $i_star_args->item_srl = $args->item_srl;
            $i_star_args->voted = (int)$output->data->voted;
            $i_star_args->voter = (int)$output->data->voter;
            $output = executeQuery('resource.minusItemStar', $i_star_args);

            $this->setRedirectUrl(getSiteUrl($site_module_info->domain, '', 'mid', Context::get('mid'),'act','','package_srl',Context::get('package_srl')));
        }

        function procResourceDeleteItem() {
            return $this->procResourceDeleteAttach();
        }

        function notify($email_address, $title, $message) {
            $oMail = new Mail();
            $oMail->setTitle($title);
            $oMail->setContent($message);
            $oMail->setSender('XE Resource Notifier',$email_address);
            $oMail->setReceiptor( null, $email_address);
            $oMail->send();
        }

    }
?>
