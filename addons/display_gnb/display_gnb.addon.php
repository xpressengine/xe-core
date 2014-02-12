<?php
    if(!defined("__ZBXE__")) exit();

    if($called_position == 'before_display_content' && Context::getResponseMethod()=="HTML") {
        $php_file = sprintf('%sfiles/cache/menu/%d.php', _XE_PATH_, 19327940);
        @include($php_file);
        if($menu) Context::set('gnb_menu_list', $menu->list);

        $oTemplateHandler = TemplateHandler::getInstance();
        $output = $output . $oTemplateHandler->compile('./addons/display_gnb/tpl/','gnb.html');
    }
?>
