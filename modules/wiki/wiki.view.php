<?php
/**
 * @class  wikiView
 * @developer NHN (developers@xpressengine.com)
 * @brief  wiki module View class
 */
class WikiView extends Wiki
{
	var $list;
	var $search_option = array('title', 'content', 'title_content', 'comment', 'user_name', 'nick_name', 'user_id', 'tag');

	/**
	 * @brief Class initialization
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return void
	 */
	function init()
	{
		/*
		 * Set the path to skins folder
		 * If current selected skin does not exist, fallback to default skin: xe_wiki
		 */
		$template_path = sprintf("%sskins/%s/", $this->module_path, $this->module_info->skin);
		if(!is_dir($template_path) || !$this->module_info->skin)
		{
			$this->module_info->skin = 'xe_wiki';
			$template_path = sprintf("%sskins/%s/", $this->module_path, $this->module_info->skin);
		}
		$this->setTemplatePath($template_path);

		$oModuleModel = &getModel('module');
		$document_config = $oModuleModel->getModulePartConfig('document', $this->module_info->module_srl);
		if(!isset($document_config->use_history))
		{
			$document_config->use_history = 'N';
		}
		$this->use_history = $document_config->use_history;
		Context::set('use_history', $document_config->use_history);

		Context::addJsFile($this->module_path . 'tpl/js/wiki.js');
		Context::set('grant', $this->grant);
		Context::set('langs', Context::loadLangSupported());

		// Force simple textarea if markup is Markdown, Google Code or MediaWiki
		$editor_config = $oModuleModel->getModulePartConfig('editor', $this->module_info->module_srl);
		if($this->module_info->markup_type != 'xe_wiki_markup'
				&& (!$editor_config || $editor_config->sel_editor_colorset != 'white_text_usehtml'))
		{
			$editor_config->editor_skin = 'xpresseditor';
			$editor_config->sel_editor_colorset = 'white_text_usehtml';
			$editor_config->content_style = 'default';
			$oModuleController = &getController('module');
			$oModuleController->insertModulePartConfig('editor', $this->module_info->module_srl, $editor_config);
		}

		// Load wiki title
		if(!isset($this->module_info->title))
		{
			$this->module_info->title = $this->module_info->browser_title;
		}

		// Load left side tree, if tree skin is used
		if($this->module_info->skin == 'xe_wiki_tree')
		{
			$this->getLeftMenu();
		}

		$security = new Security($this->module_info);
		$security->encodeHTML('title');
	}

	/**
	 * @brief Displays wiki document view
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return
	*/
	function dispWikiContent()
	{
		$output = $this->dispWikiContentView();
		if(!$output->toBool())
		{
			return ;
		}
	}

	/**
	 * @brief Displays the history of the particular wiki page
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	*/
	function dispWikiHistory()
	{
		$oDocumentModel = &getModel('document');
		$document_srl = Context::get('document_srl');
		$page = Context::get('page');
		$entry = Context::get('entry');

		if(!$document_srl)
		{
			if(!$entry)
			{
				$oWikiModel = &getModel('wiki');
				$root = $oWikiModel->getRootDocument($this->module_info->module_srl);
				$document_srl = $root->document_srl;
				$entry = $oDocumentModel->getAlias($document_srl);
				Context::set('entry', $entry);
				Context::set('document_srl', $document_srl);
			}
			$document_srl = $oDocumentModel->getDocumentSrlByAlias($this->module_info->mid, $entry);
			if(!$document_srl)
			{
				$document_srl = $oDocumentModel->getDocumentSrlByTitle($this->module_info->module_srl, $entry);
			}
		}

		$oDocument = $oDocumentModel->getDocument($document_srl);
		if(!$oDocument->isExists())
		{
			return $this->stop('msg_invalid_request');
		}

		$output = $oDocumentModel->getHistories($document_srl, 10, $page);
		if(!$output->toBool() || !$output->data)
		{
			Context::set('histories', array());
		}
		else
		{
			Context::set('histories', $output->data);
			Context::set('page', $output->page);
			Context::set('page_navigation', $output->page_navigation);
		}
		Context::set('oDocument', $oDocument);

		$this->setTemplateFile('document_histories');

		$security = new Security();
		$security->encodeHTML('histories..nick_name');

		return new Object(0, 'success');
	}

