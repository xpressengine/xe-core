<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file whichwebserver.addon.php
     * @author NHN (developers@xpressengine.com)
     * @brief print which webserver is.
     **/

    if($called_position == 'after_module_proc' && Context::getResponseMethod()=='HTML') 
	{
		$logged_info = Context::get('logged_info');
		if($logged_info->is_admin == 'Y')
		{
			Context::addHtmlFooter("\n<!-- I`m ". $_ENV['HOSTNAME'] ." -->\n");
		}
    }
?>
