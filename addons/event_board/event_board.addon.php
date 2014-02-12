<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file event_board.addon.php
     * @author NHN (developers@xpressengine.com)
     * @brief 기간내에만 게시물 쓰기/수정/삭제될 수 있도록 하는 애드온
     **/

	if ($logged_info->is_admin == 'Y') return;

    if($called_position == 'after_module_proc' && Context::getResponseMethod()!="XMLRPC") {
		$current_date = strtotime('now');
		$start_date = strtotime($addon_info->start_date);
		$end_date = strtotime($addon_info->end_date);

		$on_actlist = array();
		$act_title = array();

		if ($current_date < $start_date || $current_date > $end_date)
		{
			$error_message = '%s ~ %s 기간 외에는 글을 %s 할 수 없습니다.';
			$on_actlist = array('dispBoardWrite', 'dispBoardWriteBydocument_srl', 'dispBoardDeleteBydocument_srl', 'procBoardInsertDocument', 'procBoardInsertDocumentBydocument_srl', 'procBoardDeleteDocumentBydocument_srl');	
			$act_title = array('등록', '수정', '삭제');	
		}
		else
		{
			$error_message = '%s ~ %s 기간 동안 에는 글을 %s 할 수 없습니다.';
			if ($addon_info->write_mode == 'off')
			{	
				$on_actlist[] = 'dispBoardWrite';
				$on_actlist[] = 'procBoardInsertDocument';
				$act_title[] = '등록';
			}
			if ($addon_info->modify_mode == 'off')
			{	
				$on_actlist[] = 'dispBoardWriteBydocument_srl';
				$on_actlist[] = 'procBoardInsertDocumentBydocument_srl';
				$act_title[] = '수정';
			}
			if ($addon_info->delete_mode == 'off')
			{
				$on_actlist[] = 'dispBoardDeleteBydocument_srl';
				$on_actlist[] = 'procBoardDeleteDocumentBydocument_srl';
				$act_title[] = '삭제';
			}
		}

		$act = Context::get('act');
		$act = (Context::get('document_srl'))?$act.'Bydocument_srl':$act;

		if (in_array($act, $on_actlist))
		{
			$this->stop(sprintf($error_message, date('Y-m-d H:i:s', $start_date), date('Y-m-d H:i:s', $end_date), join('/', $act_title)));
		}
    }
?>