    /**
     * @return Object
     */
    function dispWikiHistoryCompare()
    {
        $old_history_srl = Context::get('old_history_srl');
        $history_srl = Context::get('history_srl');
        $document_srl = Context::get('document_srl');

        if(!$old_history_srl || !$document_srl)
        {
            return new Object(-1, 'msg_invalid_request');
        }

        $oDocumentModel = &getModel('document');

        $oDocument = $oDocumentModel->getDocument($document_srl);
        Context::set('oDocument', $oDocument);
		$entry = $oDocumentModel->getAlias($document_srl);
		Context::set('entry', $entry);

		// Set up old version
        $output = $oDocumentModel->getHistory($old_history_srl);
		$old_version = new stdClass;
		$old_version->content = $output->content;
		$old_version->regdate = $output->regdate;

		// Set up new version (can be either history or document itself)
		$new_version = new stdClass;
        if(!$history_srl || $history_srl == $document_srl)
        {
            $new_version->content = $oDocument->get('content');
			$new_version->regdate = $oDocument->get('last_update');
        }
        else
        {
            $output = $oDocumentModel->getHistory($history_srl);
			$new_version->content = $output->content;
			$new_version->regdate = $output->regdate;
        }

        // Include the diff class
        require_once dirname(__FILE__).'/lib/Diff.php';

        // Initialize the diff class
        $a = explode("\n", str_replace("\r", '', $old_version->content));
        $b = explode("\n", str_replace("\r", '', $new_version->content));
        $diff = new Diff($a, $b, array());

        // Generate a side by side diff
        require_once dirname(__FILE__).'/lib/Diff/Renderer/Html/SideBySide.php';
        $renderer = new Diff_Renderer_Html_SideBySide;
        $diff_html = $diff->Render($renderer);
		if($diff_html == "")
		{
			$diff_html = Context::getLang('diff_no_differences');
		}
		else
		{
			// Table header is hardcoded in the Renderer class, so we need to customize it
			$old_version_header = Context::getLang('diff_old_version');
			$old_version_header .= '<br />';
			$old_version_header .= zdate($old_version->regdate, 'Y.m.d H:i:s');
			$diff_html = str_replace("Old Version", $old_version_header, $diff_html);

			$new_version_header = Context::getLang('diff_new_version');
			$new_version_header .= '<br />';
			$new_version_header .= zdate($new_version->regdate, 'Y.m.d H:i:s');
			$diff_html = str_replace("New Version", $new_version_header, $diff_html);
		}

        Context::set('old_version', $old_version);
        Context::set('new_version', $new_version);
        Context::set('diff_html', $diff_html);

        $this->setTemplateFile('document_compare');

		return new Object(0, 'success');
    }

	/**
	 * @brief Document editing screen
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	*/
	function dispWikiEditPage()
	{
		if(!$this->grant->write_document)
		{
			return $this->dispWikiMessage('msg_not_permitted');
		}

		$oDocumentModel = & getModel('document');
		$document_srl = Context::get('document_srl');
		$entry = Context::get('entry');
		$section = Context::get('section');

		if(!$document_srl)
		{
			$mid = Context::get('mid');
			$document_srl = $oDocumentModel->getDocumentSrlByAlias($mid, $entry);
		}

		$oDocument = $oDocumentModel->getDocument(0, $this->grant->manager);
		$oDocument->setDocument($document_srl);

		$oDocument->add('module_srl', $this->module_srl);

		if($oDocument->isExists())
		{
			// Set up document alias
			$oDocument->add('alias', $oDocumentModel->getAlias($document_srl));

			// Prepare doc_srl of latest edit made to this document - used for
			// preventing content override when more than one user edits a the same document
			$output = $oDocumentModel->getHistories($document_srl, 1, 1);
			if($output->toBool() && $output->data)
			{
				$history = array_pop($output->data);
				$latest_doc_edit = $history->history_srl;
				Context::set('latest_doc_edit', $latest_doc_edit);
			}

			// Update content, when paragraph edit is used
			if (isset($section))
			{
				require_once $this->module_path . 'lib/WikiText.class.php';
				$content = $oDocument->get('content');
                $lang = $this->module_info->markup_type;
                if ($lang == 'mediawiki_markup') $lang = 'wikitext';
                elseif ($lang == 'googlecode_markup') $lang = 'googlecode';
                //markdown stays markdown
                elseif ($lang == 'xe_wiki_markup') $lang = 'xewiki';
                $wt = new WTParser($content, $lang);
				$paragraph = $wt->getText((int)$section);
                $oDocument->add('content', $paragraph);
			}
		}
		else
		{
			$oDocument->add('title', $entry);
			$alias = $this->beautifyEntryName($entry);
			$oDocument->add('alias', $alias);
		}
		Context::set('document_srl', $document_srl);
		Context::set('oDocument', $oDocument);

		// Document status list
		$statusNameList = $this->_getStatusNameList();
		if(count($statusNameList) > 0) Context::set('status_list', $statusNameList);

		$history_srl = Context::get('history_srl');
		if($history_srl)
		{
			$output = $oDocumentModel->getHistory($history_srl);
			if($output && $output->content != NULL)
			{
				Context::set('history', $output);
				$security = new Security();
				$security->encodeHTML('history.nick_name');
			}
		}
		$this->setTemplateFile('document_edit');

		return new Object(0, 'success');
	}

