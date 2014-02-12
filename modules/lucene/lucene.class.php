<?php

/**
 * @class   lucene
 * @author  NHN (developers@xpressengine.com)
 * @brief   lucene 모듈의 클래스
 **/
class lucene extends moduleObject {
	
	/**
	 * @brief 모듈 설치를 위해 추가적인 작업을 수행.
	 */
	function moduleInstall() {
		//$oModuleModel =& getModel('module');
		//$oModuleController = &getController('module');
		// trigger 등록
		//$oModuleController->deleteTrigger('document.insertDocument', 'lucene', 'controller', 'triggerInsertDocument', 'after'); 
		//$oModuleController->deleteTrigger('document.deleteDocument', 'lucene', 'controller', 'triggerDeleteDocument', 'after'); 
		//$oModuleController->deleteTrigger('comment.insertComment', 'lucene', 'controller', 'triggerInsertComment', 'after'); 
		//$oModuleController->deleteTrigger('comment.deleteComment', 'lucene', 'controller', 'triggerDeleteComment', 'after'); 
		//$oModuleController->insertTrigger('document.insertDocument', 'lucene', 'controller', 'triggerInsertDocument', 'after'); 
		//$oModuleController->insertTrigger('document.deleteDocument', 'lucene', 'controller', 'triggerDeleteDocument', 'after'); 
		//$oModuleController->insertTrigger('comment.insertComment', 'lucene', 'controller', 'triggerInsertComment', 'after'); 
		//$oModuleController->insertTrigger('comment.deleteComment', 'lucene', 'controller', 'triggerDeleteComment', 'after'); 
		// actionforward 등록
		//$oModuleController->deleteActionFoward('integration_search', 'view', 'IS');
		//$oModuleController->insertActionFoward('lucene', 'view', 'IS');
		return new Object();
	}

	/**
	 * @brief 모듈 업데이트 여부를 리턴.
	 */
	function checkUpdate() { 
		return false; 
	}

	/**
	 * @brief 모듈 업데이트를 위한 작업을 수행.
	 */
	function moduleUpdate() {
		return new Object(0, 'success_updated');
	}

	/**
	 * @brief 캐시 재생성을 위한 작업을 수행.
	 */
	function recompileCache() {
	}
}

?>
