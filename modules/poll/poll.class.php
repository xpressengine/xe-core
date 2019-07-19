<?php
/* Copyright (C) XEHub <https://www.xehub.io> */
/**
 * @class  poll
 * @author XEHub (developers@xpressengine.com)
 * @brief The parent class of the poll module
 */
class poll extends ModuleObject
{
	/**
	 * @brief Additional tasks required to accomplish during the installation
	 */
	function moduleInstall()
	{
		$oModuleController = getController('module');

		$config = new stdClass;
		$config->skin = 'default';
		$config->colorset = 'normal';
		$oModuleController->insertModuleConfig('poll', $config);
		// 2007.10.17 When deleting posts/comments delete the poll as well
		$oModuleController->insertTrigger('document.insertDocument', 'poll', 'controller', 'triggerInsertDocumentPoll', 'after');
		$oModuleController->insertTrigger('comment.insertComment', 'poll', 'controller', 'triggerInsertCommentPoll', 'after');
		$oModuleController->insertTrigger('document.updateDocument', 'poll', 'controller', 'triggerUpdateDocumentPoll', 'after');
		$oModuleController->insertTrigger('comment.updateComment', 'poll', 'controller', 'triggerUpdateCommentPoll', 'after');
		$oModuleController->insertTrigger('document.deleteDocument', 'poll', 'controller', 'triggerDeleteDocumentPoll', 'after');
		$oModuleController->insertTrigger('comment.deleteComment', 'poll', 'controller', 'triggerDeleteCommentPoll', 'after');

		return new BaseObject();
	}

	/**
	 * @brief A method to check if the installation has been successful
	 */
	function checkUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			// 2007.10.17 When deleting posts/comments delete the poll as well
			if(!$oModuleModel->getTrigger('document.insertDocument', 'poll', 'controller', 'triggerInsertDocumentPoll', 'after')) return true;
			if(!$oModuleModel->getTrigger('comment.insertComment', 'poll', 'controller', 'triggerInsertCommentPoll', 'after')) return true;
			if(!$oModuleModel->getTrigger('document.updateDocument', 'poll', 'controller', 'triggerUpdateDocumentPoll', 'after')) return true;
			if(!$oModuleModel->getTrigger('comment.updateComment', 'poll', 'controller', 'triggerUpdateCommentPoll', 'after')) return true;
			if(!$oModuleModel->getTrigger('document.deleteDocument', 'poll', 'controller', 'triggerDeleteDocumentPoll', 'after')) return true;
			if(!$oModuleModel->getTrigger('comment.deleteComment', 'poll', 'controller', 'triggerDeleteCommentPoll', 'after')) return true;

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return false;
	}

	/**
	 * @brief Execute update
	 */
	function moduleUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
		$version_update_id = implode('.', array(__CLASS__, __XE_VERSION__, 'updated'));
		if($oModuleModel->needUpdate($version_update_id))
		{
			// 2007.10.17 When deleting posts/comments delete the poll as well
			if(!$oModuleModel->getTrigger('document.deleteDocument', 'poll', 'controller', 'triggerDeleteDocumentPoll', 'after'))
				$oModuleController->insertTrigger('document.deleteDocument', 'poll', 'controller', 'triggerDeleteDocumentPoll', 'after');
			if(!$oModuleModel->getTrigger('comment.deleteComment', 'poll', 'controller', 'triggerDeleteCommentPoll', 'after'))
				$oModuleController->insertTrigger('comment.deleteComment', 'poll', 'controller', 'triggerDeleteCommentPoll', 'after');
			// 2008.04.22 A poll connection to add posts/comments
			if(!$oModuleModel->getTrigger('document.insertDocument', 'poll', 'controller', 'triggerInsertDocumentPoll', 'after'))
				$oModuleController->insertTrigger('document.insertDocument', 'poll', 'controller', 'triggerInsertDocumentPoll', 'after');
			if(!$oModuleModel->getTrigger('comment.insertComment', 'poll', 'controller', 'triggerInsertCommentPoll', 'after'))
				$oModuleController->insertTrigger('comment.insertComment', 'poll', 'controller', 'triggerInsertCommentPoll', 'after');
			if(!$oModuleModel->getTrigger('document.updateDocument', 'poll', 'controller', 'triggerUpdateDocumentPoll', 'after'))
				$oModuleController->insertTrigger('document.updateDocument', 'poll', 'controller', 'triggerUpdateDocumentPoll', 'after');
			if(!$oModuleModel->getTrigger('comment.updateComment', 'poll', 'controller', 'triggerUpdateCommentPoll', 'after'))
				$oModuleController->insertTrigger('comment.updateComment', 'poll', 'controller', 'triggerUpdateCommentPoll', 'after');

			$oModuleController->insertUpdatedLog($version_update_id);
		}

		return new BaseObject(0, 'success_updated');
	}

	/**
	 * @brief Re-generate the cache file
	 */
	function recompileCache()
	{
	}
}
/* End of file poll.class.php */
/* Location: ./modules/poll/poll.class.php */
