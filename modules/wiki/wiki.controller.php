<?php
/**
* @class wikiController
* @developer NHN (developers@xpressengine.com)
* @brief  wiki controller class
*/
class WikiController extends Wiki
{
	/**
	 * @brief Inserts a wiki document
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function procWikiInsertDocument() 
	{
		// Create object model of document module
		$oDocumentModel = &getModel('document');
		// Create object controller of document module
		$oDocumentController = &getController('document');
		
		// Check permissions
		if(!$this->grant->write_document) 
		{
			return new Object(-1, 'msg_not_permitted'); 
		}
		
		$entry = Context::get('entry');
		// Get required parameters settings
		$obj = Context::getRequestVars(); 
		$obj->module_srl = $this->module_srl;
		if($this->module_info->use_comment != 'N') 
		{
			$obj->allow_comment = 'Y';
		}
		else
		{
			$obj->allow_comment = 'N';
		}
		
		// Set nick_name
		if(!$obj->nick_name) 
		{
			$logged_info = Context::get('logged_info');
			if($logged_info)
			{
				$obj->nick_name = $logged_info->nick_name;
			}
			else
			{
				$obj->nick_name = 'anonymous';
			}
		}
		
		if($obj->is_notice != 'Y' || !$this->grant->manager) 
		{
			$obj->is_notice = 'N'; 
		}
		
		settype($obj->title, "string");
		if($obj->title == '')
		{
			$obj->title = cut_str(strip_tags($obj->content), 20, '...');
		}
		
		//If you still Untitled
		if($obj->title == '') 
		{
			$obj->title = 'Untitled';
		}
		
		// Check that already exist
		$oDocument = $oDocumentModel->getDocument($obj->document_srl, $this->grant->manager);
		// Get linked docs (by alias)
		$wiki_text_parser = $this->getWikiTextParser(); 
		$linked_documents_aliases = $wiki_text_parser->getLinkedDocuments($obj->content);
		
		// Modified if it already exists
		if($oDocument->isExists() && $oDocument->document_srl == $obj->document_srl) 
		{
			// If we have section, update content: retrieve full text and insert new section in it
			$section = Context::get('section');
			if (isset($section))
			{
				$full_content = $oDocument->get('content');
				$section_content = $obj->content;

                $lang = $this->module_info->markup_type;
                if ($lang == 'mediawiki_markup') $lang = 'wikitext';
                elseif ($lang == 'googlecode_markup') $lang = 'googlecode';
                elseif ($lang == 'xe_wiki_markup') $lang = 'xewiki';
                $wt = new WTParser($full_content, $lang);
				$wt->setText($section_content, (int)$section);
				$new_content = $wt->getText();

				$obj->content = $new_content;
			}

			$output = $oDocumentController->updateDocument($oDocument, $obj);
			
			// Have been successfully modified the hierarchy/ alias change
			if($output->toBool()) 
			{
				// Update alias
				$oDocumentController->deleteDocumentAliasByDocument($obj->document_srl); 
				$aliasName = Context::get('alias');
				if(!$aliasName)
				{
					$aliasName = $this->beautifyEntryName($obj->title); 
				}
				$oDocumentController->insertAlias($obj->module_srl, $obj->document_srl, $aliasName);
				
				// Update linked docs
				if(count($linked_documents_aliases) > 0) 
				{
					$oWikiController = &getController('wiki'); 
					$oWikiController->updateLinkedDocuments($obj->document_srl, $linked_documents_aliases, $obj->module_srl);
				}
			}
			$msg_code = 'success_updated';
			// remove document from cache
			$oCacheHandler = CacheHandler::getInstance('object', NULL, TRUE);
			if($oCacheHandler->isSupport()) 
			{
				$object_key = sprintf('%s.%s.php', $obj->document_srl, Context::getLangType()); 
				$cache_key = $oCacheHandler->getGroupKey('wikiContent', $object_key); 
				$oCacheHandler->delete($cache_key);
			}
		} // if this is a new document
		else 
		{
			$output = $oDocumentController->insertDocument($obj); 
			$msg_code = 'success_registed'; 
			$obj->document_srl = $output->get('document_srl');
			
			// Insert Alias
			$aliasName = Context::get('alias');
			if(!$aliasName)
			{
				$aliasName = $this->beautifyEntryName($obj->title); 
			}
			$oDocumentController->insertAlias($obj->module_srl, $obj->document_srl, $aliasName);
			
			// Insert linked docs
			if(count($linked_documents_aliases) > 0) 
			{
				$oWikiController = &getController('wiki'); 
				$oWikiController->insertLinkedDocuments($obj->document_srl, $linked_documents_aliases, $obj->module_srl);
			}
		}
		
		// Stop when an error occurs
		if(!$output->toBool()) 
		{
			return $output; 
		}
		
		$this->recompileTree($this->module_srl);
		
		// Returns the results
		$entry = $oDocumentModel->getAlias($output->get('document_srl'));
		
		// Registration success message
		$this->setMessage($msg_code);
		if($entry) 
		{
			$site_module_info = Context::get('site_module_info'); 
			$url = getNotEncodedSiteUrl($site_module_info->document, '', 'mid', $this->module_info->mid, 'entry', $entry, 'document_srl', '');
		}
		else 
		{
			$url = getNotEncodedSiteUrl($site_module_info->document, '', 'document_srl', $output->get('document_srl'));
		}
		if($section)
		{
			$section_title = Context::get('section_title');
			$url .= '#' . $section_title;
		}
		$this->setRedirectUrl($url);
	}

	/**
     * @brief Preview for wikitext
     * @developer Florin Ercus (xe_dev@arnia.ro)
     * @access public
     * @return
     *
     * Called through AJAX, returns JSON
     */
    function procWikiTextParse()
    {
        require_once "lib/WikiText.class.php";
        $content = Context::get('content');
        $lang = Context::get('markup');
        $this->module_info->markup_type = $lang;
        $parser = $this->getWikiTextParser();
        $content = $parser->parse($content, false);
        $rez = array('content'=>$content);
        //@TODO: avoid this by using the default ajax mechanism
        echo json_encode($rez);
        die;
    }

