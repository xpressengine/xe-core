<?php
/* require_once ("lib/WikiSite.interface.php"); // Commented for backwards compatibility with PHP4 */

/**
* @class wiki
* @developer NHN (developers@xpressengine.com)
* @brief  wiki module high class
*/
class Wiki extends ModuleObject /* implements WikiSite // Commented for backwards compatibility with PHP4 */
{
	var $omitting_characters = array('/&/', '/\//', '/,/', '/ /'); 
	var $replacing_characters = array('', '', '', '_');
	
	/**
	 * @brief Returns current wiki instance syntax parser
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @return interface SyntaxParser
	 */
	function getWikiTextParser() 
	{
		if($this->module_info->markup_type == 'markdown') 
		{
			require_once ($this->module_path . "lib/MarkdownParser.class.php"); $wiki_syntax_parser = new MarkdownParser($this);
		}
		else 
		{
			if($this->module_info->markup_type == 'googlecode_markup') 
			{
				require_once ($this->module_path . "lib/GoogleCodeWikiParser.class.php"); $wiki_syntax_parser = new GoogleCodeWikiParser($this);
			}
			else 
			{
				if($this->module_info->markup_type == 'mediawiki_markup') 
				{
					require_once ($this->module_path . "lib/MediaWikiParser.class.php"); $wiki_syntax_parser = new MediaWikiParser($this);
				}
				else 
				{
					require_once ($this->module_path . "lib/XEWikiParser.class.php"); $wiki_syntax_parser = new XEWikiParser($this);
				}
			}
		}
		return $wiki_syntax_parser;
	}
	
	/**
	 * @brief Receives a document title and returns an URL firendly name
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @param $entry_name string
	 * @return string 
	 */
	function beautifyEntryName($entry_name) 
	{
		$entry_name = strip_tags($entry_name); 
		$entry_name = html_entity_decode($entry_name); 
		$entry_name = preg_replace($this->omitting_characters, $this->replacing_characters, $entry_name); 
		$entry_name = preg_replace('/[_]+/', '_', $entry_name); 
		$entry_name = strtolower($entry_name); 
		return $entry_name;
	}
	
	/**
	 * @brief Checks if a certain document exists
	 * Returns doc_alias if document exists or false otherwise
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $document_name string
	 * @return string
	 */
	function documentExists($document_name) 
	{
		$oDocumentModel = & getModel('document');
		// Search for document by alias
		$document_srl = $oDocumentModel->getDocumentSrlByAlias($this->module_info->mid, $document_name);
		if($document_srl)
		{
			return $document_name;
		}
		
		// If not found, search by title
		$document_srl = $oDocumentModel->getDocumentSrlByTitle($this->module_info->module_srl, $document_name);
		if($document_srl) 
		{
			$alias = $oDocumentModel->getAlias($document_srl); return $alias;
		}
		return FALSE;
	}
	
	/**
	 * @brief Checks if current user has permission to add new documents
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @return boolean
	 */
	function currentUserCanCreateContent() 
	{
		return $this->grant->write_document;
	}
	
	/**
	 * @brief Returns qualified internal link, given an alias or doc title
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $document_name string
	 * @return string
	 */
	function getFullLink($document_name) 
	{
		return getUrl('', 'mid', $this->module_info->mid, 'entry', $document_name, 'document_srl', '');
	}

	/**
     * @brief Returns qualified internal link, given an alias or doc title
     * @developer Florin Ercus (xe_dev@arnia.ro)
     * @access public
     * @param $document_name string
     * @return string
     * @TODO: check case when document is accessed just by document_srl
     */
	function getEditPageUrlForCurrentDocument($section=null)
    {
        if (is_null($section)) return getUrl('', 'mid', $this->module_info->mid, 'entry', Context::get('entry'), 'act', 'dispWikiEditPage');
        return getUrl('', 'mid', $this->module_info->mid, 'entry', Context::get('entry'), 'act', 'dispWikiEditPage', 'section', $section);
    }

	/**
	 * @brief Creates tables, indexes and adds any other logic needed for module upon installation
	 * @access public
	 * @developer NHN (developers@xpressengine.com)
	 * @return Object 
	 */
	function moduleInstall() 
	{
		return new Object();
	}
	
