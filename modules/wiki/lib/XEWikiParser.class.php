<?php

/* require_once ('SyntaxParser.interface.php');  // Commented for backwards compatibility with PHP4 */

require_once ('WikiText.class.php');

/**
 * @brief Syntax parser for XE Wiki
 * @developer Corina Udrescu (xe_dev@arnia.ro)
 *
 * Contains the old parsing code of Wiki (before Markdown was made default)
 * Can only be ran in the context of an XE request (requires wiki class, ModuleObject class and Context)
 */
class XEWikiParser /* implements SyntaxParser // Commented for backwards compatibility with PHP4  */
{
	var $wiki_site = NULL;
	var $internal_links_regex = "!\[([^\]]+)\]!is";

	/**
	 * @brief Constructor
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $wiki_site WikiSite
	 * @return
	 */
	function __construct($wiki_site = NULL)
	{
		$this->wiki_site = $wiki_site;
	}

	/**
	 * @brief Converts XE Markup (explicit links - http / https - and internal links - given as [doc_name | description] to HTML
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $org_content string
	 * @return string
	 */
	function parse($org_content)
	{
        $parser = new WTParser($org_content, 'xewiki', $this->wiki_site);
        $org_content = $parser->toString(true);

		// Replace square brackets with link
		$content = preg_replace_callback($this->internal_links_regex, array(&$this, 'callback_wikilink'), $org_content);
		// No idea what this does :)
		$content = preg_replace('@<([^>]*)(src|href)="((?!https?://)[^"]*)"([^>]*)>@i', '<$1$2="' . Context::getRequestUri() . '$3"$4>', $content);
		return $content;
	}

	/**
	 * @brief Returns an array of aliases for documents that are being linked in text
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $text string
	 * @return string
	 */
	function getLinkedDocuments($text)
	{
		$matches = array();
		$aliases = array();
		preg_match_all($this->internal_links_regex, $text, $matches, PREG_SET_ORDER);
		foreach($matches as $match)
		{
			$entry_name = $this->makeEntryName($match);
			if($entry_name->exists && !in_array($entry_name->link_entry, $aliases))
			{
					$aliases[] = $entry_name->link_entry;
			}
		}
		return $aliases;
	}

	/**
	 * @brief Generates the string to use as entry name
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array
	 * @return stdClass
	 */
	function makeEntryName($matches)
	{
		// At first, we assume link does not have description
		$answer->is_alias_link = FALSE;
		$matches[0] = trim($matches[0]);
		$names = explode('|', $matches[1]);
		$page_name = trim($names[0]);
		$link_description = trim($names[1]);

		if($link_description)
		{
			$answer->is_alias_link = TRUE;
			$answer->printing_name = $link_description;
		}
		else
		{
			$answer->printing_name = $page_name;
		}
		$alias = $this->wiki_site->documentExists($page_name);
		if($alias)
		{
			$answer->link_entry = $alias;
			$answer->exists = TRUE;
		}
		else
		{
			$answer->link_entry = $page_name;
		}
		return $answer;
	}

	/**
	 * @brief The return link to be substituted according to wiki
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array
	 * @return string
	 */
	function callback_wikilink($matches)
	{
		if($matches[1]{0} == "!")
		{
			return "[" . substr($matches[1], 1) . "]";
		}

		$entry_name = $this->makeEntryName($matches);
		if($entry_name->exists)
		{
			$cssClass = 'exist';
		}
		else
		{
			$cssClass = 'notexist';
		}
		$url = $this->wiki_site->getFullLink($entry_name->link_entry);
		$answer = "<a href=\"$url\" class=\"" . $cssClass . "\" >" . $entry_name->printing_name . "</a>";
		return $answer;
	}
}
/* End of file XEWikiParser.class.php */
/* Location: XEWikiParser.class.php */
