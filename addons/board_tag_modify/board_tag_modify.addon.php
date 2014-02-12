<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

if(!defined('__XE__'))
	exit();


if($called_position == 'after_module_proc')
{
	if($this->act!="dispBoardContent")
		return;

	$document_srl = Context::get('document_srl');
	if($document_srl == false )
		return;

	$logged_info = Context::get('logged_info');

	$oDocumentModel = getModel('document');
	$oDocument = $oDocumentModel->getDocument($document_srl);
	
	if($oDocument->isExists()==false)
		return;

	$module_srl = $oDocument->get('module_srl');

	$oModuleModel = getModel('module');
	$grant = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($module_srl), $logged_info);

	if($grant->manager || in_array("arrange", $logged_info->group_list))
	{

		$tags = json_encode($oDocument->get('tag_list'));
		
		Context::addHtmlHeader( "
<style>
#tags_addon {padding:10px 5px;}
#tags_addon label {color:#999999}
</style>
<script>
(function($) {
$(window).load(function(){
	$.get(request_uri + '/addons/board_tag_modify/html/tags.html', function(data) {
		$('.read_footer').append(data);
		$('#tags_addon_document_srl').val('{$document_srl}');
		$('#tags_addon_module_srl').val('{$module_srl}');
		
		var tags = {$tags};
		if(tags != null) {
			$('#tags_addon_tags').val(tags.join(','));
		}
	});
});
})(jQuery);
</script>" );

	}
}

if($called_position == "before_module_init") 
{
	if($this->act=="dispBoardContent" && Context::get('addon_proc')=="update_tags")
	{
		$document_srl = Context::get('document_srl');

		$logged_info = Context::get('logged_info');
		$oDocumentModel = getModel('document');
		$oDocument = $oDocumentModel->getDocument($document_srl);

		if($oDocument->isExists()==false)
			return;

		$module_srl = $oDocument->get('module_srl');
		$oModuleModel = getModel('module');
		$grant = $oModuleModel->getGrant($oModuleModel->getModuleInfoByModuleSrl($module_srl), $logged_info);

		if($grant->manager || in_array("arrange", $logged_info->group_list))
		{
			$args = new stdClass();
			$args->document_srl = $document_srl;
			$args->tags = Context::get('tags');
			if($args->tags==false)
				$args->tags = "";
			$output = executeQuery("document.updateDocumentTags", $args);

			$oCacheHandler = CacheHandler::getInstance('object');
			if($oCacheHandler->isSupport())
			{
				//remove document item from cache
				$cache_key = 'document_item:'. getNumberingPath($document_srl) . $document_srl;
				$oCacheHandler->delete($cache_key);
			}

			$args->module_srl = $module_srl;

			$oTagController = getController('tag');
			$oTagController->triggerInsertTag($args);
			header( "location:" . getNotEncodedUrl('','mid', $this->mid, 'document_srl', $document_srl) );
		}
	}
}
