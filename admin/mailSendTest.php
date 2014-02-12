<?php
exit;
include '/home/DOMAINS/DOMAINS-XE-WEB/www.xpressengine.com/classes/mail/Mail.class.php';

// 메일 발송
            $oMail = new Mail();
            $oMail->setTitle('mail title');
            $oMail->setContent('mail content');
            $oMail->setSender( 'webmaster', 'contact@xpressengine.com');
            $oMail->setReceiptor( '체리필터', 'developers@xpressengine.com' );
?>
