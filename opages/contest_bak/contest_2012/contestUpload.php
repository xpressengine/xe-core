<?php
    if(!defined("__ZBXE__")) exit();
	$obj = Context::getRequestVars();

	$logged_info = Context::get('logged_info');
	if(!$logged_info){
		$returnUrl = getNotEncodedUrl('', 'mid', 'contestUpload', 'act', 'dispMemberLoginForm');
		header('location:'.$returnUrl);
		return;
	}

	$args->member_srl = $logged_info->member_srl;
	$args->module_srl = $this->module_info->module_srl;
	$args->order_type = 'asc';
	$args->sort_index = 'regdate';
	$args->list_count = 1;

	$oDocumentModel = &getModel('document');
	$contestList = $oDocumentModel->getDocumentList($args);

	$contest = $contestList->data;

	foreach($contest as $key => $val){
		if($val->document_srl)
			$obj->document_srl = $val->document_srl;
	}

	if($obj->document_srl){
		$oDocumentModel = &getModel('document');
		$document_info = $oDocumentModel->getDocument($obj->document_srl);
		$doc_extra_info = unserialize($document_info->get('extra_vars'));
		Context::set('document_info', $document_info);
		Context::set('doc_extra_info', $doc_extra_info);
	}

	

	if($obj->action == 'contestUpload'){
		$error = 0;
		$oDocumentController = &getController('document');

		$args->module_srl = $this->module_info->module_srl; 
		$args->user_id = $logged_info->user_id;
		$args->user_name = $logged_info->user_name;
		$args->user_nickname = $logged_info->nick_name;
		$args->member_srl = $logged_info->member_srl;
		$args->email_address = $logged_info->email_address;

		$args->extra_vars->a_name = $obj->a_name;
		$args->extra_vars->a_contact_pre = $obj->a_contact_pre;
		$args->extra_vars->a_contact_post = $obj->a_contact_post;
		$args->extra_vars->a_occupation = $obj->a_occupation;
		$args->extra_vars->a_self_intro = $obj->a_self_intro;

		$args->extra_vars->product_intro = $obj->product_intro;
		$args->extra_vars->product_design = $obj->product_design;
		$args->extra_vars->product_desc = $obj->product_desc;

		$args->extra_vars->agree_term = $obj->agree_term;
		$args->extra_vars->up_thumbnail = $obj->up_thumbnail;
		$args->extra_vars->up_psd = $obj->up_psd;

		if($obj->document_srl){
			if(!$obj->up_thumbnail) $args->extra_vars->up_thumbnail = $doc_extra_info->up_thumbnail;
			if(!$obj->up_psd)  $args->extra_vars->up_psd = $doc_extra_info->up_psd;
		}

		if(!$obj->document_srl){
			$args->document_srl = getNextSequence();
		}else{
			$args->document_srl = $obj->document_srl;
		}

		//upload thumbnial
		$args->thumbnail_name = $obj->up_thumbnail['name'];
		$thumbnail_file =  $_FILES['up_thumbnail'];

		if($args->thumbnail_name){
			$file_pieces = explode(".", $args->thumbnail_name);
			$file_prefix = $file_pieces[0];
			$file_suffix = $file_pieces[1];
			
			// only jpg, png, gif, bmp file can be uploaded
			if($file_suffix != 'jpg' && $file_suffix != 'png' && $file_suffix != 'gif'){$error = 1;}
			$file_type = 'thumbnail';
			$args->extra_vars->jpg_file = uploadFile($this->module_info->module_srl,$args->document_srl, $args->thumbnail_name, $thumbnail_file['tmp_name'],$file_type);
		}

		//upload PSD
		$args->psd_name = $obj->up_psd['name'];
		$psd_file =  $_FILES['up_psd'];

		if($args->psd_name){
			$file_pieces = explode(".", $args->psd_name);
			$file_prefix = $file_pieces[0];
			$file_suffix = $file_pieces[1];
			
			// only psd file can be uploaded
			if($file_suffix != 'psd' || $file_suffix != 'zip') {$error = 2;}
			
			if($file_suffix == 'psd') $file_type = 'psd';
			elseif($file_suffix == 'zip') $file_type = 'zip';
			else $file_type = 'psd';

			$args->extra_vars->psd_file = uploadFile($this->module_info->module_srl,$args->document_srl, $args->psd_name, $psd_file['tmp_name'],$file_type);
		}

		if(!$obj->document_srl){
			$output = $oDocumentController->insertDocument($args);
		}else{
			$args->extra_vars->jpg_file = $args->extra_vars->jpg_file ? $args->extra_vars->jpg_file : $doc_extra_info->jpg_file;
			$args->extra_vars->psd_file = $args->extra_vars->psd_file ? $args->extra_vars->psd_file : $doc_extra_info->psd_file;
			$output = $oDocumentController->updateDocument($document_info,$args);
		}


		if(!in_array(Context::getRequestMethod(),array('XMLRPC','JSON'))) {
			$returnUrl = getNotEncodedUrl('', 'mid', 'contestUpload', 'document_srl', $args->document_srl);
			header('location:'.$returnUrl);
			return;
		}

	}


	/**
	 * @brief upload contest files to XE
	 **/
	function uploadFile($module_srl, $document_srl, $file_name, $target_file, $file_type) {
		$target_path = sprintf('files/contest_files/%s/%s/%s', $module_srl,$document_srl,$file_type);
		FileHandler::makeDir($target_path);
		
		$target_filename = sprintf('%s/%s', $target_path, $file_name);

		@copy($target_file, $target_filename);

		return $target_filename;
	}