	/**
	 * @brief Displaying custom error / succes message
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @param $msg_code string
	 * @return Object
	 */
	function dispWikiMessage($msg_code)
	{
		$msg = Context::getLang($msg_code);
		if(!$msg)
		{
			$msg = $msg_code;
		}

		Context::set('message', $msg);
		$this->setTemplateFile('message');

		return new Object(0, 'success');
	}

	/**
	 * @brief View a list of wiki's articles
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	*/
	function dispWikiTitleIndex()
	{
		$page = Context::get('page');
		$oDocumentModel = &getModel('document');
		$obj->module_srl = $this->module_info->module_srl;
		$obj->sort_index = 'update_order';
		$obj->page = $page;
		$obj->list_count = 50;
		$obj->search_keyword = Context::get('search_keyword');
		$obj->search_target = Context::get('search_target');

		$output = $oDocumentModel->getDocumentList($obj);

		if($output->data)
		{
			foreach($output->data as $no => $val)
			{
				$alias = $oDocumentModel->getAlias($val->document_srl);
				$output->data[$no]->add('alias', $alias);
			}
		}
		Context::set('document_list', $output->data);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('page_navigation', $output->page_navigation);

		// search options settings
		foreach($this->search_option as $opt)
		{
			$search_option[$opt] = Context::getLang($opt);
		}

		Context::set('search_option', $search_option);
		$this->setTemplateFile('title_index');

		return new Object(0, 'success');
	}

	/**
	 * @brief hierarchical view of the appropriate wiki
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	 */
	function dispWikiTreeIndex()
	{
		$oWikiModel = &getModel('wiki');
		Context::set('document_tree', $oWikiModel->readWikiTreeCache($this->module_srl));
		$this->setTemplateFile('tree_list');

		$security = new Security();
		$security->encodeHTML('document_tree..');

		return new Object(0, 'success');
	}

	/**
	 * @brief Display screen for changing the hierarchy
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	*/
	function dispWikiModifyTree()
	{
		if(!$this->grant->write_document)
		{
			return new Object(-1, 'msg_not_permitted');
		}

		Context::set('isManageGranted', $this->grant->write_document ? 'true' : 'false');
		$this->setTemplateFile('modify_tree');

		return new Object(0, 'success');
	}

	/**
	 * @brief View update history
	 * @developer NHN (developers@xpressengine.com)
	 * @access private
	 * @param $entry Document alias
	 * @return void
	*/
	function addToVisitLog($entry)
	{
		$module_srl = $this->module_info->module_srl;
		if(!$_SESSION['wiki_visit_log'])
		{
			$_SESSION['wiki_visit_log'] = array();
		}
		if(!$_SESSION['wiki_visit_log'][$module_srl] || !is_array($_SESSION['wiki_visit_log'][$module_srl]))
		{
			$_SESSION['wiki_visit_log'][$module_srl] = array();
		}
		else
		{
			foreach($_SESSION['wiki_visit_log'][$module_srl] as $key => $value)
			{
				if($value == $entry)
				{
					unset($_SESSION['wiki_visit_log'][$module_srl][$key]);
				}
			}
			if(count($_SESSION['wiki_visit_log'][$module_srl]) >= 5)
			{
				array_shift($_SESSION['wiki_visit_log'][$module_srl]);
			}
		}
		$_SESSION['wiki_visit_log'][$module_srl][] = $entry;
	}

