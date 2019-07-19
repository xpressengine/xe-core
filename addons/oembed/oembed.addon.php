<?php
/* Copyright (C) XEHub <https://www.xehub.io> */

if(!defined('__XE__'))
{
	exit();
}

if($called_position == 'after_module_proc' && Context::getResponseMethod() == 'HTML')
{
	Context::loadFile('./addons/oembed/jquery.oembed.css');
	Context::loadFile(array('./addons/oembed/jquery.oembed.js', 'body', '', null), true);
	Context::loadFile(array('./addons/oembed/oembed.js', 'body', '', null), true);
}

/* End of file */
