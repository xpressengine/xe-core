<?php
/** Exercise the actual template compiler and rendering context on legacy PHP. */
define('__XE__', true);
require dirname(dirname(dirname(__FILE__))) . '/config/config.inc.php';
$context = Context::getInstance();
$context->context = new stdClass();
$GLOBALS['__Context__'] = $context->context;
$context->get_vars = new stdClass();
$context->lang_type = 'en';
$context->request_method = 'GET';
$context->response_method = 'HTML';
$context->oFrontEndFileHandler = new FrontEndFileHandler();
$GLOBALS['lang'] = new stdClass();
$_SESSION = array();
$directory = sys_get_temp_dir() . '/xe-security-templates-' . getmypid() . '/';
mkdir($directory);
$generated = array();
register_shutdown_function(function() use ($directory, &$generated) {
    foreach ($generated as $file) if (is_file($file)) unlink($file);
    foreach (glob($directory . '*') as $file) unlink($file);
    rmdir($directory);
});
function renderSecurityTemplate($source)
{
    global $directory, $generated;
    $file = $directory . 'template.html';
    file_put_contents($file, '<config autoescape="on" />' . $source);
    $template = new TemplateHandler();
    $html = $template->compile($directory, 'template.html');
    $property = new ReflectionProperty('TemplateHandler', 'compiled_file');
    $property->setAccessible(true);
    $generated[] = $property->getValue($template);
    return $html;
}
function checkTemplate($condition, $message)
{
    if (!$condition) { fwrite(STDERR, 'FAIL: ' . $message . "\n"); exit(1); }
}
$title = "한글 \"title\" & 'quote' </script><script>alert(1)</script>";
$components = array('image_link' => (object)array('title' => $title));
Context::set('enable_component', true, false);
Context::set('component_list', $components, false);
$source = file_get_contents(_XE_PATH_ . 'modules/editor/skins/ckeditor/editor.html');
preg_match('/<!--@if\(\$enable_component\)-->(.*?)<!--@endif-->/s', $source, $match);
$html = renderSecurityTemplate('<script>' . $match[1] . '</script>');
preg_match('/xe_component_arrays\s*=\s*(.*);/', $html, $json);
checkTemplate(isset($json[1]) && json_decode($json[1], true) === array('image_link' => escape($title, false)), 'Editor component settings are valid JSON with autoescape');
checkTemplate(substr_count($html, '</script>') === 1, 'Editor title cannot terminate script');
foreach (array('default', 'xedition') as $skin) {
    $source = file_get_contents(_XE_PATH_ . 'modules/board/skins/' . $skin . '/_read.html');
    preg_match('/var sTitle = .*;/', $source, $match);
    $document = new documentItem();
    $document->document_srl = 1;
    $document->add('title', $title);
    Context::set('oDocument', $document, false);
    $html = renderSecurityTemplate('<script>' . $match[0] . '</script>');
    preg_match('/var sTitle = (.*);/', $html, $json);
    checkTemplate(isset($json[1]) && json_decode($json[1], true) === $document->getTitleText(), 'Board title is valid JSON: ' . $skin);
    checkTemplate(substr_count($html, '</script>') === 1, 'Board title cannot terminate script: ' . $skin);
}
echo 'OK: 6 template security checks on PHP ', PHP_VERSION, "\n";
