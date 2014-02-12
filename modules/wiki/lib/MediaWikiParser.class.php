<?php
/* require_once ('SyntaxParser.interface.php'); // Commented for backwards compatibility with PHP4 */
require_once ('ParserBase.class.php');
require_once ('WikiText.class.php');

/**
 * @brief Converts a limited subset of MediaWiki syntax into HTML
 * @developer Corina Udrescu (xe_dev@arnia.ro)
 */
class MediaWikiParser extends ParserBase
{
	var $typeface_symbols = array("italic" => "\'\'"
									, "bold" => "\'\'\'"
									, "inline_code" => "`"
									, "multiline_code_open" => "[\n][ ][<]nowiki[>]"
									, "multiline_code_close" => "[<][\/]nowiki[>]"
									, "superscript" => '\^'
									, "subscript" => ',,'
									, "strikeout" => '~~');

	var $internal_links_regex = "/
									[[][[]				# Starts with [[
									(([^#|]+?)[:])?		# Can start with something that ends in : [matches 1,2]
									([^#|]+?)?		# Followed by any word	[matches 3]
									([#](.*?))?		# Followed by an optional group that starts with # [matches 4,5]
									([|](.*?))?		# Followed by an optional group that starts with a pipe [matches 6,7]
									[]][]]				# Ends with ]]
									([^ \n]*)?		# Optional tail for brackets - take all characters until first space or newline  [matches 8]
								/x";

	/**
	 * @brief Constructor
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $wiki_site WikiSite
	 * @return
	 */
	function __construct($wiki_site)
	{
		parent::__construct($wiki_site);
	}

	/**
	 * @brief Overrides parseText in base
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
     * @param bool $toc do not display toc and edit links if in preview mode (ajax)
     * @override
	 * @access protected
	 * @return
	 */
    function parseText($toc=true)
	{
        parent::parseText();
        $parser = new WTParser($this->text);
        $this->text = $parser->toString($toc, $toc ? $this->wiki_site->getEditPageUrlForCurrentDocument() : null);
		$this->parseDefinitionLists();
		$this->parsePreformattedText();
    }

    /**
	 * @brief Returns a list of all documents this page links to, given by alias
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $text string
	 * @return array()
	 */
	function getLinkedDocuments($text)
	{
		$matches = array();
		$aliases = array();
		preg_match_all($this->internal_links_regex, $text, $matches, PREG_SET_ORDER);
		foreach($matches as $match)
		{
			$content = $match[3]; // Page name or external url
			// If external URL, continue

			if(preg_match("/^(https?|ftp|file)/", $content))
			{
					continue;
			}
			$alias = $this->wiki_site->documentExists($content);
			if($alias && !in_array($alias, $aliases))
			{
					$aliases[] = $alias;
			}
		}
		return $aliases;
	}

	/**
	 * @brief Removes all blocks that need to be escaped from original text
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 *
	 * Blocks are saved in a private member until parsing is done,
	 * and after that they are inserted back into the content.
	 */
	function escapeWhateverThereIsToEscape()
	{
		// Escape text between <nowiki> and </nowiki>
		$this->text = preg_replace_callback("~
												[<]nowiki[>]
												(.*)
												[<][/]nowiki[>]
											~x", array($this, "_escapeBlock"), $this->text);
		// Escape text right after <nowiki/>
		$this->text = preg_replace_callback("~
												[<]nowiki[ ]?[/][>]
												([^ ]*)
											~x", array($this, "_escapeBlock"), $this->text);
	}

	/**
	 * @brief Callback function for escapes
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array()
	 * @return string
	 */
	function _escapeBlock(&$matches)
	{
		$this->batch_count++;
		$replacement = "%%%" . $this->batch_count . "%%%";
		$this->escaped_blocks[$replacement] = $matches[1];
		return $replacement;
	}

	/**
	 * @brief Handles list like content
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseLists()
	{
		// Ordered list
		// Find all ordered list blocks - at most 4 levels deep - and surround them with <ol></ol>
		// First pass, finding all blocks
		$this->text = preg_replace("/						# This parses all list blocks (includes all LIs)
									[\n\r]					# Newline
									[#]						# That start with #
									.+						# Followed by any characters (at least one)
									([\n|\r][#].+)+			# Then repeat at least once
									/x", "\n<ol>$0\n</ol>", $this->text);
		// List item with sub items of 2 or more
		$this->text = preg_replace("/[\n\r]#(?!#) *(.+)(([\n\r]#{2,}.+)+)/", "\n<li>$1\n<ol>$2\n</ol>\n</li>", $this->text);
		// List item with sub items of 3 or more
		$this->text = preg_replace("/[\n\r]#{2}(?!#) *(.+)(([\n\r]#{3,}.+)+)/", "\n<li>$1\n<ol>$2\n</ol>\n</li>", $this->text);
		// List item with sub items of 4 or more
		$this->text = preg_replace("/[\n\r]#{3}(?!#) *(.+)(([\n\r]#{4,}.+)+)/", "\n<li>$1\n<ol>$2\n</ol>\n</li>", $this->text);
		// Unordered list
		// First pass, finding all blocks
		$this->text = preg_replace("/[\n\r]\*.+([\n|\r]\*.+)+/", "\n<ul>$0\n</ul>", $this->text);
		// List item with sub items of 2 or more
		$this->text = preg_replace("/[\n\r]\*(?!\*) *(.+)(([\n\r]\*{2,}.+)+)/", "\n<li>$1\n<ul>$2\n</ul>\n</li>", $this->text);
		// List item with sub items of 3 or more
		$this->text = preg_replace("/[\n\r]\*{2}(?!\*) *(.+)(([\n\r]\*{3,}.+)+)/", "\n<li>$1\n<ul>$2\n</ul>\n</li>", $this->text);
		// List item with sub items of 4 or more
		$this->text = preg_replace("/[\n\r]\*{3}(?!\*) *(.+)(([\n\r]\*{4,}.+)+)/", "\n<li>$1\n<ul>$2\n</ul>\n</li>", $this->text);
		// List items
		// Wraps all list items to <li/>
		$this->text = preg_replace("/^[#\*]+ *(.+)$/m", "<li>$1</li>", $this->text); return ;
	}

	/**
	 * @brief Handles definition lists
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 *
	 * Only allow one level lists - anything else is ignored
	 */
	function parseDefinitionLists()
	{
		// Wrap block with <dl> tags
		$this->text = preg_replace("/
									[\r]?[\n]					# Newline
									[;:]						# That start with #
									.+						# Followed by any characters (at least one)
									(([\r]?[\n])[:;].+)+			# Then repeat at least once
									/x", "\n<dl>$0\n</dl>", $this->text);
		// Wrap term with <dt>
		$this->text = preg_replace("/^[;](.+)$/m", "<dt>$1</dt>", $this->text);
		// Wrap definitio with <dd>
		$this->text = preg_replace("/^[:](.+)$/m", "<dd>$1</dd>", $this->text);
	}

	/**
	 * @brief Skips indent parsing
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @override
	 * @return
	 */
	function parseIndents()
	{
	}

	/**
	 * @brief Skip blockquote parsing; indenting means Preformatted text in MediaWiki
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @override
	 * @return
	 */
	function parseQuotes()
	{
	}

	/**
	 * @brief Handle <pre> text
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parsePreformattedText()
	{
		$this->text = preg_replace("/(
							(([\n])			# Start with newline
							[ ]				# One space
							(.+)			# Any number of characters
							)+				# Repeat at least once
							)/xe", "str_replace('$3', '', '<pre>$1</pre>')", $this->text);
	}

	/**
	 * @brief Handle links
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseLinks()
	{
		// Replace external urls that just start with http, https, ftp etc.;
		// skip the ones in square brackets;
		// skip the ones that are already inside a link - start with "
		$this->text = preg_replace("~
									(?<!  				# does not start with ..
										([[\"'])        # [ followed by \" or '
									)
									((https?|ftp|file)    # starts with http, https, ftp or file
									://                   # followed by ://
									[^ <\n]*)               # followed by any number of characters except space, < and \n
									~x", "<a href=\"$2\" title=\"$2\" class=\"external\">$2</a>", $this->text);
		// Find internal links given between [[double brackets]]
		//	- can contain piped description [my link|description that can have many words]
		//	- can link to local content [my link#local_anchor|and some description maybe]
		//	- can contain namespaced page names [Help:Contents|This is the Contents page]
		$this->text = preg_replace_callback($this->internal_links_regex, array($this, "_handle_internal_link"), $this->text);
		// Find external links given between [simple brackets]
		$this->text = preg_replace_callback("/
									[[]				# Starts with [
									([^#]+?)		# Followed by any word
									([#](.*?))?		# Followed by an optional group that starts with #
									([ ](.*?))?		# Followed by an optional group that starts with a space
									[]]				# Ends with ]
								/x", array($this, "_handle_external_link"), $this->text);
	}

	/**
	 * @brief Callback function for parseLinks
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array()
	 * @return string
	 */
	function _handle_external_link(&$matches)
	{
		$url = $matches[1];
		$local_anchor = $matches[2];
		$description = $matches[5];
		$href = '"' . $url . $local_anchor . '"';
		$title = " title=\"$url\"";
		$class = " class=\"external\"";
		$description = ($description ? $description : $url);
		return "<a href=$href$title$class>$description</a>";
	}

	/**
	 * @brief Callback for call to preg_replace_callback that parses links
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array()
	 * @return string
	 *
	 * Sample input:
	 * array(9) {
	 *		[0]=> "[[Help:Main Page#See also|different text]]esses"
	 *		[1]=> "Help:"
	 *		[2]=> "Help"
	 *		[3]=> "Main Page"
	 *		[4]=> "#See also"
	 *		[5]=> "See also"
	 *		[6]=> "|different text"
	 *		[7]=> "different text"
	 *		[8]=> "esses"
	 *		}
	 *
	 */
	function _handle_internal_link(&$matches)
	{
		$namespace = $matches[2];
		$content = $matches[3]; // Page name or external url
		$local_anchor = $matches[4];
		$description = $matches[7];
		$tail = $matches[8];
		// If tail had a <nowiki /> tag before it, the words would have been removed from text by now
		// That is why we search for %%%

		if(strpos($matches[8], '%%%') === 0)
		{
			$external_tail = $tail;
			$tail = '';
		}
		// Building href
		// url#local_anchor
		$alias = $this->wiki_site->documentExists(str_replace(' ', '_', $content));
		if($alias)
		{
			$href = $alias;
		}
		else
		{
			$href = str_replace(' ', '_', $content);
		}
		$href .= str_replace(' ', '_', $local_anchor);
		// Building title attribute
		$title = $content ? " title=\"$content\"" : '';

		// Add class attribute
		$class = '';
		if(preg_match("/^(https?|ftp|file)/", $href))
		{
			$class = 'external ';
		}
		if($alias)
		{
			$class .= 'exist';
		}
		else
		{
			if($local_anchor)
			{
				$class .= 'exist';
			}
			else
			{
				$class .= 'notexist';
			}
		}
		$class = " class=\"$class\"";

		// Build description
		if(!$description)
		{
			$description = $content . $local_anchor;
		}
		if($tail)
		{
			$description .= $tail;
		}
		// If document does not exist, return plain text

		if(!$alias && !$this->wiki_site->currentUserCanCreateContent())
		{
			return $description;
		}

		return "<a href=\"$href\"$title$class>$description</a>$external_tail";
	}

	/**
	 * @brief Handle tables
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseTables()
	{
		// Table rows: |-
		// First row is default
		$this->text = preg_replace("/^				# Row starts with
										[|][-]		# |-
										/mx", '</tr><tr>', $this->text);
		// Table start: {|
		$this->text = preg_replace("/^					# Start on newline
										[\s]*			# Any number of spaces
										{[|]				# Starts with {|
										/mx", '<table><tbody><tr>', $this->text);
		// Table end: |}
		$this->text = preg_replace("/^					# Start on newline
										[\s]*			# Any number of spaces
										[|]}				# Starts with {|
										/mx", '</tr></tbody></table>', $this->text);
		// Table cells on same line: ||
		$this->text = preg_replace_callback("/^				# Row starts with
										[\s]*		# Any number of whitespace
										[|]			# |
										(.*)
										$/mx", array($this, '_handle_cell'), $this->text);
		// Table cells on new line: |
		$this->text = preg_replace("/^
										[\s]*		# Any number of whitespace
										[|]			# |
										[\s]*
										(.*)
										[\s]*
										$/mx", '<td>$1</td>', $this->text);
	}

	/**
	 * @brief Callback for parseTables
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array
	 * @return string
	 */
	function _handle_cell($matches)
	{
		$table_cells = preg_replace('/[\s]*[|][|][\s]*/', '</td><td>', $matches[0]);
		return $table_cells;
	}
}
/* End of file MediaWikiParser.class.php */
/* Location: MediaWikiParser.class.php */
