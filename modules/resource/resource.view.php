<?php
    /**
     * @class  resourceView
     * @author NHN (developers@xpressengine.com)
     * @brief  resource view class
     **/

    class resourceView extends resource {

        function init() {
            $oDocumentModel = &getModel('document');
            Context::set('categories', $oDocumentModel->getCategoryList($this->module_srl));

            $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            if(!is_dir($template_path)||!$this->module_info->skin) {
                $this->module_info->skin = 'xe_official';
                $template_path = sprintf("%sskins/%s/",$this->module_path, $this->module_info->skin);
            }
            $this->setTemplatePath($template_path);
            $this->setTemplateFile(strtolower(str_replace('dispResource','',$this->act)));
            Context::addJsFile($this->module_path.'tpl/js/resource.js');
        }

        function dispResourceIndex() {
            $oResourceModel = &getModel('resource');
            $oDocumentModel = &getModel('document');
            $oFileModel = &getModel('file');

            $document_srl = Context::get('document_srl');
            $package_srl = Context::get('package_srl');
            $logged_info = Context::get('logged_info');
            $type = Context::get('type');
            $item_srl = Context::get('item_srl');
            $page = Context::get('page');

            if($document_srl && !$package_srl) {
                $args->document_srl = $document_srl;
                $output = executeQuery('resource.getItemByDocumentSrl',$args);
                if($output->data) {
                    $package_srl = $output->data->package_srl;
                    Context::set('document_srl', '', true);
                }
            }

            if($package_srl) {
                $args->module_srl = $this->module_srl;
                $args->package_srl = $package_srl;
                $output = executeQuery('resource.getLatestItem', $args);
				$latest_package = $output->data;
                Context::set('latest_package', $latest_package);
            }

            if($latest_package) {
                $args->module_srl = $this->module_srl;
                $args->package_srl = $package_srl;
                $args->item_srl = $item_srl;
                $output = executeQuery('resource.getLatestItem', $args);
                if($output->data) {
                    $package = $oDocumentModel->getDocument($output->data->document_srl);
                    foreach($output->data as $key => $val) $package->add($key, $val);
                    if($package->get('package_voter')>0) $package->add('star', (int)($package->get('package_voted')/$package->get('package_voter')));
                    else $package->add('star',0);
                    if($package->get('item_voter')>0) $package->add('item_star', (int)($package->get('item_voted')/$package->get('item_voter')));
                    else $package->add('item_star',0);
                    $package->add('depencies', $oResourceModel->getDependency($this->module_srl, $package->get('item_srl')));

                    if($package->get('item_file_srl')) {
                        $file = $oFileModel->getFile($package->get('item_file_srl'));
                        if($file) $package->add('download_url', getFullUrl().$file->download_url);
                    }
                    $package->add('allow_comment','Y');
                    Context::set('package', $package);
                    Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

                    if($logged_info->member_srl) Context::set('voted', $voted = $oResourceModel->hasVoted($this->module_srl, $package->get('package_srl'), $package->get('item_srl'), $logged_info->member_srl));
                }

                if($type == 'all') {
                    Context::set('items', $oResourceModel->getItems($this->module_srl, $package_srl));
                }
            } else {
                $order_target = Context::get('order_target');
                if(!in_array($order_target, array('newest', 'download', 'popular'))) $order_target = 'newest';
                Context::set('order_target', $order_target);

                $order_type = Context::get('order_type');
                if(!in_array($order_type, array('asc', 'desc'))) $order_type = 'desc';
                Context::set('order_type', $order_type);

                $category_srl = Context::get('category_srl');
				if(!$category_srl) $category_srl = Context::get('category');
                $childs = Context::get('childs');

                $search_keyword = Context::get('search_keyword');

                $output = $oResourceModel->getLatestItemList($this->module_srl, $category_srl, $childs, null, $search_keyword, $order_target, $order_type, $page, $this->module_info->list_count);
                Context::set('item_list', $output->data);
                Context::set('total_count', $output->total_count);
                Context::set('total_page', $output->total_page);
                Context::set('page', $output->page);
                Context::set('page_navigation', $output->page_navigation);
            }

            Context::set('package_categories', $oResourceModel->getCategoryPacakgeCount($this->module_srl));
        }

        function dispResourceInsertPackage() {
            Context::set('licenses', $this->licenses);

			$from = Context::get('from');
			$backUrl = array(
				'' => getUrl('act', '', 'from', ''),
				'packageList' => getUrl('act', 'dispResourcePackageList', 'from', ''),
			);
			Context::set('back_url', $backUrl[$from]);

            // for backward compatibility
            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_package.xml');
        }

        function dispResourceModifyPackage() {
            $oResourceModel = &getModel('resource');

            $logged_info = Context::get('logged_info');
            $package_srl = Context::get('package_srl');
            if(!$package_srl) return new Object(-1,'msg_invalid_request');
            Context::set('selected_package', $selected_package = $oResourceModel->getPackage($this->module_srl, $package_srl));
            if(!$selected_package->package_srl) return new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            Context::set('licenses', $this->licenses);

            // for backward compatibility
            Context::addJsFilter($this->module_path.'tpl/filter', 'modify_package.xml');
        }

        function dispResourceDeletePackage() {
            $oResourceModel = &getModel('resource');

            $logged_info = Context::get('logged_info');
            $package_srl = Context::get('package_srl');
            if(!$package_srl) return new Object(-1,'msg_invalid_request');
            Context::set('selected_package', $selected_package = $oResourceModel->getPackage($this->module_srl, $package_srl));

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            // for backward compatibility
            Context::addJsFilter($this->module_path.'tpl/filter', 'delete_package.xml');
        }

        function dispResourcePackage() {
            $oResourceModel = &getModel('resource');
            $logged_info = Context::get('logged_info');
            $package_srl = Context::get('package_srl');
            if($package_srl) $selected_package = $oResourceModel->getPackage($this->module_srl, $package_srl);
            if(!$package_srl || !$selected_package) return new Object('msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            if($selected_package->voter>0) $selected_package->star = (int)($selected_package->voted/$selected_package->voter);
            else $selected_package->star = 0;
            Context::set('selected_package', $selected_package);

            Context::set('attach_items', $oResourceModel->getItems($this->module_srl, $selected_package->package_srl));
        }

        function dispResourcePackageList() {
            $oResourceModel = &getModel('resource');

            $logged_info = Context::get('logged_info');
            $output = $oResourceModel->getPackageList($this->module_srl, null, Context::get('category_srl'), $logged_info->member_srl, Context::get('page'));
            Context::set('package_list', $output->data);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);
        }

        function dispResourceAttach() {
            $oResourceModel = &getModel('resource');
            $oEditorModel = &getModel('editor');

            $logged_info = Context::get('logged_info');
            $package_srl = Context::get('package_srl');
            if(!$package_srl) return new Object(-1,'msg_invalid_request');
            Context::set('selected_package', $selected_package = $oResourceModel->getPackage($this->module_srl, $package_srl));

            if(!$selected_package) return new Object(-1,'msg_invalid_request');

            if(!$this->grant->manager && $logged_info->member_srl != $selected_package->member_srl) return new Object(-1,'msg_not_permitted');

            Context::set('latest_item', $oResourceModel->getLatestItem($package_srl));

            // Context::set('item_srl', $item_srl = getNextSequence());
            // Context::set('document_srl', $document_srl = getNextSequence());
            $inputError = Context::get('INPUT_ERROR');
            Context::set('editor', $oEditorModel->getModuleEditor('document', $this->module_srl, $inputError['document_srl'], 'document_srl', 'attach_description'));

            Context::addJsFilter($this->module_path.'tpl/filter', 'attach.xml');
        }

        function dispResourceModifyAttach() {
            $oResourceModel = &getModel('resource');
            $oFileController = &getController('file');
            $oEditorModel = &getModel('editor');
            $oDocumentModel = &getModel('document');

            $package_srl = Context::get('package_srl');
            $item_srl = Context::get('item_srl');
            if(!$this->module_srl || !$package_srl || !$item_srl) return new Object(-1,'msg_invalid_request');

            $logged_info = Context::get('logged_info');

            Context::set('item', $item = $oResourceModel->getItem($this->module_srl, $package_srl, $item_srl));
            if(!$item) return new Object(-1,'msg_invalid_request');

            Context::set('selected_package', $package = $oResourceModel->getPackage($this->module_srl, $package_srl));
            if(!$package || (!$this->grant->manager && $package->member_srl != $logged_info->member_srl )) return new Object(-1,'msg_invalid_request');

            Context::set('editor', $oEditorModel->getModuleEditor('document', $this->module_srl, $item->document_srl, 'document_srl', 'attach_description'));

            Context::set('dependency', $oResourceModel->getDependency($this->module_srl, $item_srl));

            Context::set('item_document', $oDocumentModel->getDocument($item->document_srl));

            // for backward compatibility
            Context::addJsFilter($this->module_path.'tpl/filter', 'modify_attach.xml');
        }

        function dispResourceManage() {
            $oResourceModel = &getModel('resource');

            if(!$this->grant->manager) return new Object(-1,'msg_not_permitted');

            $status = Context::get('status');
            if(!in_array($status, array('waiting','accepted','reservation'))) Context::set('status', $status = '');

            $category_srl = Context::get('category_srl');

            $output = $oResourceModel->getPackageList($this->module_srl, $status, $category_srl, NULL, Context::get('page'));
            Context::set('package_list', $output->data);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);

            Context::addJsFilter($this->module_path.'tpl/filter', 'change_status.xml');
        }

        function dispResourceSearchDependency() {
            $oResourceModel = &getModel('resource');

            $category_srl = Context::get('category_srl');
            $search_keyword = Context::get('search_keyword');
            $page = Context::get('page');

            $output = $oResourceModel->getLatestItemList($this->module_srl, $category_srl, null, null, $search_keyword, null, null, $page, $this->module_info->list_count);
            Context::set('item_list', $output->data);
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('page_navigation', $output->page_navigation);

            $this->setLayoutFile('popup_layout');
            $this->setTemplatePath($this->module_path.'tpl');
            $this->setTemplateFile('search_dependency');
        }
    }
?>
