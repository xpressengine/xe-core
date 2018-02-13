window.runOnce = false;
function alertNewMemberAcceptSetting(isEnable, msg)
{
	if(window.runOnce) return;
	window.runOnce = true;
	var $ = jQuery;
	$("#signupTab>a").text("⚠ " + $("#signupTab>a").text()).css("color", "#7F2000");
	$(".x_msg-warn").show();
}