	/**
	 * @brief Wiki document view
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	*/
	function dispWikiContentView()
	{
		$oWikiModel = &getModel('wiki');
		$oDocumentModel = &getModel('document');

		// The requested order parameter values
		$document_srl = Context::get('document_srl');

		$entry = Context::get('entry');
		if(!$document_srl)
		{
			if(!$entry)
			{
				$root = $oWikiModel->getRootDocument($this->module_info->module_srl);
				if(!is_null($root))
				{
					$document_srl = $root->document_srl;
					$entry = $oDocumentModel->getAlias($document_srl);
					Context::set('entry', $entry);
				}
				else
				{
					$visitingAnEmptyWiki = TRUE;
				}
			}
			$document_srl = $oDocumentModel->getDocumentSrlByAlias($this->module_info->mid, $entry);
			if(!$document_srl)
			{
				$document_srl = $oDocumentModel->getDocumentSrlByTitle($this->module_info->module_srl, $entry);
			}
		}
		else if(!$entry)
		{
			$entry = $oDocumentModel->getAlias($document_srl);
			Context::set('entry', $entry);
		}

		/*
		 * Check if exists document_srl for requested document
		 */
		if($document_srl)
		{
			$oDocument = $oDocumentModel->getDocument($document_srl);
			if($oDocument->isExists())
			{
				// Load next and previous documents before parsing content, otherwise extra_vars are overriden
				list($prev_document_srl, $next_document_srl) = $oWikiModel->getPrevNextDocument($this->module_srl, $document_srl);
				if($prev_document_srl)
				{
					Context::set('oDocumentPrev', $oDocumentModel->getDocument($prev_document_srl));
				}
				if($next_document_srl)
				{
					Context::set('oDocumentNext', $oDocumentModel->getDocument($next_document_srl));
				}
				$this->addToVisitLog($entry);

				$this->_handleWithExistingDocument($oDocument);
			}
			else
			{
				Context::set('document_srl', '', TRUE);
				return new Object(-1, 'msg_not_founded');
			}
		} // generate an empty document object if you do not have a document_srl for requested document
		else
		{
			$oDocument = $oDocumentModel->getDocument(0);
		}
		// View posts by checking permissions or display an error message if you do not have permission

		if($oDocument->isExists())
		{
			// add page title to the browser title
			Context::addBrowserTitle($oDocument->getTitleText());

			// Increase in hits (if document has permissions)
			if(!$oDocument->isSecret() || $oDocument->isGranted())
			{
				$oDocument->updateReadedCount();
			}

			// Not showing the content if it is secret
			if($oDocument->isSecret() && !$oDocument->isGranted())
			{
				$oDocument->add('content', Context::getLang('thisissecret'));
			}
			$this->setTemplateFile('document_view');
			// set contributors

			if($this->use_history)
			{
				$contributors = $oWikiModel->getContributors($oDocument->document_srl);
				Context::set('contributors', $contributors);
			}

			// If the document has no rights set for comments is forced to use
			if($this->module_info->use_comment != 'N')
			{
				$oDocument->add('allow_comment', 'Y');
			}

			// Set up alias
			$alias = $oDocumentModel->getAlias($oDocument->document_srl);
			$oDocument->add('alias', $alias);
			// Put root element in context
			$root = $oWikiModel->getRootDocument($this->module_info->module_srl);
			Context::set('root', $root);
		}
		else
		{
			// Document was not found
			//	 If user is for the first time on this wiki and there is no document in the entire site
			//   show a friendly message

			if($visitingAnEmptyWiki)
			{
				$title = Context::getLang('create_first_page_title');
				if($this->module_info->markup_type == 'markdown') {
					$content = Context::getLang('create_first_page_markdown_help');
					$wiki_parser = $this->getWikiTextParser();
					$content = $wiki_parser->parse($content);
				}
				else {
					$content = Context::getLang('create_first_page_description');
                }

				$oDocument->add('title', $title);
				$alias = $this->beautifyEntryName($title);
				$oDocument->add('alias', $alias);
				$oDocument->add('content', $content);
			}
			//  Otherwise, show the usual message
			else
			{
				$content = Context::getLang('not_exist');
				$oDocument->add('title', $entry);
				$alias = $this->beautifyEntryName($entry);
				$oDocument->add('alias', $alias);
				$oDocument->add('content', $content);
			}
			$this->setTemplateFile('document_not_found');
		}

		Context::set('visit_log', $_SESSION['wiki_visit_log'][$this->module_info->module_srl]);
		// Setting a value oDocument for being use in skins
		Context::set('oDocument', $oDocument);
		Context::set('entry', $alias);

		// get translated language for current document
		$translatedlangs = $oDocument->getTranslationLangCodes();
		$arr_translation_langs = array();
		foreach($translatedlangs as $langs)
		{
			$arr_translation_langs[] = $langs->lang_code;
		}
		Context::set("translatedlangs", $arr_translation_langs);

		$this->getBreadCrumbs((int)$oDocument->document_srl);

		// Redirect to user friendly URL if request comes from Search
		$error_return_url = Context::get('error_return_url');
		if(isset($error_return_url))
		{
			$site_module_info = Context::get('site_module_info');
			if($document_srl)
			{
				$url = getSiteUrl($site_module_info->document, '', 'mid', $this->module_info->mid, 'entry', $oDocument->get('alias'), 'document_srl', '');
			}
			else
			{
				$url = getSiteUrl($site_module_info->document, '', 'mid', $this->module_info->mid, 'entry', $entry, 'document_srl', '');
			}
			$this->setRedirectUrl($url);
		}
		return new Object(0, 'success');
	}

