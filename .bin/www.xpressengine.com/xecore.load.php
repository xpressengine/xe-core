<?php

// get XECore index.php
define('___PATH_XE_CORE___', realpath(dirname(__FILE__)) . '/../../www.xpressengine.com/');

define('__XE__',   TRUE);
require ___PATH_XE_CORE___ . '/config/config.inc.php';
$oContext = &Context::getInstance();
$oContext->init();
