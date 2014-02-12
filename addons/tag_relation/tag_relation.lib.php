<?php
/**
 * @author BNU <bnufactory@gmail.com>
 * @brief 연관글 출력 애드온
 **/

if(!defined('__ZBXE__')) exit();

function getTagRelation($matches)
{
	$document_srl = $matches[1];
	if(!$document_srl || !is_numeric($document_srl)) return $matches[0];

	$addon_info = $GLOBALS['_tag_relation_addon_info_'];

	$document_list = getDocumentListWithinTag($document_srl);
	if(!$document_list) return $matches[0];

	Context::set('tag_relation', $addon_info);
	Context::set('tag_relation_document_list', $document_list);

	// 템플릿 컴파일
	$oTemplate = &TemplateHandler::getInstance();
	$addon_output = $oTemplate->compile($addon_info->skin_path, 'index.html');

	return $matches[0] . $addon_output;
}

function getDocumentListWithinTag($document_srl)
{
	$oModuleModel = &getModel('module');
	$documentModel = &getModel('document');
	$addon_info = $GLOBALS['_tag_relation_addon_info_'];

	$oCacheHandler = &CacheHandler::getInstance('object');
	if($oCacheHandler->isSupport())
	{
		$option_key = md5(implode(',', $addon_info));
		$object_key = 'object:related_documents:' . $document_srl . ':' . $option_key;
		$cache_key = $oCacheHandler->getGroupKey('xemagazine-addon-tag_relation', $object_key);
		$output = $oCacheHandler->get($cache_key);
		if($output) return $output;
	}

	// 원본글의 정보
	$oDocument = Context::get('oDocument');
	if(!$oDocument->document_srl && $oDocument->document_srl != $document_srl)
	{
		$oDocument = $documentModel->getDocument($document_srl, false, false);
	}
	$tags = $oDocument->get('tags');

	// 태그가 없으면 종료
	if(!$tags) return;
	$tag_list = explode(',', $tags);
	if(!count($tag_list)) return;

	$module_srls = array();

	// 대상 모듈의 글
	if($addon_info->target == 'selected_mid' && $addon_info->mid_list)
	{
		$module_srls = $oModuleModel->getModuleSrlByMid($addon_info->mid_list);

	
	}
	// 동일 사이트의 글
	else if($addon_info->target == 'current_site')
	{
		$site_module_info = Context::get('site_module_info');
		$args->site_srl = $site_module_info->site_srl;
		$output = executeQueryArray('addons.tag_relation.getSiteModules', $args);
		if(!$output->data) $output->data = array();

		foreach($output->data as $item)
		{
			$module_srls[] = $item->module_srl;
		}

	
	}
	// 동일 모듈(mid)의 글
	else if($addon_info->target == 'current_mid')
	{
		$module_srls = $oModuleModel->getModuleSrlByMid(Context::get('mid'));
		$module_srls = implode(',', $module_srls);
	}

	// 수집 제외 태그 제거
	if($addon_info->exclude_tags)
	{
		$exclude_tag_list = explode(',', $addon_info->exclude_tags);
		$tag_list = array_diff($tag_list, $exclude_tag_list);
	}

	$tags = "'".implode("','", $tag_list)."'";
	$args->tag_list = $tags;
	if(count($module_srls))
	{
		$args->module_srls = implode(',', $module_srls);
	}
	else
	{
		$args->module_srl = 1;
	}
	$output = executeQueryArray('addons.tag_relation.getDocumentListWithinTag', $args);
	if(!$output->data) return;


	// 태그 중첩 검사
	$document_srls = array();
	$module_srls = array();
	foreach($output->data as $item)
	{
		// 원본 글 제거
		if($item->document_srl == $document_srl) continue;

		// 최소 중첩 수 체크
		if($addon_info->lowest_tag && $item->count < $addon_info->lowest_tag) continue;

		$document_srls[$item->document_srl] = $item->count;
		$module_srls[$item->document_srl] = $item->module_srl;
	}

	// 대상 글이 없으면 종료
	if(!count($document_srls)) return;

	// 중복이 많은 순서로 정렬
	$document_srls = array_keys($document_srls);
	arsort($document_srls);

	// 지정한 갯수로 제한
	$list_count = $addon_info->list_count;
	if(count($document_srls) > $list_count)
	{
		$document_srls = array_slice($document_srls, 0, $list_count);
	}

	// 최종 정리된 문서 구함
	$columnList = array('document_srl', 'module_srl', 'title', 'nick_name', 'user_name', 'user_id', 'regdate', 'readed_count', 'voted_count', 'comment_count');
	$documents = $documentModel->getDocuments($document_srls, false, false, $columnList);

	// 모듈 제목 구함
	$module_title = array();
	if($addon_info->print_module_title == 'Y')
	{
		foreach($documents as &$oDocument)
		{
			if(!$module_title[$oDocument->get('module_srl')])
			{
				$args = new stdClass();
				$args->module_srl = $oDocument->get('module_srl');
				$output = executeQuery('module.getMidInfo', $args);
				if(!$output->data) continue;

				$module_title[$oDocument->get('module_srl')] = $output->data->browser_title;
			}
			$oDocument->add('module_title', $module_title[$oDocument->get('module_srl')]);
		}
	}

	if($oCacheHandler->isSupport())
	{
		$oCacheHandler->put($cache_key, $documents, 3600);
	}

	return $documents;
}

/* !End of file */
