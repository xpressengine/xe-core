<?php
	$oResourceView = &getView('resource');

	class resourceMobile extends resourceView {

		function init()
		{
            $oDocumentModel = &getModel('document');
             Context::set('categories', $oDocumentModel->getCategoryList($this->module_srl));

            $template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
            if(!is_dir($template_path)||!$this->module_info->mskin) {
                $this->module_info->mskin = 'default';
                $template_path = sprintf("%sm.skins/%s/",$this->module_path, $this->module_info->mskin);
            }
            $this->setTemplatePath($template_path);

            Context::addJsFile($this->module_path.'tpl/js/resource.js');
		}

		function dispResourceIndex()
		{
			parent::dispResourceIndex();
			$this->setTemplateFile('index.html');
		}


		function dispResourceCategory()
		{
			$oDocumentModel = &getModel('document');
			Context::set('category_list', $oDocumentModel->getCategoryList($this->module_srl));
			$this->setTemplateFile('category.html');
		}
	}
?>
