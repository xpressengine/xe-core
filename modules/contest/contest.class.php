<?php
    class contest extends ModuleObject {

        function moduleInstall() {
            return new Object();
        }

        function checkUpdate() {
            return false;
        }

        function moduleUpdate() {
            return new Object(0, 'success_updated');
        }

        function recompileCache() {
        }
    }
?>