?>

<!--%import("css/content.css")-->

<h2 class="m_title"><img src="img_content/img_h2_txt.gif" width="130" height="27" alt="공모작 등록" /> </h2>
<h3 class="m_title2"><img src="img_content/img_p_txt4.gif" width="52" height="19" alt="템플릿" /></h3>
<form action="{getUrl('','mid','contestUpload')}" method="post" enctype="multipart/form-data" id="contest_up_form">
<input type="hidden" name="action" value="contestUpload">
<input type="hidden" name="document_srl" value="{$document_info->document_srl}" id="document_srl">
<div class="step">	
	<ol class="program">
		<li>
			<h4>01. 자기 소개 </h4>
			<div class="item_cont">
				<p><label for="name" class="txt_lbl">이름<span class="required_input">*</span></label><input type="text" id="name" name="a_name" class="user_name" value="{$doc_extra_info->a_name}"|cond="$doc_extra_info&&$logged_info" value="{$logged_info->nick_name}"|cond="!$doc_extra_info&&$logged_info"/><label for="tel" class="tel_lbl">연락처<span class="required_input">*</span></label><input type="text" maxlength="3" id="a_contact_pre" class="tel" name="a_contact_pre" value="{$doc_extra_info->a_contact_pre}"|cond="$doc_extra_info&&$logged_info"/><span class="connect">-</span><input type="text" class="tel" id="a_contact_post" maxlength="4" name="a_contact_post" value="{$doc_extra_info->a_contact_post}"|cond="$doc_extra_info&&$logged_info" /><span class="connect">-</span><input type="text" class="tel" maxlength="4" id="a_contact_post2" name="a_contact_post" value="{$doc_extra_info->a_contact_post}"|cond="$doc_extra_info&&$logged_info" /></p>
				<p><label for="professional" class="txt_lbl">소속</label><input type="text" id="professional" name=" a_occupation" class="prof" value="{$doc_extra_info->a_occupation}"|cond="$doc_extra_info"></p>
				<p><label for="desc" class="textarea_lbl">하고 싶은 말</label><textarea cols="300" rows="15" id="desc" name="a_self_intro" class="desc">{$doc_extra_info->a_self_intro}</textarea></p>
			</div>
		</li>
		<li>
			<h4>02. 작품 소개</h4>
			<div class="item_cont">
				<p><label for="txtarea" class="textarea_lbl">컨셉<span class="required_input">*</span></label><textarea id="product_intro" name="product_intro" class="txtarea">{$doc_extra_info->product_intro}</textarea></p>
				<p><label for="txtarea2" class="textarea_lbl">디자인 구성</label><textarea id="product_design" name="product_design" class="txtarea">{$doc_extra_info->product_design}</textarea></p>
				<p><label for="txtarea3" class="textarea_lbl">하고 싶은 말</label><textarea id="product_desc" name="product_desc" class="txtarea">{$doc_extra_info->product_desc}</textarea></p>
			</div>
		</li>
		<li>
			<h4>03. 공모작 공개 동의서</h4>
			<div class="item_cont">
				<p>공모전에 접수된 모든 디자인 파일은 XE 커뮤니티>자료실에서 무료 공개되어 많은 사람들이 사용할 수 있게 됩니다. 이는 해당 파일을 누구나 편집 및 사용할 수 있다는 것을 의미합니다.</p><p>공모전 제출 작품은 <a href="http://www.gnu.org/licenses/old-licenses/lgpl-2.0.html" target="_blank" class="lnk" title="LGPL v2 라이센스"><strong>LGPL v2 </strong>라이선스</a>가 적용됩니다.</p> 
				<div class="check"><label for="agree_term">동의합니까?</label><input id="agree_term" name="agree_term" type="checkbox" class="input_checkbox" checked/></div> 
			</div>
		</li>
		<li>
			<h4>04. 디자인 파일을 업로드해주세요.</h4>
			<div class="item_cont">
				<p class="file_box"><label for="up_thumbnail" class="file_lbl">메인페이지 대표컷 이미지 업로드(가로 1024 이내, png형식으로 제출)<span class="required_input">*</span></label><input id="up_thumbnail" name="up_thumbnail" type="file"/><a href="{$doc_extra_info->jpg_file}" class="jpg_link">{$doc_extra_info->up_thumbnail['name']}</a></p> 				
				
				<p class="file_box"><label for="up_psd" class="file_lbl">응모 작품 업로드 (psd, zip)<span class="required_input">*</span></label><input id="up_psd" name="up_psd" type="file"/><a href="{$doc_extra_info->psd_file}" class="psd_link">{$doc_extra_info->up_psd['name']}</a></p> 
			</div>
		</li>
	</ol>