	/**
	 * @brief Display screen for posting a comment
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	 */
	function dispWikiReplyComment()
	{
		// Check permission
		if(!$this->grant->write_comment)
		{
			return $this->dispWikiMessage('msg_not_permitted');
		}

		// Produces a list of variables needed to implement
		$parent_srl = Context::get('comment_srl');

		// Return message error if there is no parent_srl
		if(!$parent_srl)
		{
			return new Object(-1, 'msg_invalid_request');
		}
		// Look for the comment
		$oCommentModel = &getModel('comment');
		$oSourceComment = $oCommentModel->getComment($parent_srl, $this->grant->manager);

		// If there is no reply error
		if(!$oSourceComment->isExists())
		{
			return $this->dispWikiMessage('msg_invalid_request');
		}
		if(Context::get('document_srl') && $oSourceComment->get('document_srl') != Context::get('document_srl'))
		{
			return $this->dispWikiMessage('msg_invalid_request');
		}
		// Generate the target comment
		$oComment = $oCommentModel->getComment();
		$oComment->add('parent_srl', $parent_srl);
		$oComment->add('document_srl', $oSourceComment->get('document_srl'));

		// Set the necessary informations
		Context::set('oSourceComment', $oSourceComment);
		Context::set('oComment', $oComment);
		$this->setTemplateFile('comment_edit');

		return new Object(0, 'success');
	}

	/**
	 * @brief Modify comment page
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	 */
	function dispWikiModifyComment()
	{
		// Check persmission
		if(!$this->grant->write_comment)
		{
			return $this->dispWikiMessage('msg_not_permitted');
		}

		// Produce a list of variables needed to implement
		$document_srl = Context::get('document_srl');
		$comment_srl = Context::get('comment_srl');

		// If you do not have error for specified comment
		if(!$comment_srl)
		{
			return new Object(-1, 'msg_invalid_request');
		}
		// Look for the comment
		$oCommentModel = &getModel('comment');
		$oComment = $oCommentModel->getComment($comment_srl, $this->grant->manager);
		// If there is no reply error
		if(!$oComment->isExists())
		{
			return $this->dispWikiMessage('msg_invalid_request');
		}
		// If the article does not have permission then display the password input screen

		if(!$oComment->isGranted())
		{
			return $this->setTemplateFile('input_password_form');
		}
		// Set the necessary informations
		Context::set('oSourceComment', $oCommentModel->getComment());
		Context::set('oComment', $oComment);

		$this->setTemplateFile('comment_edit');

		return new Object(0, 'success');
	}

