<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  syndication
 * @author NAVER (developers@xpressengine.com)
 * @brief syndication module's high class
 * @todo site 전체의 문서를 연동하거나 게시판 메뉴 삭제 시 관련 게시판 내용 전체를 syndication과 연동하는 처리가 되어있지 않음.
 *			model 파일에서 처리 방식은 구현했으나 한번 시작되면 전체 문서를 종료할 때까지 계속 ping을 전송해야 하는 부담이 있음.
 **/

define('SyndicationModule', 'M');
define('SyndicationDocument', 'D');

define('SyndicationInserted', 'I');
define('SyndicationUpdated', 'U');
define('SyndicationDeleted', 'D');

class syndication extends ModuleObject {

	var $services = array(
		'Naver' => 'http://syndication.openapi.naver.com/ping/',
	);

	var $statuses = array(
		'Naver' => 'http://syndication.openapi.naver.com/status/?site=%s',
	);

	function moduleInstall() {
		$oModuleController = getController('module');
		$oModuleController->insertTrigger('document.insertDocument', 'syndication', 'controller', 'triggerInsertDocument', 'after');
		$oModuleController->insertTrigger('document.updateDocument', 'syndication', 'controller', 'triggerUpdateDocument', 'after');
		$oModuleController->insertTrigger('document.deleteDocument', 'syndication', 'controller', 'triggerDeleteDocument', 'after');
		$oModuleController->insertTrigger('module.deleteModule', 'syndication', 'controller', 'triggerDeleteModule', 'after');

		$oModuleController->insertTrigger('document.moveDocumentToTrash', 'syndication', 'controller', 'triggerMoveDocumentToTrash', 'after');
		$oModuleController->insertTrigger('document.restoreTrash', 'syndication', 'controller', 'triggerRestoreTrash', 'after');
		$oModuleController->insertTrigger('document.moveDocumentModule', 'syndication', 'controller', 'triggerMoveDocumentModule', 'after');

		$oAddonAdminModel = getAdminModel('addon');
		if($oAddonAdminModel->getAddonInfoXml('catpcha')){
			$oAddonAdminController = &addonAdminController::getInstance();
			$oAddonAdminController->doActivate('catpcha');
			$oAddonAdminController->makeCacheFile();
		}
	}

	function checkUpdate() {
		$oModuleModel = getModel('module');
		if(!$oModuleModel->getTrigger('document.moveDocumentToTrash', 'syndication', 'controller', 'triggerMoveDocumentToTrash', 'after')) return true;
		if(!$oModuleModel->getTrigger('document.restoreTrash', 'syndication', 'controller', 'triggerRestoreTrash', 'after')) return true;
		if(!$oModuleModel->getTrigger('document.moveDocumentModule', 'syndication', 'controller', 'triggerMoveDocumentModule', 'after')) return true;

		return false;
	}

	function moduleUpdate() {
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		if(!$oModuleModel->getTrigger('document.moveDocumentToTrash', 'syndication', 'controller', 'triggerMoveDocumentToTrash', 'after')){
			$oModuleController->insertTrigger('document.moveDocumentToTrash', 'syndication', 'controller', 'triggerMoveDocumentToTrash', 'after');
		}
		if(!$oModuleModel->getTrigger('document.restoreTrash', 'syndication', 'controller', 'triggerRestoreTrash', 'after')){
			$oModuleController->insertTrigger('document.restoreTrash', 'syndication', 'controller', 'triggerRestoreTrash', 'after');
		}
		if(!$oModuleModel->getTrigger('document.moveDocumentModule', 'syndication', 'controller', 'triggerMoveDocumentModule', 'after')){
			$oModuleController->insertTrigger('document.moveDocumentModule', 'syndication', 'controller', 'triggerMoveDocumentModule', 'after');
		}

		$oAddonAdminModel = getAdminModel('addon');
		if($oAddonAdminModel->getAddonInfoXml('catpcha')){
			$oAddonAdminController = &addonAdminController::getInstance();
			$oAddonAdminController->doActivate('catpcha');
			$oAddonAdminController->makeCacheFile();
		}
	}

	function recompileCache() {
	}

	function checkOpenSSLSupport()
	{
		if(!in_array('ssl', stream_get_transports())) {
			return FALSE;
		}
		return TRUE;
	}

	public function makeObject($code = 0, $message = 'success')
	{
		return class_exists('BaseObject') ? new BaseObject($code, $message) : new Object($code, $message);
	}
}
