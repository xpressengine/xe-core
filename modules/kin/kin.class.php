<?php
    /**
     * @class  kin
     * @author zero (skklove@gmail.com)
     * @brief  kin high class
     **/

    class kin extends ModuleObject {

        var $search_option = array('title','content','title_content','comment','user_name','nick_name','user_id','tag'); 
		var $list_count = 20;
		var $page_count = 10; 

        function moduleInstall() {
			$oModuleController = &getController('module');
			$oModuleController->insertTrigger('menu.getModuleListInSitemap', 'kin', 'model', 'triggerModuleListInSitemap', 'after');
            return new Object();
        }

        function checkUpdate() {
			$oModuleModel = &getModel('module');
			if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'kin', 'model', 'triggerModuleListInSitemap', 'after')) return true;
            return false;
        }

        function moduleUpdate() {
			$oModuleModel = &getModel('module');
			$oModuleController = &getController('module');
			if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'kin', 'model', 'triggerModuleListInSitemap', 'after'))
				$oModuleController->insertTrigger('menu.getModuleListInSitemap', 'kin', 'model', 'triggerModuleListInSitemap', 'after');
            return new Object(0, 'success_updated');
        }

        function recompileCache() {
        }
    }
?>
