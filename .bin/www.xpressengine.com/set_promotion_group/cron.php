<?php

define('___CUR_PATH___', dirname(__FILE__) );

// include config for use XECore
require realpath(___CUR_PATH___ . '/../xecore.load.php');

$oMoumiAdminController = getAdminController('moumi');
$oMoumiAdminController->cronPromotionMemberGroup(___CUR_PATH___);

