<?php
    if(!defined("__ZBXE__")) exit();
	$logged_info = Context::get('logged_info');
	if($logged_info){
		$oModuleModel = &getModel('module');
		$contestUploadInfo = $oModuleModel->getModuleInfoByMid('contestUpload');

		$args->member_srl = $logged_info->member_srl;
		$args->module_srl = $contestUploadInfo->module_srl;
		$args->order_type = 'asc';
		$args->sort_index = 'regdate';
		$args->list_count = 1;

		$oDocumentModel = &getModel('document');
		$contestList = $oDocumentModel->getDocumentList($args);

		$contest = $contestList->data;

		foreach($contest as $key => $val){
			if($val->document_srl)
				Context::set('document_srl',$val->document_srl);
		}

	}

	
?>

<!-- #contest_registration -->
<?PHP
if(date('Ymd')>'20121212'){
?>
<div id="contest_registration">
<h2><img src="http://contest.dev.xpressengine.cn/layouts/xe_contest_2012/img_content/h1Registration.gif" width="127" height="50" alt="응모작 제출" /></h1>
<p style="margin-bottom:3em">제출이 마감되었습니다.</p>
</div>

<?PHP
}else{
?>


<div id="contest_registration">
<h1><img src="img_content/bg_h2_text.gif" width="129" height="54" alt="공모작 등록"></h1>
<p style="margin-top:22px"><img src="img_content/img_p_txt.gif" width="345" height="35" alt="응모작 제출 기간은 2012년 8월 31일 (일) 까지 입니다.(XE 공식사이트 계정을 이용해 로그인하실 수 있습니다.)" ></p>
<!-- no upload -->
<block cond="!$document_srl">
<p style="margin-top:30px"><a href="{getUrl('','mid','contestUpload')}"><img  src="img_content/btn_reg.gif" width="234" height="51" alt="응모작 접수" /></a></p>
<p><img src="img_content/img_p_txt2.gif" width="526" height="12" alt="이미 제출한 응모작의 열람과 수정이 가능합니다. 접수 기간 중에는 타인의 응모작은 볼 수 없습니다." /></p>
</block>
<!-- // no upload -->

<!-- modify -->
<block cond="$document_srl">
<p style="margin-top:30px"><a href="{getUrl('','mid','contestUpload','document_srl', $document_srl)}"><img src="img_content/btn_modify.gif" width="234" height="51" alt="내 응모작 확인/수정" /></a></p>
<p><img src="img_content/img_p_txt3.gif" width="471" height="12" alt="이미 제출한 응모작의 열람과 수정이 가능합니다. 접수 기간 중에는 타인의 응모작은 볼 수 없습니다." /></p>
</block>
<!-- modify -->
</div>
<?PHP
}
?>
<!-- /#contest_registration -->