	/**
	 * @brief Checks to see if document was edited by someone else, so that we won't override their changes on save
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @return
	 * 
	 * Called through AJAX, returns JSON
	 */
	function procWikiCheckIfDocumentWasUpdated()
	{
		// Force json response
		Context::setRequestMethod('JSON');
		
		$document_srl = Context::get('document_srl');
		if(!$document_srl) 
		{
			$this->add('updated', false);
			return;
		}
		
		$previous_doc_edit = Context::get('latest_doc_edit');
		
		$oDocumentModel = &getModel('document');
		$output = $oDocumentModel->getHistories($document_srl, 1, 1);
		if($output->toBool() && $output->data) // If we did find previous edits
		{
			$history = array_pop($output->data);
			$latest_doc_edit = $history->history_srl;
			if((!$previous_doc_edit && $latest_doc_edit) 
					|| ($previous_doc_edit < $latest_doc_edit))
			{
				$this->add('updated', true);
				return;				
			}

		}		
		
		$this->add('updated', false);
		return;		
	}
	
	/**
	 * @brief Delete database references to links in current document
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $document_srl 
	 * @return type 
	 */
	function deleteLinkedDocuments($document_srl) 
	{
		
		$args->document_srl = $document_srl; 
		$output = executeQuery('wiki.deleteLinkedDocuments', $args); 
		return $output;
	}
	
	/**
	 * @brief Save all internal links in current document to the database
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $document_srl
	 * @param $alias_list
	 * @param $module_srl
	 * @return $output 
	 */
	function insertLinkedDocuments($document_srl, $alias_list, $module_srl) 
	{
		$args->document_srl = $document_srl; 
		$args->alias_list = implode(',', $alias_list); 
		$args->module_srl = $module_srl; 
		$output = executeQuery('wiki.insertLinkedDocuments', $args); 
		return $output;
	}
	
	/**
	 * @brief Updates info about links in current document
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $document_srl
	 * @param $alias_list
	 * @param $module_srl
	 * @return type 
	 */
	function updateLinkedDocuments($document_srl, $alias_list, $module_srl) 
	{
		$output = $this->deleteLinkedDocuments($document_srl);
		if($output->toBool()) 
		{
			$output = $this->insertLinkedDocuments($document_srl, $alias_list, $module_srl); 
		}
		return $output;
	}
	
