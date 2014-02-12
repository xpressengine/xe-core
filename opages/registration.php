<?php
    if(!defined("__ZBXE__")) exit();
?>
<!-- #contest_registration -->
<?PHP
if(date('Ymd')>'20100110'){
?>
<div id="contest_registration">
<h1 class="h1"><img src="http://contest.xpressengine.com/layouts/xe_contest/img_content/h1Registration.gif" width="127" height="50" alt="응모작 제출" /></h1>
<p style="margin-bottom:3em">제출이 마감되었습니다.</p>
</div>

<?PHP
}else{
?>

<div id="contest_registration">
<h1 class="h1"><img src="http://contest.xpressengine.com/layouts/xe_contest/img_content/h1Registration.gif" width="127" height="50" alt="응모작 제출" /></h1>
<p style="margin-bottom:3em">응모작 제출 기간은 2010년 1월 10일 (일) 까지 입니다.<br />
(XE 공식사이트 계정을 이용해 로그인하실 수 있습니다.)</p>
<p><a href="http://contest.xpressengine.com/?mid=contestUpload&act=dispBoardWrite"><img src="img/buttonRegistration.gif" alt="응모작 접수" /></a></p>
<p style="margin-bottom:3em">응모작 제출을 위해서는 회원가입 및 로그인이 필요합니다. 로그아웃 상태인 경우, 로그인 페이지로 이동합니다.</p>
<p><a href="http://contest.xpressengine.com/?mid=contestUpload"><img src="img/buttonRegistrationModify.gif" alt="내 응모작 확인/수정" /></a></p>
<p>이미 제출한 응모작의 열람과 수정이 가능합니다. 접수 기간 중에는 타인의 응모작은 볼 수 없습니다.</p>
</div>
<?PHP
}
?>
<!-- /#contest_registration -->