	/**
	 * @brief Delete comment form
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	 */
	function dispWikiDeleteComment()
	{
		// Check permission
		if(!$this->grant->write_comment)
		{
			return $this->dispWikiMessage('msg_not_permitted');
		}

		// Produce a list of variables needed to implement
		$comment_srl = Context::get('comment_srl');

		// If you do not have error for specified comment
		if($comment_srl)
		{
			$oCommentModel = &getModel('comment');
			$oComment = $oCommentModel->getComment($comment_srl, $this->grant->manager);
		}

		// If there is no reply error
		if(!$oComment->isExists())
		{
			return $this->dispWikiContent();
		}

		// If the article does not have permission then display the password input screen
		if(!$oComment->isGranted())
		{
			return $this->setTemplateFile('input_password_form');
		}

		Context::set('oComment', $oComment);
		$this->setTemplateFile('delete_comment_form');

		return new Object(0, 'success');
	}

	/**
	 * @brief Add comment view - used for loading comment form via ajax
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access public
	 * @return Object
	 */
	function dispCommentEditor()
	{
		// allow only logged in users to comment.
		if(!Context::get('is_logged'))
		{
			return new Object(-1, 'login_to_comment');
		}
		$document_srl = Context::get('document_srl');

		$oDocumentModel = &getModel("document");
		$oDocument = $oDocumentModel->getDocument($document_srl);
		if(!$oDocument->isExists())
		{
			return new Object(-1, 'msg_invalid_request');
		}
		if(!$oDocument->allowComment())
		{
			return new Object(-1, 'comments_disabled');
		}
		Context::set('oDocument', $oDocument);
		$oModuleModel = &getModel('module');
		$module_info = $oModuleModel->getModuleInfoByModuleSrl($oDocument->get('module_srl'));
		Context::set("module_info", $module_info);

		$module_path = './modules/' . $module_info->module . '/';
		$skin_path = $module_path . 'skins/' . $module_info->skin . '/';
		if(!$module_info->skin || !is_dir($skin_path))
		{
			$skin_path = $module_path . 'skins/xe_wiki/';
		}
		$oTemplateHandler = TemplateHandler::getInstance();
		$this->add('html', $oTemplateHandler->compile($skin_path, 'comment_edit.html'));

		return new Object(0, 'success');
	}

	/**
	 * @brief Perpares document for display and loads any extra data needed
	 * @developer NHN (developers@xpressengine.com)
	 * @access private
	 * @param $oDocument
	 * @return void
	 */
	function _handleWithExistingDocument(&$oDocument)
	{
		// Display error message if it is a different module than requested module
		if($oDocument->get('module_srl') != $this->module_info->module_srl)
		{
			$this->stop('msg_invalid_request');
			return;
		}

		// Check if you have administrative authority to grant
		if($this->grant->manager)
		{
			$oDocument->setGrant();
		}

		// Check if history is enable and get Document history otherwise ignore it
		$history_srl = Context::get('history_srl');
		if($history_srl)
		{
			$oDocumentModel = & getModel('document'); $output = $oDocumentModel->getHistory($history_srl);
			if($output && $output->content != NULL)
			{
				Context::set('history', $output);
			}
		}

		$content = $oDocument->getContent(FALSE, FALSE, FALSE, FALSE);
		$content = $this->_renderWikiContent($oDocument->document_srl, $content);
		// Retrieve documents that link here and that this doc links to
		$oWikiModel = &getModel('wiki');
		$inbound_links = $oWikiModel->getInboundLinks($oDocument->document_srl);
		$outbound_links = $oWikiModel->getOutboundLinks($oDocument->document_srl);

		$inbound_links_html = '';
		foreach($inbound_links as $link)
		{
			$inbound_links_html .= '<p><a href="' . getUrl('mid', $this->module_info->mid, 'entry', $link->alias, 'document_srl', '') . '">' . $link->title . '</a> </p>';
		}
		if($inbound_links_html != '')
		{
			$inbound_links_html = '<div id="wikiInboundLinks" class="wikiLinks"><p><strong>' . Context::getLang('pages_that_link_to_this') . '</strong></p>' . $inbound_links_html . '</div>';
		}

		$outbound_links_html = '';
		foreach($outbound_links as $link)
		{
			$outbound_links_html .= '<p><a href="' . getUrl('mid', $this->module_info->mid, 'entry', $link->alias, 'document_srl', '') . '">' . $link->title . '</a></p>';
		}
		if($outbound_links_html != '')
		{
			$outbound_links_html = '<div id="wikiOutboundLinks" class="wikiLinks"><p><strong>' . Context::getLang('links_in_this_page') . '</strong></p>' . $outbound_links_html . '</div>';
		}

		$content .= $inbound_links_html . $outbound_links_html;

		$oDocument->add('content', $content);
	}