	/**
	 * @brief Register comments on the wiki if user is not logged
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access public
	 * @return
	 */
	function procWikiInsertCommentNotLogged() 
	{
		$this->procWikiInsertComment();
	}
	
	/**
	 * @brief Register comments on the wiki
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function procWikiInsertComment() 
	{
		// Check permissions
		if(!$this->grant->write_comment) 
		{
			return new Object(-1, 'msg_not_permitted');
		}
		
		// extract data required
		$obj = Context::gets('document_srl', 'comment_srl', 'parent_srl', 'content', 'password', 'nick_name', 'nick_name', 'member_srl', 'email_address', 'homepage', 'is_secret', 'notify_message');
		$obj->module_srl = $this->module_srl;
		// Check for the presence of document object
		$oDocumentModel = &getModel('document'); 
		$oDocument = $oDocumentModel->getDocument($obj->document_srl);
		if(!$oDocument->isExists()) 
		{
			return new Object(-1, 'msg_not_permitted');
		}
		// Create object model of document module
		$oCommentModel = &getModel('comment');
		// Create object controller of document module
		$oCommentController = &getController('comment');
		
		// Check for the presence of comment_srl
		// if comment_srl is n/a then retrieves a value with getNextSequence()
		if(!$obj->comment_srl) 
		{
			$obj->comment_srl = getNextSequence();
		}
		else 
		{
			$comment = $oCommentModel->getComment($obj->comment_srl, $this->grant->manager);
		}
		
		// If there is no new comment_srl
		if($comment->comment_srl != $obj->comment_srl) 
		{
			// If there is no new parent_srl	
			if($obj->parent_srl) 
			{
				$parent_comment = $oCommentModel->getComment($obj->parent_srl);
				if(!$parent_comment->comment_srl) 
				{
					return new Object(-1, 'msg_invalid_request');
				}
				$output = $oCommentController->insertComment($obj);
			}
			else 
			{
				$output = $oCommentController->insertComment($obj);
			}
			if($output->toBool()) 
			{
				//check if comment writer is admin or not
				$oMemberModel = &getModel("member");
				if(isset($obj->member_srl) && !is_null($obj->member_srl)) 
				{
					$member_info = $oMemberModel->getMemberInfoByMemberSrl($obj->member_srl);
				}
				else 
				{
					$member_info->is_admin = 'N';
				}
				// if current module is using Comment Approval System and comment write is not admin user then
				if(method_exists($oCommentController,'isModuleUsingPublishValidation') 
						&& $oCommentController->isModuleUsingPublishValidation($this->module_info->module_srl) 
						&& $member_info->is_admin != 'Y') 
				{
					$this->setMessage('comment_to_be_approved');
				}
				else 
				{
					$this->setMessage('success_registed');
				}
			}
		}
		else // If you have to modify comment_srl
		{
			$obj->parent_srl = $comment->parent_srl; 
			$output = $oCommentController->updateComment($obj, $this->grant->manager);
			//$comment_srl = $obj->comment_srl;
		}
		if(!$output->toBool()) 
		{
			return $output; 
		}
		
		$this->add('mid', Context::get('mid')); 
		$this->add('document_srl', $obj->document_srl); 
		$this->add('comment_srl', $obj->comment_srl); 
		$this->setRedirectUrl(Context::get('success_return_url'));
	}
	
	/**
	 * @brief Delete article from the wiki 
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function procWikiDeleteDocument() 
	{
		$oDocumentController = &getController('document'); 
		$oDocumentModel = &getModel('document');
		
		// Check permissions
		if(!$this->grant->delete_document) 
		{
			return new Object(-1, 'msg_not_permitted'); 
		}
		
		$document_srl = Context::get('document_srl');
		if(!$document_srl) 
		{
			return new Object(-1, 'msg_invalid_request'); 
		}
		
		$oDocument = $oDocumentModel->getDocument($document_srl);
		if(!$oDocument || !$oDocument->isExists()) 
		{
			return new Object(-1, 'msg_invalid_request');
		}
		
		$output = $oDocumentController->deleteDocument($oDocument->document_srl);
		
		if(!$output->toBool()) 
		{
			return $output; 
		}
		$oDocumentController->deleteDocumentAliasByDocument($oDocument->document_srl); 
		$this->recompileTree($this->module_srl); 
		$tree_args->module_srl = $this->module_srl; 
		$tree_args->document_srl = $oDocument->document_srl; 
		$output = executeQuery('wiki.deleteTreeNode', $tree_args);
		// remove document from cache
		$oCacheHandler = CacheHandler::getInstance('object', NULL, TRUE);
		if($oCacheHandler->isSupport()) 
		{
			$object_key = sprintf('%s.%s.php', $document_srl, Context::getLangType()); 
			$cache_key = $oCacheHandler->getGroupKey('wikiContent', $object_key); 
			$oCacheHandler->delete($cache_key);
		}
		$site_module_info = Context::get('site_module_info'); 
		$this->setRedirectUrl(getSiteUrl($site_module_info->domain, '', 'mid', $this->module_info->mid));
	}
	
	/**
	 * @brief Delete comment from the wiki
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function procWikiDeleteComment() 
	{
		// check the comment's sequence number
		$comment_srl = Context::get('comment_srl');
		if(!$comment_srl) 
		{
			return $this->doError('msg_invalid_request');
		}
		// create controller object of comment module
		$oCommentController = &getController('comment'); 
		$output = $oCommentController->deleteComment($comment_srl, $this->grant->manager);
		if(!$output->toBool()) 
		{
			return $output; 
		}
		
		$this->add('mid', Context::get('mid')); 
		$this->add('page', Context::get('page')); 
		$this->add('document_srl', $output->get('document_srl')); 
		$this->setRedirectUrl(Context::get('success_return_url'));
		//$this->setMessage('success_deleted');
	}
	
	/**
	 * @brief Change position of the document on hierarchy
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return 
	 */
	function procWikiMoveTree()
	{
		// Check permissions
		if(!$this->grant->write_document)
		{
			return new Object(-1, 'msg_not_permitted');
		}
		
		// request arguments
		$args = Context::gets('parent_srl', 'target_srl', 'source_srl');
		// retrieve Node information
		$output = executeQuery('wiki.getTreeNode', $args); 
		$node = $output->data;
		if(!$node->document_srl) 
		{
			return new Object('msg_invalid_request'); 
		}
		$args->module_srl = $node->module_srl; 
		$args->title = $node->title;
		if(!$args->parent_srl) 
		{
			$args->parent_srl = 0;
		}
		
		// target without parent list_order must have a minimum list_order
		if(!$args->target_srl)
		{
			$list_order->parent_srl = $args->parent_srl; 
			$output = executeQuery('wiki.getTreeMinListorder', $list_order);
			if($output->data->list_order) 
			{
				$args->list_order = $output->data->list_order - 1;
			}
		}
		else 
		{
			$t_args->source_srl = $args->target_srl; 
			$output = executeQuery('wiki.getTreeNode', $t_args); 
			$target = $output->data;

			if(!$target->parent_srl)
			{
				$delete_args = new stdClass;
				$delete_args->document_srl = $args->target_srl;
				$delete_args->module_srl = $target->module_srl;

				$output = executeQuery('wiki.deleteTreeNode', $delete_args);
			}
			else
			{
				$update_args = new stdClass;
				$update_args->module_srl = $target->module_srl;
				$update_args->parent_srl = $target->parent_srl;
				$update_args->list_order = $target->list_order;

				$output = executeQuery('wiki.updateTreeListOrder', $update_args);
			}

			if(!$output->toBool()) 
			{
				return $output;
			}

			$args->list_order = $target->list_order + 1;
		}
		if(!$node->is_exists) 
		{
			$output = executeQuery('wiki.insertTreeNode', $args);
		}
		else 
		{
			$output = executeQuery('wiki.updateTreeNode', $args);
		}
		if(!$output->toBool()) 
		{
			return $output;
		}
		if($args->list_order) 
		{
			$doc->document_srl = $args->source_srl; 
			$doc->list_order = $args->list_order; 
			$output = executeQuery('wiki.updateDocumentListOrder', $doc);
			if(!$output->toBool()) 
			{
				return $output;
			}
		}
		$this->recompileTree($this->module_srl);
	}
	
