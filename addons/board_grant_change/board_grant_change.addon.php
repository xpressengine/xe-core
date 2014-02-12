<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @file
 * @brief Addon for extends board grant 
 * @author [NAVER](http://www.navercorp.com) (<developers@xpressengine.com>)
*/
if(!defined('__XE__'))
	exit();

if($called_position=="before_module_proc" && $this->module=="board")
{
	// change board grant about comment
	$arrCheckAct = array(
		'dispBoardContent',
		'dispBoardDeleteComment',
		'dispBoardModifyComment',
		'procBoardInsertComment',
		'procBoardDeleteComment',
	);
	if($this->grant->write_comment==false && in_array($this->act, $arrCheckAct)) 
	{
		$document_srl = Context::get('document_srl');
		if(!$document_srl)
			return;

		$logged_info = Context::get('logged_info');

		if($logged_info==false)
			return;
		
		$oDocumentModel = &getModel('document');
		$oDocument = $oDocumentModel->getDocument($document_srl);
		
		if(!$oDocument->isExists())
			return;

		// if document's owner have not grant for comment, give grant about comment to owner
		if($oDocument->get('member_srl')==$logged_info->member_srl)
			$this->grant->write_comment = true;
	}

}