	/**
	 * @brief Parse wiki document syntax
	 * @developer NHN (developers@xpressengine.com)
	 * @access private
	 * @param $document_srl
	 * @param $org_content original content
	 * @return string $parsed_content
	*/
	function _renderWikiContent($document_srl, $org_content)
	{
		$oCacheHandler = & CacheHandler::getInstance('object', NULL, TRUE);
		if($oCacheHandler->isSupport())
		{
			$object_key = sprintf('%s.%s.php', $document_srl, Context::getLangType());
			$cache_key = $oCacheHandler->getGroupKey('wikiContent', $object_key);
			$content = $oCacheHandler->get($cache_key);
		}
        //disable cache here: (true || ...)
		if(true || !$content)
		{
			$wiki_syntax_parser = $this->getWikiTextParser();
			$content = $wiki_syntax_parser->parse($org_content);
			if($oCacheHandler->isSupport())
			{
				$oCacheHandler->put($cache_key, $content);
			}
		}
		return $content;
	}

	/**
	 * @brief Set list for Tree menu on left side of pages
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access private
	 * @param $module_srl
	 * @param $document_srl
	 * @return void
	 */
	function _loadSidebarTreeMenu($module_srl, $document_srl)
	{
		if($document_srl)
		{
			$oWikiModel = &getModel('wiki');
			$this->list = $oWikiModel->getMenuTree($module_srl, $document_srl, $this->module_info->mid);
		}
		Context::set("list", $this->list);
		
		$security = new Security();
		$security->encodeHTML('list..title');
	}

	/**
	 * @brief Generate Left menu according with settings from admin panel
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access private
	 * @return void
	 */
	function getLeftMenu()
	{
		$oWikiModel = &getModel("wiki");
		$oDocumentModel = &getModel("document");
		$module_srl = $this->module_info->module_srl;
		// We need to retrieve skin info directly from module model
		// because it wasn't yet synced with module_info (this method executes on init)
		$oModuleModel = &getModel('module');
		$skin_vars = $oModuleModel->getModuleSkinVars($module_srl);
		if($skin_vars["menu_style"]->value == "classic")
		{
			$this->list = $oWikiModel->loadWikiTreeList($module_srl);
			Context::set('list', $this->list);
		}
		else
		{
			$document_srl = Context::get("document_srl");
			$entry = Context::get("entry");
			$root = $oWikiModel->getRootDocument($module_srl);
			if(!$document_srl)
			{
				if(!$entry)
				{
					$document_srl = $root->document_srl;
					$entry = $oDocumentModel->getAlias($document_srl);
				}
				else
				{
					if(is_null($oDocumentModel->getDocumentSrlByTitle($this->module_info->module_srl, $entry)))
					{
						$document_srl = $root->document_srl;
						$this->_loadSidebarTreeMenu($module_srl, $document_srl);
					}
					else
					{
						$this->_loadSidebarTreeMenu($module_srl, $oDocumentModel->getDocumentSrlByAlias($module_srl, $entry));
					}
				}
				$document_srl = $oDocumentModel->getDocumentSrlByAlias($this->module_info->mid, $entry);
				$this->_loadSidebarTreeMenu($module_srl, $document_srl);
			}
			else
			{
				$this->_loadSidebarTreeMenu($module_srl, $document_srl);
			}
		}
	}