	/**
	 * @brief recreate Wiki the hierarchy
	 * @developer NHN (developers@xpressengine.com)	
	 * @access public
	 * @return
	 */
	function procWikiRecompileTree() 
	{
		if(!$this->grant->write_document) 
		{
			return new Object(-1, 'msg_not_permitted'); 
		}
		return $this->recompileTree($this->module_srl);
	}
	
	/**
	 * @brief recreate Wiki hierarchy
	 * @developer NHN (developers@xpressengine.com)	
	 * @access public
	 * @param $module_srl
	 * @return
	 */	
	function recompileTree($module_srl) 
	{
		$oWikiModel = &getModel('wiki'); 
		$list = $oWikiModel->loadWikiTreeList($module_srl); 
		$dat_file = sprintf('%sfiles/cache/wiki/%d.dat', _XE_PATH_, $module_srl); 
		$xml_file = sprintf('%sfiles/cache/wiki/%d.xml', _XE_PATH_, $module_srl); 
		$buff = ''; $xml_buff = "<root>\n";
		
		// cache file creation
		foreach($list as $key => $val) 
		{
			$buff .= sprintf('%d,%d,%d,%d,%s%s', $val->parent_srl, $val->document_srl, $val->depth, $val->childs, $val->title, "\n"); 
			$xml_buff .= sprintf('<node node_srl="%d" parent_srl="%d"><![CDATA[%s]]></node>%s', $val->document_srl, $val->parent_srl, $val->title, "\n");
		}
		$xml_buff .= '</root>';
		
		FileHandler::writeFile($dat_file, $buff); 
		FileHandler::writeFile($xml_file, $xml_buff); 
		return new Object();
	}
	
