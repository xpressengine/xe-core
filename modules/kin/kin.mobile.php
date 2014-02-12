<?php
	$oKinView = &getView('kin');

	class kinMobile extends kinView {

		function init()
		{
            $oDocumentModel = &getModel('document');

            $template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
            if(!is_dir($template_path)||!$this->module_info->mskin) {
                $this->module_info->mskin = 'default';
                $template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
            }

			$categories = $oDocumentModel->getCategoryList($this->module_srl);
			if($this->module_info->hide_category != 'Y' && count($categories))
			{
				$this->module_info->use_category = 'Y';
				Context::set('categories', $categories);
			}
			else
			{
				$this->module_info->use_category = 'N';
			}

            $this->setTemplatePath($template_path);
            Context::addJsFilter($this->module_path.'tpl/filter', 'input_password.xml');
		}

		function dispKinIndex()
		{
			$this->list_count = 10;

            if(Context::get('document_srl')) return $this->dispKinView();
			$output = parent::dispKinIndex();
			if(is_a($output, 'Object') && !$output->toBool())
			{
				return $output;
			}

			$this->setTemplateFile('index.html');
		}

		function dispKinWrite()
		{
			$output = parent::dispKinWrite();
			if(is_a($output, 'Object') && !$output->toBool())
			{
				return $output;
			}

			$this->setTemplateFile('write.html');
		}

        function dispKinView() {
            $oModuleModel = &getModel('module');
            $oDocumentModel = &getModel('document');
            $oKinModel = &getModel('kin');

            $oDocument = $oDocumentModel->getDocument(Context::get('document_srl'));
            if(!$oDocument->isExists()) return new Object(-1, 'msg_document_is_null');

            Context::addBrowserTitle($oDocument->getTitleText());
            $oDocument->updateReadedCount();

            $point = $oKinModel->getKinPoint($oDocument->document_srl);
            $oDocument->add('point', $point);
            Context::set('oDocument', $oDocument);

            Context::set('category_srl', $oDocument->get('category_srl'));

            $point_config = $oModuleModel->getModuleConfig('point');
            Context::set('point_name', $point_config->point_name?$point_config->point_name:'point');

            Context::addJsFilter($this->module_path.'tpl/filter', 'insert_comment.xml');

            $replies_count = $oKinModel->getKinCommentCount(array($oDocument->get('document_srl')));
            Context::set('replies_count', $replies_count);

			$oCommentModel = &getModel('comment');
            $oComment = $oCommentModel->getComment(0);
            $oComment->add('module_srl', $this->module_srl);
            $oComment->add('document_srl', $document_srl);
            $oComment->add('comment_srl', getNextSequence());
            Context::set('oReply', $oComment);

			$this->setTemplateFile('view.html');
        }

		function getKinCommentPage() {
			$oDocumentModel =& getModel('document');
			$oKinModel = &getModel('kin');

			$document_srl = Context::get('document_srl');
			if(!$document_srl) return new Object(-1, "msg_invalid_request");
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) return new Object(-1, "msg_invalid_request");

            Context::set('selected_reply', $oKinModel->getSelectedReply($oDocument->document_srl));
			Context::set('oDocument', $oDocument);

			$replies = $oDocument->getComments();
			if(count($replies)) {
				foreach($replies as $key => $val) $parent_srls[] = $val->comment_srl; 
			}
            $replies_count = $oKinModel->getKinCommentCount($parent_srls);
            Context::set('replies_count', $replies_count);
            Context::set('replies', $replies);

			$oTemplate = new TemplateHandler;
			$html = $oTemplate->compile($this->getTemplatePath(), "comment.html");
			$this->add("html", $html);
		}

		function dispKinCategory()
		{
			$oDocumentModel = &getModel('document');
			Context::set('category_list', $oDocumentModel->getCategoryList($this->module_srl));
			$this->setTemplateFile('category.html');
		}
	}
?>