</div>
<div class="btn_box">
	<input type="submit" class="input_submt" value="Submit"|cond="!$doc_extra_info" value="Update"|cond="$doc_extra_info"/>
</div>
</form>


<script type="text/javascript">
 jQuery(function($){

	$('#contest_up_form').submit(function(){
		var error = 0;

		var a_name = $('#name').attr('value');
		if(!a_name){
			error = 1;
			alert('Please insert your name');
			return false;
		}

		var a_contact_pre = $('#a_contact_pre').attr('value');
		var a_contact_post = $('#a_contact_post').attr('value');
		var a_contact_post2 = $('#a_contact_post2').attr('value');
		if(!a_contact_pre || !a_contact_post || !a_contact_post2 || isNaN(a_contact_pre) || isNaN(a_contact_post) || isNaN(a_contact_post2)){
			error = 1;
			alert('Please insert your valid phone number');
			return false;
		}

		var pro_intro = $('#product_intro').attr('value');
		if(!pro_intro){
			error = 1;
			alert('Please insert the introduction of the item');
			return false;
		}


		var agree_check = $('#agree_term').attr('checked');

		if(!agree_check){
			error = 1;
			alert('Sorry, You haven\'t read and agree to the terms of the license agreement.');
			return false;
		}

		var document_srl = $('#document_srl').attr('value');

		if(!document_srl){
			var thumbnail_name = $('#up_thumbnail').attr('value');
			var thumbnail_suffix = thumbnail_name.substring(thumbnail_name.lastIndexOf(".")+1);
			if(!thumbnail_name || (thumbnail_suffix != 'jpg' && thumbnail_suffix != 'png' && thumbnail_suffix != 'gif')){
				error = 1;
				alert('Please upload a valid thumbnail, it only supports jpg, png, gif and bmp image.');
				return false;
			}

			var psd_name = $('#up_psd').attr('value');
			var psd_suffix = psd_name.substring(psd_name.lastIndexOf(".")+1);
			if(!psd_name || (psd_suffix != 'psd' && psd_suffix != 'zip')){
				error = 1;
				alert('Please upload a valid psd or zip file.');
				return false;
			}
		}else{
			var thumbnail_name = $('#up_thumbnail').attr('value');
			var thumbnail_suffix = thumbnail_name.substring(thumbnail_name.lastIndexOf(".")+1);
			if(thumbnail_name && (thumbnail_suffix != 'jpg' && thumbnail_suffix != 'png' && thumbnail_suffix != 'gif')){
				error = 1;
				alert('Please upload a valid thumbnail, it only supports jpg, png, gif and bmp image.');
				return false;
			}

			var psd_name = $('#up_psd').attr('value');
			var psd_suffix = psd_name.substring(psd_name.lastIndexOf(".")+1);
			if(psd_name && (psd_suffix != 'psd'  && psd_suffix != 'zip')){
				error = 1;
				alert('Please upload a valid psd or zip file.');
				return false;
			}
		}

		if(error != 0){
			return false;
		}else{
			return true;
		}
	});

 });

</script>