	/**
	 * @brief Confirm password for modifying non-members Comments
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	 */
	function procWikiVerificationPassword() 
	{
		$password = Context::get('password');
		$document_srl = Context::get('document_srl');
		$comment_srl = Context::get('comment_srl'); 
		
		$oMemberModel = &getModel('member');
		if($comment_srl) 
		{
			$oCommentModel = &getModel('comment'); 
			$oComment = $oCommentModel->getComment($comment_srl);
			if(!$oComment->isExists()) 
			{
				return new Object(-1, 'msg_invalid_request');
			}
			if(!$oMemberModel->isValidPassword($oComment->get('password'), $password)) 
			{
					return new Object(-1, 'msg_invalid_password'); 
			}
			$oComment->setGrant();
		} else {
			// get the document information
			$oDocumentModel = &getModel('document');
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if(!$oDocument->isExists()) return new Object(-1, 'msg_invalid_request');

			// compare the document password and the user input password
			if(!$oMemberModel->isValidPassword($oDocument->get('password'),$password)) return new Object(-1, 'msg_invalid_password');

			$oDocument->setGrant();

			$succes_return_url = Context::get('success_return_url');
			$this->setRedirectUrl($succes_return_url);

		}
	}
	
	/**
	 * @brief function, used by Ajax call, that return curent version and one of history version of the document for making diff
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access public
	 * @return
	 */
	function procWikiContentDiff() 
	{
		$document_srl = Context::get("document_srl"); 
		$history_srl = Context::get("history_srl"); 
		$oDocumentModel = &getModel('document'); 
		$oDocument = $oDocumentModel->getDocument($document_srl); 
		$current_content = $oDocument->get('content'); 
		$history = $oDocumentModel->getHistory($history_srl);
		$history_content = $history->content; 
		$this->add('old', $history_content); 
		$this->add('current', $current_content);
	}
	
	/**
	 * @brief function, used by Ajax call, that return HTML Comment Editor
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access public
	 * @return
	 */
	function procDispCommentEditor() 
	{
		$document_srl = Context::get("document_srl"); 
		$oDocumentModel = &getModel('document'); 
		$oDocument = $oDocumentModel->getDocument($document_srl); 
		$editor = $oDocument->getCommentEditor(); 
		$oEditorModel = &getModel('editor');
		
		// get an editor
		$option->primary_key_name = 'comment_srl'; 
		$option->content_key_name = 'content'; 
		$option->allow_fileupload = FALSE; 
		$option->enable_autosave = FALSE; 
		$option->disable_html = TRUE; 
		$option->enable_default_component = FALSE; 
		$option->enable_component = FALSE; 
		$option->resizable = TRUE; 
		$option->height = 150; 
		$editor = $oEditorModel->getEditor(0, $option); 
		
		Context::set('editor', $editor); 
		$this->add('editor', $editor);
	}
}
/* End of file wiki.controller.php */
/* Location: wiki.controller.php */
