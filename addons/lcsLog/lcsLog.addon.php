<?php
    if(!defined("__ZBXE__")) exit();

    /**
     * @file lcsLog.addon.php
     * @author zero (zero@nzeo.com)
     * @brief lcsLog addon
     **/

    if(Context::get('module')=='admin' || $called_position != 'before_module_init') return;

    // Context::addJsFile()을 이용하면 끝
    Context::addHtmlFooter('<script type="text/javascript" src="http://static.analytics.openapi.naver.com/js/wcslog.js"></script>');
    Context::addHtmlFooter('<script type="text/javascript"> if(!wcs_add) var wcs_add = {}; wcs_add["wa"] = "2e8037cfde8b"; try { wcs_do(); } catch(e) { }</script>');
?>
