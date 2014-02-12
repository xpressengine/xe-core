<?php
/**
 * @brief Code Highlighter
 **/
class code_highlighter extends EditorHandler
{
	var $editor_sequence = 0;
	var $component_path = '';
	var $brushes = array(
		array('Plain Text', 'Plain', 'text plain'),
		array('AppleScript', 'AppleScript', 'applescript'),
		array('ActionScript3', 'AS3', 'actionscript3 as3'),
		array('Bash(Shell)', 'Bash', 'bash shell'),
		array('ColdFusion', 'ColdFusion', 'coldfusion cf'),
		array('c/C++', 'Cpp', 'cpp c'),
		array('C#', 'CSharp', 'c# c-sharp csharp'),
		array('CSS', 'Css', 'css'),
		array('Delphi', 'Delphi', 'delphi pascal'),
		array('Diff', 'Diff', 'diff patch pas'),
		array('Erlang', 'Erlang', 'erl erlang'),
		array('Groovy', 'Groovy', 'groovy'),
		array('Java', 'Java', 'java'),
		array('JavaFX', 'JavaFX', 'jfx javafx'),
		array('JavasSript', 'JScript', 'js jscript javascript'),
		array('Perl', 'Perl', 'perl pl'),
		array('PHP', 'Php', 'php'),
		array('PowerShell', 'PowerShell', 'powershell ps'),
		array('Python', 'Python', 'py python'),
		array('Ruby', 'Ruby', 'ruby rails ror rb'),
		array('Sass', 'Sass', 'sass scss'),
		array('Scala', 'Scala', 'scala'),
		array('SQL', 'Sql', 'sql'),
		array('VB/VB.net', 'Vb', 'vb vbnet'),
		array('XML/HTML', 'Xml', 'xml xhtml xslt html')
	);

	function code_highlighter($editor_sequence, $component_path)
	{
		$this->editor_sequence = $editor_sequence;
		$this->component_path = $component_path;
	}

	function getPopupContent()
	{
		// 템플릿을 미리 컴파일해서 컴파일된 소스를 return
		$tpl_path = $this->component_path.'tpl';
		$tpl_file = 'popup.html';
		$script_path = $this->component_path.'syntaxhighlighter/scripts/';

		$brushes = array();
		foreach($this->brushes as $item) $brush_autoload[] = '"'.$item[2].' '.$script_path.'shBrush'.$item[1].'.js"';
		
		if(!$this->theme) $this->theme = 'Default';
		$theme_file = $this->component_path.'syntaxhighlighter/styles/shCore'.$this->theme.'.css';
		if(!file_exists($theme_file)) $theme_file = $this->component_path.'syntaxhighlighter/styles/shCoreDjango.css';

		Context::set('tpl_path', $tpl_path);
		Context::set('script_path', $script_path);
		Context::set('brushes', $this->brushes);
		Context::set('brush_autoload', implode(',', $brush_autoload));
		Context::addCSSFile($theme_file);
		Context::addJsFile($script_path.'shCore.js');
		Context::addJsFile($script_path.'shAutoloader.js');

		$oTemplate = &TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, $tpl_file);
	}

	/**
	 * @brief 에디터 컴포넌트가 별도의 고유 코드를 이용한다면 그 코드를 html로 변경하여 주는 method
	 **/
	function transHTML($xml_obj)
	{
		$script_path = getScriptPath().'modules/editor/components/code_highlighter/syntaxhighlighter/scripts/';
		$code_type = ucfirst($xml_obj->attrs->code_type);
		$option_title = ' title="'.$xml_obj->attrs->title.'"';
		$option_first_line = $xml_obj->attrs->first_line;
		$option_collapse = $xml_obj->attrs->collapse;
		$option_nogutter = $xml_obj->attrs->nogutter;
		$option_highlight = $xml_obj->attrs->highlight;
		$option[] = 'brush:'.strtolower($code_type);
		if(in_array($option_collapse, array('true', 'checked', 'Y'))) $option[] = 'collapse:true';
		if(in_array($option_nogutter, array('true', 'checked', 'Y'))) $option[] = 'gutter:false';
		if($option_highlight) $option[] = 'highlight:['.$option_highlight.']';
		if($option_first_line > 1) $option[] = 'first-line:'.$option_first_line;
		$body = $xml_obj->body;

		$body = strip_tags($body, '<br>');
		$body = preg_replace("/(<br\s*\/?>)(\n|\r)*/i", "\n", $body);
		$body = strip_tags($body);
		$body = str_replace('&nbsp;', ' ', $body);

		if(!$GLOBALS['_called_editor_component_code_highlighter_'])
		{
			$GLOBALS['_called_editor_component_code_highlighter_'] = true;
			foreach($this->brushes as $item) $brush[] = '"'.$item[2].' '.$script_path.'shBrush'.$item[1].'.js"';
			
			$sh_js_code[] = '<script type="text/javascript">';
			$sh_js_code[] = 'window.SyntaxHighlighter.autoloader(';
			$sh_js_code[] = implode(','.PHP_EOL, $brush);
			$sh_js_code[] = ');';
			$sh_js_code[] = 'window.SyntaxHighlighter.config.bloggerMode = true;';
			$sh_js_code[] = 'window.SyntaxHighlighter.all();';
			$sh_js_code[] = '</script>';

			if(!$this->theme) $this->theme = 'Default';
			$theme_file = $this->component_path.'syntaxhighlighter/styles/shCore'.$this->theme.'.css';
			if(!file_exists($theme_file)) $theme_file = $this->component_path.'syntaxhighlighter/styles/shCoreDjango.css';

			Context::set('script_path', $script_path);
			Context::addHtmlFooter(implode(PHP_EOL, $sh_js_code));
			Context::addCSSFile($theme_file);
			Context::addJsFile($script_path.'shCore.js');
			Context::addJsFile($script_path.'shAutoloader.js');
		}

		$output = sprintf('<pre class="%s" %s>%s</pre>',
			implode(';', $option),
			$option_title,
			$body
		);

		return $output;
	}
}
