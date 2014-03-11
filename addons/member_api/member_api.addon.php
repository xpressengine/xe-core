<?php
if (!defined('__XE__')) exit();

if ($called_position == 'before_module_init' && $_REQUEST['act'] == 'api') {
	$vars = Context::getRequestVars();
	$key = pack('H*', '6AE0DA7F-3638-47AB-8EBF-D8632DA3BA24');
	$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
	$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

	$oMemberModel = getModel('member');

	$result = new stdClass;
	$result->result = false;
	$result->message = 'failed';
	$result->data = null;

	if ($vars->resource == 'member_info') {
		$email = base64_decode($vars->email);
		$iv_dec = substr($email, 0, $iv_size);
		$email = substr($email, $iv_size);
		$email = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $email, MCRYPT_MODE_CBC, $iv_dec));

		$member_info = $oMemberModel->getMemberInfoByEmailAddress($email);

		if (!$member_info) {
			$result->message = 'not_exists_member';
		} else {
			$password = base64_decode($vars->password);
			$iv_dec = substr($password, 0, $iv_size);
			$password = substr($password, $iv_size);
			$password = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $password, MCRYPT_MODE_CBC, $iv_dec));

			if ($password == $member_info->password) {
				$data = new stdClass;
				$data->member_srl = $member_info->member_srl;
				$data->nick_name = $member_info->nick_name;
				$data->email = $member_info->email_address;

				$result->result = true;
				$result->message = 'success';
				$result->data = json_encode($data);
			} else {
				$result->message = 'invalid_password';
			}
		}
	}

	if ($result->data) {
		$ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $result->data, MCRYPT_MODE_CBC, $iv);
		$ciphertext = $iv . $ciphertext;
		$ciphertext_base64 = base64_encode($ciphertext);
		$result->data = $ciphertext_base64;
	}

	header("Content-Type: application/json; charset=UTF-8");
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Pragma: no-cache");

	echo json_encode($result);

	exit();
}
