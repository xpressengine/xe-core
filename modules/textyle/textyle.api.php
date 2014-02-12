<?php
    /**
     * @class  textyleAPI
     * @author NHN (developers@xpressengine.com)
     * @brief  textyle module Action API class
     **/

    class textyleAPI extends textyle {

        /**
         * @brief check alias
         **/
        function dispTextylePostCheckAlias(&$oModule) {
            $oModule->add('document_srl',Context::get('document_srl'));
        }

    }

?>
