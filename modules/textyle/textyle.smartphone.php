<?php
    /**
     * @class  textyleSmartphone
     * @author NHN (developers@xpressengine.com)
     * @brief  textyle module SmartPhone IPhone class
     **/

    class textyleSPhone extends textyle {

        function procSmartPhone(&$oSmartPhone) {
            $oDocumentModel = &getModel('document');

            $oDocument = Context::get('oDocument');
            if($oDocument->isExists()) {
                if(Context::get('comment') == 'true' && $oDocument->getCommentCount()) {
                    Context::set('comment_list', $oDocument->getComments());
                    $comment_page_navigation = $oDocument->comment_page_navigation;
                    if($comment_page_navigation) {
                        if($comment_page_navigation->cur_page > $comment_page_navigation->first_page) $oSmartPhone->setPrevUrl(getUrl('cpage',$comment_page_navigation->cur_page-1));
                        if($comment_page_navigation->cur_page < $comment_page_navigation->last_page) $oSmartPhone->setNextUrl(getUrl('cpage',$comment_page_navigation->cur_page+1));
                    }
                    $oSmartPhone->setParentUrl(getUrl('comment',''));
                    $tpl_file = 'comment_list';
                } else {
                    $oSmartPhone->setParentUrl(getUrl('document_srl',''));
                    $tpl_file = 'view_document';
                }
            } else {
                $page_navigation = Context::get('page_navigation');
                if($page_navigation) {
                    if($page_navigation->cur_page > $page_navigation->first_page) $oSmartPhone->setPrevUrl(getUrl('page',$page_navigation->cur_page-1));
                    if($page_navigation->cur_page < $page_navigation->last_page) $oSmartPhone->setNextUrl(getUrl('page',$page_navigation->cur_page+1));
                }
                $tpl_file = 'list';
            }

            $oTemplate = new TemplateHandler();
            $content = $oTemplate->compile($this->module_path.'tpl/smartphone', $tpl_file);
            $oSmartPhone->setContent($content);
        }
    }
?>