	/**
	 * @brief Checks if module is up to date	 
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return boolean
	 */
	function checkUpdate() 
	{
		$oModuleModel = &getModel('module');
		$flag = FALSE; $flag = $this->_hasOldStyleAliases(); $oDB = DB::getInstance();
		if(!$oDB->isIndexExists("wiki_links", "idx_link_doc_cur_doc"))
		{
			$flag = TRUE; 
			return $flag;
		}

		if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'wiki', 'model', 'triggerModuleListInSitemap', 'after')) return true;
	}
	
	/**
	 * @brief Updates module
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	 */
	function moduleUpdate() 
	{
		$oModuleModel = &getModel('module');
		$oModuleController = &getController('module');

		if($this->_hasOldStyleAliases())
		{
			$this->_updateOldStyleAliases();
		}
		
		// tag in the index column of the table tag
		$oDB = DB::getInstance();
		if(!$oDB->isIndexExists("wiki_links", "idx_link_doc_cur_doc")) 
		{
			$oDB->addIndex("wiki_links", "idx_link_doc_cur_doc", array("link_doc_srl", "cur_doc_srl")); 			
		}

		// 2012. 03. 21 when add new menu in sitemap, custom menu add
		if(!$oModuleModel->getTrigger('menu.getModuleListInSitemap', 'wiki', 'model', 'triggerModuleListInSitemap', 'after'))
		{
			$oModuleController->insertTrigger('menu.getModuleListInSitemap', 'wiki', 'model', 'triggerModuleListInSitemap', 'after');
		}
		
		return new Object(0, 'success_updated');
	}
	
	/**
	 * @brief Uninstalls module	 
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	 */
	function moduleUninstall() 
	{
		return new Object();
	}
	
	/**
	 * @brief Deletes cache
	 * @developer NHN (developers@xpressengine.com)
	 * @access public
	 * @return Object
	 */
	function recompileCache() 
	{
		$oCacheHandler = & CacheHandler::getInstance('object', NULL, TRUE);
		if($oCacheHandler->isSupport()) 
		{
			$oCacheHandler->invalidateGroupKey("wikiContent");
		}
		return new Object();
	}
	
	/**
	 * @brief Make sure that alias does not contain special characters / spaces, etc
	 * @developer NHN (developers@xpressengine.com)
	 * @access private
	 * @return boolean
	 */
	function _hasOldStyleAliases() 
	{
		// Get all Wiki module_srl.
		$output = executeQueryArray('wiki.getAllWikiList', NULL); $wiki_srls = array();
		if(count($output->data)) 
		{
			foreach($output->data as $key => $module_instance) 
			{
				$wiki_srls[] = $module_instance->module_srl;
			}
		}
		$args = new stdClass();
		$args->wiki_srls = $wiki_srls; $output = executeQueryArray('wiki.checkOldStyleAliases', $args);
		if(count($output->data)) 
		{
			$omitting_characters = array('&', '//', ',', ' ');
			foreach($output->data as $key => $doc_alias) 
			{
				//if($doc_alias->alias_title == 'Front Page') continue;
				
				foreach($omitting_characters as $key => $char) 
				{
					if(strpos($doc_alias->alias_title, $char)) 
					{
						return TRUE;
					}
				}
			}
		}
		return FALSE;
	}
	
	/**
	 * @brief Fixes alias in a batch - special characters / spaces that have not been removed
	 * @developer NHN (developers@xpressengine.com)
	 * @access private
	 * @return 
	 */
	function _updateOldStyleAliases() 
	{
		// Get all Wiki module_srl
		$output = executeQueryArray('wiki.getAllWikiList', NULL); $wiki_srls = array();
		if(count($output->data)) 
		{
			foreach($output->data as $key => $module_instance) 
			{
				$wiki_srls[] = $module_instance->module_srl;
			}
		}
		$args->wiki_srls = $wiki_srls; $output = executeQueryArray('wiki.checkOldStyleAliases', $args);
		if(count($output->data)) 
		{
			foreach($output->data as $key => $doc_alias) 
			{
				$omitting_characters = array('&', '//', ',', ' ');
				//if($doc_alias->alias_title == 'Front Page') continue;
				
				foreach($omitting_characters as $key => $char) 
				{
					if(strpos($doc_alias->alias_title, $char)) 
					{
						unset($args); $args->alias_srl = $doc_alias->alias_srl; $args->alias_title = wiki::beautifyEntryName($doc_alias->alias_title); $output = executeQuery('wiki.updateDocumentAlias', $args);
					}
				}
			}
		}
	}
}
/* End of file wiki.class.php */
/* Location: wiki.class.php */
