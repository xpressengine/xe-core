<?php
$id = Context::get('id');
$password = Context::get('password');

$args->user_id = $id;
$output = executeQuery('member.getMemberInfo', $args);

if ($output->data->password == md5($password)){
	$email = $output->data->email_address;	
}else if ($id && $password){
	$msg = '아이디 혹은 비밀번호가 잘못되었습니다.';
}


?>
<load target="./modules/admin/tpl/css/admin.min.css" />
<div class="x" style="padding: 20px; ">
<h1 class="h1">내 이메일 찾기</h1>

<? if (!$email){ ?>
	<? if ($msg){ ?>
	<div class="msg">
		<p class="error"><?=$msg?></p>
	</div>
	<? } ?>
<form  method="post" class="form">
	<ul>
		<li>
			<p class="q"><label for="id">아이디</label></p>
			<p class="a"><input type="text" name="id" /></p>
		</li>
		<li>
			<p class="q"><label for="password">비밀번호</label></p>
			<p class="a"><input type="password" name="password" /></p>
		</li>
	</ul>
	<div class="btnArea">
		<span class="btn"><input type="submit" value="이메일 찾기" /></span>
	</div>
</form>
<? }else{ ?>
<ul class="form">
	<li>
		<p class="q">이메일</p>
		<p class="a"><? echo $email ?></p>
	</li>
</ul>
<? } ?>

</div>
