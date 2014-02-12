function debugPrint(msg) {
	if(typeof console == 'object' && typeof console.log == 'function')
	{
		console.log(msg);
	}
}

function getCode()
{
	if(typeof(opener) == "undefined") return;

	var node = opener.editorPrevNode;
	var form$ = jQuery('#fo');

	if(!node || node.nodeName != 'DIV')
	{
		var code = opener.editorGetSelectedHtml(opener.editorPrevSrl);
		code = jQuery(code).text();
		code = getArrangedCode(code, 'textarea');
		form$.find('textarea[name=code]').val(code);
		debugPrint('asdf');
		return;
	}

	var opt = getArrangedOption(jQuery(node));
	opt.code = getArrangedCode(opt.code, 'textarea');

	form$.find('select[name=code_type]').val(opt.code_type);
	form$.find('input[name=title]').val(opt.title);
	form$.find('input[name=first_line]').val(opt.first_line);
	form$.find('input[name=highlight]').val(opt.highlight);
	form$.find('input[name=collapse]').attr('checked', opt.collapse);
	form$.find('input[name=nogutter]').attr('checked', opt.nogutter);
	form$.find('textarea[name=code]').val(opt.code);
}

function insertCode()
{
	if(typeof(opener) == "undefined") return;

	var form$ = jQuery('#fo');
	var opt = getArrangedOption(form$);
	opt.code = getArrangedCode(opt.code, 'wyswig');

	var style = "border:#666 1px dotted;border-left:#2ae 5px solid;padding:5px;background:#FAFAFA url('./modules/editor/components/code_highlighter/code.png') no-repeat top right;";
	var text = '<div editor_component="code_highlighter" code_type="'+opt.code_type+'" title="'+opt.title+'" first_line="'+opt.first_line+'" collapse="'+opt.collapse+'" highlight="'+opt.highlight+'" nogutter="'+opt.nogutter+'" style="'+style+'">'+opt.code+'</div>';

	var iframe_obj = opener.editorGetIFrame(opener.editorPrevSrl);
	try {
		var prevNode = opener.editorPrevNode;
		prevNode.parentNode.insertBefore(jQuery(text+'<br />').get(0), prevNode);
		prevNode.parentNode.removeChild(prevNode);
	} catch(e) {
		try {
			opener.editorReplaceHTML(iframe_obj, text);
		} catch(ee) { }
	};
	opener.editorFocus(opener.editorPrevSrl);

	window.close();
}

function getArrangedOption(elem$)
{
	if(!elem$.size()) return;

	var node = elem$[0];
	var opt = {};

	if(node.nodeName == 'FORM')
	{
		opt.code_type = elem$.find('select[name=code_type]').val();
		opt.title = elem$.find('input[name=title]').val();
		opt.first_line = elem$.find('input[name=first_line]').val();
		opt.collapse = (elem$.find('input[name=collapse]:checked').length) ? 'true' : 'false';
		opt.nogutter = (elem$.find('input[name=nogutter]:checked').length) ? 'true' : 'false';
		opt.gutter = (opt.nogutter == 'true') ? 'false' : 'true';
		opt.highlight = elem$.find('input[name=highlight]').val();
		opt.code = elem$.find('textarea[name=code]').val();
	}
	else
	{
		opt.code_type = node.getAttribute('code_type');
		opt.title = node.getAttribute('title');
		opt.first_line = node.getAttribute('first_line');
		opt.collapse = node.getAttribute('collapse');
		opt.nogutter = node.getAttribute('nogutter');
		opt.highlight = node.getAttribute('highlight');
		opt.code = elem$.html();
		opt.collapse = (opt.collapse == 'Y' || opt.collapse == 'true' || opt.collapse == 'checked') ? true : false;
		opt.nogutter = (opt.nogutter == 'Y' || opt.nogutter == 'true' || opt.nogutter == 'checked') ? true : false;
		opt.gutter = (opt.nogutter == false) ? true : false;
	}

	if(!opt.first_line) opt.first_line = '1';

	return opt;
}

function getArrangedCode(code, outputType)
{
	if(!outputType) outputType = 'textarea';

debugPrint('or');
debugPrint(code);
	if(outputType == 'wyswig')
	{
		code = code.replace(/</g, "&lt;");
		code = code.replace(/>/g, "&gt;");
		code = code.replace(/ /g, "&nbsp;");
		code = code.replace(/\n/g, "<br />\n");
	}

	if(outputType == 'textarea')
	{
		code = code.replace(/&nbsp;/g, ' ');
		code = code.replace(/<br\s*\/?>\n?/gi, "\n");
		code = code.replace(/&lt;/g, '<');
		code = code.replace(/&gt;/g, '>');
	}
	else if(outputType == 'preview')
	{
		code = code.replace(/</g, '&lt;');
		code = code.replace(/>/g, '&gt;');
	}

debugPrint('Type : '+outputType);
debugPrint(code);

	code = jQuery.trim(code);
	if(!code) code = '여기에 코드를 입력해주세요';

	return code;
}

