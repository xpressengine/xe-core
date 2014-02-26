<?php

/**
 * @brief setting for cron
 */

$searchDay = 7;	// 게시물 대상 날짜 ( 동작 날짜 부터  $searchDay 이전 날짜 까지 검색 ) 
$searchDocuments = array('tip','serverClass','phpClass','htmlCssClass','jsClass','designClass');	// document module_srl
$searchComments = array('qna'); 	// comment module_srl

$checkDocumentWordCount = 10;	// documents의content 글자 수 체크
$checkCommentWordCount = 10;	// comments의 content 글자 수 체크

$checkPoint = 100;	// 100 포인트 이상이면 그룹을 줌
$pointDocument = 100;	// documents 포인트 
$pointComment = 40;	// comments 포인트

$setGroupName = "프로모션회원";	// 조건 충족시 부여될 그룹 이름

$stdDate = date("Y-m-d");	// 게시물 검색 기준일

// setting site module info for XECore
$site_module_info = new stdClass;
$site_module_info->site_srl = 0;
Context::set('site_module_info', $site_module_info);

$logged_info = new stdClass;
$logged_info->is_admin = "Y";
Context::set('logged_info',$logged_info);

// escapte CSRF
$_SERVER['REQUEST_METHOD'] = "POST";
$_SERVER["HTTP_REFERER"] = Context::getDefaultUrl();


