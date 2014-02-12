<?php
    /**
     * @class  wikiWAP
     * @author NHN (developers@xpressengine.com)
     * @brief  wiki 모듈의 WAP class
     **/

    class wikiWAP extends wiki {

        /**
         * @brief wap procedure method
         **/
        function procWAP(&$oMobile) {
            // Check permissions
            if(!$this->grant->list || $this->module_info->consultation == 'Y') return $oMobile->setContent(Context::getLang('msg_not_permitted'));

            // Create document model
            $oDocumentModel = &getModel('document');

            // Check if you have selected an existing document (article)
            $document_srl = Context::get('document_srl');
            if($document_srl) {
                $oDocument = $oDocumentModel->getDocument($document_srl);
                if($oDocument->isExists()) {
                    // Verify permissions to view
                    if(!$this->grant->view) return $oMobile->setContent(Context::getLang('msg_not_permitted'));

                    // Set browser's title
                    Context::setBrowserTitle($oDocument->getTitleText());

                    // If a comment
                    if($this->act=='dispWikiContentView') 
					{
                        $oCommentModel = &getModel('comment');
                        $output = $oCommentModel->getCommentList($oDocument->document_srl, 0, false, $oDocument->getCommentCount());

                        $content = '';
                        if(count($output->data)) {
                            foreach($output->data as $key => $val){
                                $oComment = new commentItem();
                                $oComment->setAttribute($val);
                                if(!$oComment->isAccessible()) continue;
                                $content .= "<b>".$oComment->getNickName()."</b> (".$oComment->getRegdate("Y-m-d").")<br>\r\n".$oComment->getContent(false,false)."<br>\r\n";
                            }
                        }

                        // Setting the content
                        $oMobile->setContent( $content );

                        // Back to the list specified in the parent page
                        $oMobile->setUpperUrl( getUrl('act',''), Context::getLang('cmd_go_upper') );

						// Post or show a comment
                    }
					else
					{
                        // Prepare the content (removes all tags from content)
                        $content = strip_tags(str_replace('<p>','<br>&nbsp;&nbsp;&nbsp;',$oDocument->getContent(false,false,false)),'<br><b><i><u><em><small><strong><big>');


                        // For information on the top of the output (including the comments link)
                        $content = Context::getLang('replies').' : <a href="'.getUrl('act','dispWikiContentView').'">'.$oDocument->getCommentCount().'</a><br>'."\r\n".$content;
                        $content = '<b>'.$oDocument->getNickName().'</b> ('.$oDocument->getRegdate("Y-m-d").")<br>\r\n".$content;
                        
                        // Setting the content
                        $oMobile->setContent( $content );

                        // Back to the list specified in the parent page
                        $oMobile->setUpperUrl( getUrl('document_srl',''), Context::getLang('cmd_list') );

                    }

                    return;
                }
            }

            // List of posts
            $args->module_srl = $this->module_srl; 
            $args->page = Context::get('page');; 
            $args->list_count = 9;
            $args->sort_index = $this->module_info->order_target?$this->module_info->order_target:'list_order';
            $args->order_type = $this->module_info->order_type?$this->module_info->order_type:'asc';
            $output = $oDocumentModel->getDocumentList($args, $this->except_notice);
            $document_list = $output->data;
            $page_navigation = $output->page_navigation;

            $childs = array();
            if($document_list && count($document_list)) {
                foreach($document_list as $key => $val) {
                    $href = getUrl('mid',$_GET['mid'],'document_srl',$val->document_srl);
                    $obj = null;
                    $obj['href'] = $val->getPermanentUrl();

                    $title = htmlspecialchars($val->getTitleText());
                    if($val->getCommentCount()) $title .= ' ['.$val->getCommentCount().']';
                    $obj['link'] = $obj['text'] = '['.$val->getNickName().'] '.$title;
                    $childs[] = $obj;
                }
                $oMobile->setChilds($childs); 
            }

            $totalPage = $page_navigation->last_page;
            $page = (int)Context::get('page');
            if(!$page) $page = 1;

            // Specify the next/prev Url
            if($page>1) $oMobile->setPrevUrl(getUrl('mid',$_GET['mid'],'page',$page-1), sprintf('%s (%d/%d)', Context::getLang('cmd_prev'), $page-1, $totalPage));

            if($page<$totalPage) $oMobile->setNextUrl(getUrl('mid',$_GET['mid'],'page',$page+1), sprintf('%s (%d/%d)', Context::getLang('cmd_next'), $page+1, $totalPage));

            $oMobile->mobilePage = $page;
            $oMobile->totalPage = $totalPage;
        }
    }

?>
