<?php
if(!defined("__ZBXE__")) exit();

$targetHost = "contest.xpressengine.com";
$targetMid = "contest";
// called_position가 before_module_init 이고 module이 admin이 아닐 경우
if($called_position == 'before_module_init' && $_SERVER["HTTP_HOST"] == $targetHost) {
	$document_srl = Context::get('document_srl');
	$mid = Context::get('mid');
	$module = Context::get('module');
	$module_srl = Context::get('module_srl');
	if(!$document_srl && !$mid && !$module && !$module_srl)
	{
        Context::set('mid', $targetMid);
        $this->mid = $targetMid;
	}
}
?>
