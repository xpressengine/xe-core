/* 개별 쪽지 삭제 */
function doDeleteMessage(message_srl) {
	if(!message_srl) return;
	if(!confirm(confirm_delete_msg)) return;

	var params = new Array();
	params['message_srl'] = message_srl;
	exec_xml('communication', 'procCommunicationDeleteMessage', params, completeDeleteMessage);
}

function completeDeleteMessage(ret_obj) {
	alert(ret_obj['message']);
	location.href = current_url.setQuery('message_srl','');
}

function mergeContents(data) {
	var $form = jQuery('#fo_comm')
	var editotSequence = data.editor_sequence || null
	var content = ''
	var sourceContent = $form.find('input[name=source_content]').val() || ''

	if (editotSequence) {
		content = editorGetContent(editotSequence)
	} else {
		content = $form.find('[name=new_content]').val()
	}

	content += sourceContent

	$form.find('input[name=content]').val(content)
	$form.submit()
}