	/**
	 * @brief Generate breadcrumbs
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access private
	 * @param $document_srl
	 * @return void
	 */
	function getBreadCrumbs($document_srl)
	{
		// get Breadcrumbs menu
		$oWikiModel = & getModel("wiki");
		$menu_breadcrumbs = $oWikiModel->getBreadcrumbs($document_srl, $this->list);
		Context::set('breadcrumbs', $menu_breadcrumbs);
	}

	/**
	 * @brief View for displaying search results
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access public
	 * @return Object
	*/
	function dispWikiSearchResults()
	{
		$oWikiModel = &getModel('wiki');
		$oDocumentModel = &getModel('document');
		$oModuleModel = &getModel('module');

		$moduleList = $oWikiModel->getModuleList(TRUE);
		$moduleList = $this->_sortArrayByKeyDesc($moduleList, 'search_rank');
		Context::set('module_list', $moduleList);
		$target_mid = $this->module_info->module_srl;
		$is_keyword = Context::get("search_keyword");
		$this->_searchKeyword($target_mid, $is_keyword);

		$this->setTemplateFile('document_search');

		return new Object(0, 'success');
	}

	/**
	 * @brief Sorts array descending by key
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access private
	 * @param $object_array
	 * @param $key
	 * @return array
	 *
	 * TODO See if can be removed and replaced with a query
	 */
	function _sortArrayByKeyDesc($object_array, $key)
	{
		$key_array = array();
		if($object_array)
		{
			foreach($object_array as $obj)
			{
				$key_array[$obj->{$key}] = $obj;
			}
		}
		krsort($key_array);
		$result = array();
		foreach($key_array as $rank => $obj)
		{
			$result[] = $obj;
		}
		return $result;
	}

	/**
	 * @brief Adds info to document - user friendly url and others
	 * for pretty displaying in search results
	 * @access private
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @param $oModuleModel
	 * @param $oDocumentModel
	 * @param $doc
	 * @return stdClass
	 *
	 * TODO See if it can be replaced / removed
	 */
	function _resolveDocumentDetails($oModuleModel, $oDocumentModel, $doc)
	{
		$entry = $oDocumentModel->getAlias($doc->document_srl);
		$module_info = $oModuleModel->getModuleInfoByDocumentSrl($doc->document_srl);
		$doc->browser_title = $module_info->browser_title;
		$doc->mid = $module_info->mid;
		if(isset($entry))
		{
			$doc->entry = $entry;
		}
		else
		{
			$doc->entry = "bugbug";
		}
		return $doc;
	}

	/**
	 * @brief Helper method for search
	 * @developer Bogdan Bajanica (xe_dev@arnia.ro)
	 * @access private
	 * @param $target_mid
	 * @param $is_keyword
	 * @return $output
	*/
	function _searchKeyword($target_mid, $is_keyword)
	{
		$page = Context::get('page');
		if(!isset($page))
		{
			$page = 1;
		}
		$search_target = Context::get('search_target');
		if(isset($search_target))
		{
			if($search_target == 'tag')
			{
				$search_target = 'tags';
			}
		}
		$oWikiModel = &getModel('wiki');
		$oModuleModel = &getModel('module');
		$oDocumentModel = &getModel('document');

		$output = $oWikiModel->search($is_keyword, $target_mid, $search_target, $page, 10);
		if($output->data)
		{
			$data = $output->data;
			foreach($output->data as $key => $value)
			{
				$doc = $this->_resolveDocumentDetails($oModuleModel, $oDocumentModel, $value);
				$data[$key] = $doc;
			}
		}
		Context::set('document_list', $data);
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $page);
		Context::set('page_navigation', $output->page_navigation);

		return $output;
	}

	 /**
	  * @brief Returns a list of document statuses
	  * @developer NHN (developers@xpressengine.com)
	  * @access private
	  * @return array
	  */
	function _getStatusNameList()
	{
		$oDocumentModel = &getModel('document');
		$resultList = array();
		if(!empty($this->module_info->use_status))
		{
			$statusNameList = $oDocumentModel->getStatusNameList();
			$statusList = explode('|@|', $this->module_info->use_status);

			if(is_array($statusList))
			{
				foreach($statusList AS $key=>$value)
				{
					$resultList[$value] = $statusNameList[$value];
				}
			}
		}
		return $resultList;
	}
}
/* End of file wiki.view.php */
/* Location: wiki.view.php */
