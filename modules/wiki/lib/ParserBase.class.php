<?php
/* require_once ('SyntaxParser.interface.php'); // Commented for backwards compatibility with PHP4

/**
 * @brief Base class for syntax parsers
 * @developer Corina Udrescu (xe_dev@arnia.ro)
 *
 * Implements default functionality, that is very similar among
 * GoogleCode and MediaWiki
 *
 * Main tags and style for ParserBase were taken from Google Code
 */
class ParserBase /* implements SyntaxParser // Commented for backwards compatibility with PHP4 */
{
	// Number of blocks of text that will skip parsing;
	// By temporarily saving them in this array, we can later insert them back in inital string
	var $escaped_blocks;
	// Number of text blocks injected back in initial text (after all other parsing is done)
	var $replaced_escaped_blocks;
	// Number of code block batches replaced
	// Batch 1: all single line {{{ text }}}
	// Batch 2: all multiline {{{ text }}} etc.
	var $batch_count;
	// Text being parsed
	var $text;

	var $typeface_symbols = array("italic" => "_", "bold" => "\*"
									, "inline_code" => "`"
									, "multiline_code_open" => "{{{"
									, "multiline_code_close" => "}}}"
									, "superscript" => '\^'
									, "subscript" => ',,'
									, "strikeout" => '~~');

	var $internal_link_camel_case_regex = "/
								(
								(?<!			# Doesn't begin with ..
									(
									[!]			#   .. a ! (these need to be escaped)
									|			#   or
									[[]			#   .. a [ (these will be treated later)
									)
								)
								(				# Sequence of letters that ..
									[A-Z]	    # Start with an uppercase letter
									[a-z0-9]+	# Followed by at least one lowercase letter
								){2,}			# Repeated at least two times
								)
								/x";

	var $internal_link_with_brackets_regex = "/
									[[]				# Starts with [
									([^#]+?)		# Followed by any word
									([#](.*?))?		# Followed by an optional group that starts with #
									([ ](.*?))?		# Followed by an optional group that starts with a space
									[]]				# Ends with ]
								/x";

	// Keeps a reference to a wiki instance, used to check if documents exist and such
	var $wiki_site = NULL;

	/**
	 * @brief Constructor
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $wiki_site WikiSite
	 * @return
	 */
	function __construct($wiki_site = NULL)
	{
		$this->escaped_blocks = array();
		$this->replaced_escaped_blocks = 0;
		$this->batch_count = 0;
		$this->text = '';
		$this->wiki_site = $wiki_site;
	}

	/**
	 * @brief Converts input text to HTML
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $text string
	 * @return string
	 */
	function parse($text, $toc=true)
	{
		$this->text = $text;
		if(!empty($this->text))
		{
			$this->parseInit();
			$this->parseText($toc);
			$this->parseEnd();
		}
		return $this->text;
	}

	/**
	 * @brief Handles parsing that needs to be done at the begging, like removing escaped sequences from text
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseInit()
	{
		// Convert Windows end of line (\r\n) to Linux (\n) end of line, otherwise preg_replace doesn't work
		$this->sanitize($this->text);
		$this->text = str_replace(chr(13), '', $this->text);
		$this->parseCodeBlocksAndEscapeThemFromParsing();
		$this->escapeWhateverThereIsToEscape();
	}

	/**
	 * XSS sanitizes $text
	 * @param $text
	 */
	function sanitize(&$text) {
		require_once 'htmlpurifier/library/HTMLPurifier.auto.php';
		$pPath = _XE_PATH_ . 'files/html_purifier';
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Cache.SerializerPath', $pPath);
		$purifier = new HTMLPurifier($config);
		if (!is_dir($pPath)) mkdir($pPath);
		$text = $purifier->purify($text);
	}

	/**
	 * @brief Handles all common transformations
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseText()
	{
		$this->parseBoldUnderlineAndSuch();
		$this->parsePragmas();
		$this->parseLists();
		$this->parseLinks();
		$this->parseTables();
		$this->parseQuotes();
		$this->parseHorizontalRules();
	}

	/**
	 * @brief Handles parsing that needs to be done at the end - like inserting back into text the escaped sequences
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseEnd()
	{
		$this->parseParagraphs();
		$this->putBackEscapedBlocks();
	}

	/**
	 * @brief Returns a list of all internal documents this page links to
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $text string
	 * @return array
	 */
	function getLinkedDocuments($text)
	{
		$matches = array();
		$aliases = array();

		preg_match_all($this->internal_link_camel_case_regex, $text, $matches, PREG_SET_ORDER);
		preg_match_all($this->internal_link_with_brackets_regex, $text, $matches, PREG_SET_ORDER);

		foreach($matches as $match)
		{
			$url = $match[1];
			$local_anchor = $match[2];
			$description = $match[5];
			// If external URL, continue

			if(preg_match("/^(https?|ftp|file)/", $url))
			{
				continue;
			}
			$alias = $this->wiki_site->documentExists($url);
			if($alias && !in_array($alias, $aliases))
			{
					$aliases[] = $alias;
			}
		}
		return $aliases;
	}

	/**
	 * @brief Escapes anything the syntax needs - defined for being overriden
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function escapeWhateverThereIsToEscape()
	{
	}

	/**
	 * @brief Parses a block of code
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseCodeBlocksAndEscapeThemFromParsing()
	{
		// Replace code blocks
		// We need to make sure text in code blocks is no longer parsed {{{ _italic_ }}} should skip the italic and leave text as is
		// For this, we use preg_replace_callback to save code blocks in an array that we will later inject back in the original string
		// {{{ This is some code }}}
		// $text = preg_replace("/[^`]{{{(.+?)}}}/me"    , "'<span class=\'inline_code\'>' . htmlentities('$1') . '</span>'", $text);
		$regex_find_singleline_multiline_code = "/(?<![`])" . $this->typeface_symbols["multiline_code_open"] . "(.+?)" . $this->typeface_symbols["multiline_code_close"] . "/m";
		$this->text = preg_replace_callback($regex_find_singleline_multiline_code, array($this, "_parseInlineCodeBlock"), $this->text);
		$this->text = preg_replace_callback("/
								(?<![" . $this->typeface_symbols["inline_code"] . "])" . $this->typeface_symbols["multiline_code_open"] . "	# Starts with three braces, not preceded by single line code symbol
								([\n]*)		# Followed by one or more newlines
								(.+?)		# One or more characters (including line breaks, see s modifier)
								([\n]*)		# Followed by one or more newlines
								" . $this->typeface_symbols["multiline_code_close"] . "			# And ends with another three braces
								/sx", array($this, "_parseMultilineCodeBlock"), $this->text);
		// `This is a short snippet of code`
		$regex_find_inline_code = "/" . $this->typeface_symbols["inline_code"] . "(.+?)" . $this->typeface_symbols["inline_code"] . "/m";
		$this->text = preg_replace_callback($regex_find_inline_code, array($this, "_parseInlineCodeBlock"), $this->text);
	}

	/**
	 * @brief preg_replace callback function for inline code blocks
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array
	 * @return string
	 *
	 *  - replaces wiki syntax code block with HTML span and saves it locally
	 *  - removes wiky syntax code block from initial string and replaces it with dummy text
	 *  - reference will be later injected back in string, after all other parsing is done
	 * The purpose of this function is to skip parsing text inside code blocks
	 */
	function _parseInlineCodeBlock(&$matches)
	{
		$this->batch_count++;
		$replacement = "%%%" . $this->batch_count . "%%%";
		$this->escaped_blocks[$replacement] = '<tt>' . htmlentities($matches[1], ENT_COMPAT, 'UTF-8') . '</tt>';
		return $replacement;
	}

	/**
	 * @brief preg_replace callback function for multiline code blocks
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array
	 * @return string
	 *
	 *  - replaces wiki syntax code block with HTML pre and saves it locally
	 *  - removes wiky syntax code block from initial string and replaces it with dummy text
	 *  - reference will be later injected back in string, after all other parsing is done
	 * The purpose of this function is to skip parsing text inside code blocks
	 */
	function _parseMultilineCodeBlock(&$matches)
	{
		$this->batch_count++;
		$replacement = "%%%" . $this->batch_count . "%%%";
		// $this->escaped_blocks[] = '<pre class=\'prettyprint\'>' . nl2br(htmlentities(stripslashes($matches[2]))) . '</pre>';
		$this->escaped_blocks[$replacement] = '<pre class=\'prettyprint\'>' . htmlentities(stripslashes($matches[2]), ENT_COMPAT, 'UTF-8') . '</pre>';
		return $replacement;
	}

	/**
	 * @brief Injects code blocks back into initial string
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function putBackEscapedBlocks()
	{
		for($i = 1; $i <= $this->batch_count; $i++)
		{
			$this->text = preg_replace_callback('/%%%' . $i . '%%%/', array($this, '_putBackEscapedBlock'), $this->text);
		}
	}

	/**
	 * @brief preg_replace callback function for injecting code blocks back in initial string
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array
	 * @return string;
	 */
	function _putBackEscapedBlock(&$matches)
	{
		return $this->escaped_blocks[$matches[0]];
	}

	/**
	 * @brief Parse bold, italic, underline etc.
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access proected
	 * @return
	 */
	function parseBoldUnderlineAndSuch()
	{
		// Replace bold
		// * This be bold*
		$this->text = preg_replace("~
								(?<!		# Star is not preceded
									/)		#					by a /
								" . $this->typeface_symbols["bold"] . "			# Starts with star
								(.+?)		# Any number of characters, including whitespace (see s modifier)
								" . $this->typeface_symbols["bold"] . "			# Ends with star
								(?!/)		# Star isn't followed by slash
								~x", "<strong>$1$2</strong>", $this->text); // Bold is only replaced on the same line
		// Replace italic
		// _italics_ but not in_middle_of_word
		$this->text = preg_replace("/(?<![^ \n*>])" . $this->typeface_symbols["italic"] . "(.+?)" . $this->typeface_symbols["italic"] . "/x", "<em>$1</em>", $this->text);
		// Replace ^super^script
		$this->text = preg_replace("/" . $this->typeface_symbols["superscript"] . "(.+?)" . $this->typeface_symbols["superscript"] . "/x", "<sup>$1</sup>", $this->text);
		// Replace ,,sub,,script
		$this->text = preg_replace("/" . $this->typeface_symbols["subscript"] . "(.+?)" . $this->typeface_symbols["subscript"] . "/x", "<sub>$1</sub>", $this->text);
		// Replace ~~strikeout~~
		$this->text = preg_replace("/" . $this->typeface_symbols["strikeout"] . "(.+?)" . $this->typeface_symbols["strikeout"] . "/x", "<span style='text-decoration:line-through'>$1</span>", $this->text);
	}


	/**
	 * @brief Parses pragma statements
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 *
	 * Optional pragma lines provide metadata about the page and how it should be displayed.
	 * These lines are only processed if they appear at the top of the file.
	 * Each pragma line begins with a pound-sign (#) and the pragma name, followed by a value.
	 */
	function parsePragmas()
	{
		// Replace #summary
		// #summary Summries are short descriptions of an article
		$this->text = preg_replace("/^#summary[ ]?(.*)/m", "<i>$1</i>", $this->text);
	}


	/**
	 * @brief Parses links
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 *
	 * Links
	 * 	Internal links
	 * 		- to pages that do not exist -> show up with ? after, and link to page creation form, if logged in; otherwise, leave as plain text
	 * 		- to pages that exist
	 * 		WikiWord, [Nonwikiword], [PageTitle Description], !WikiWordEscaped
	 * 		- to local anchors -> defined by h1, h2 etc TODO
	 *  Links to issues and revisions
	 *	Links to extenal pages
	 *		- anyhting that starts with http, https, ftp
	 *		- [URL description]
	 * 		- anything that starts with http, https, ftp and ends with png, gif, jpg, jpeg -> image
	 * 		- [Url ImageUrl] -> image links
	 */
	function parseLinks()
	{
		// Find internal links given as CamelCase words
		$this->text = preg_replace_callback($this->internal_link_camel_case_regex, array($this, "_handle_link"), $this->text);
		// Remove exclamation marks from CamelCase words
		$this->text = preg_replace("/(!)(([A-Z][a-z0-9]+){2,})/x", '$2', $this->text);
		// Replace image URLs with img tags
		$this->text = preg_replace("#
									(https?|ftp|file)
									://
									[^ ]*?
									(.gif|.png|.jpe?g)
									#x", "<img src=$0 />", $this->text);
		// Replace external urls that just start with http, https, ftp etc.; skip the ones in square brackets
		/*$this->text = preg_replace("#
									(?<!
										(
										[[]
										|
										[=]
										)
									)
									((https?|ftp|file)
									://
									[^ ]*)
									#x", "<a href=$2>$2</a>", $this->text);*/
        $this->text = preg_replace("#(?<!([\[=]))((https?|ftp|file)://[^\s]*)#", "<a href=\"$2\">$2</a>", $this->text);
		// Find internal links given between [brackets]
		//	- can contain description [myLink description that can have many words]
		//	- can link to local content [myLink#local_anchor and some description maybe]
		// Also catches external links
		$this->text = preg_replace_callback($this->internal_link_with_brackets_regex, array($this, "_handle_link"), $this->text);
	}


	/**
	 * @brief Callback for CamelCase and bracket links
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array
	 * @return string
	 */
	function _handle_link(&$matches)
	{
		$url = $matches[1];
		$local_anchor = $matches[2];
		$description = $matches[5];
		// If external URL, just return it as is

		if(preg_match("/^(https?|ftp|file)/", $url))
		{
			return "<a href=$url$local_anchor>" . ($description ? $description : $url) . "</a>";
		}
		// If local document that  exists, return expected link and exit
		if($alias = $this->wiki_site->documentExists($url))
		{
			$url = $this->wiki_site->getFullLink($alias);
			return "<a href=$url$local_anchor>" . ($description ? $description : $url) . "</a>";
		}
		// Else, if document does not exist
		//   If user is not allowed to create content, return plain text
		if(!$this->wiki_site->currentUserCanCreateContent())
		{
			return $description ? $description : $url;
		}
		//   Else return link to create new page
		$url = $this->wiki_site->getFullLink($description);
		return "<a href=$url class=notexist>" . ($description ? $description : $url) . "</a>";
	}


	/**
	 * @brief Replaces Wiki Syntax lists with HTML lists
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseLists()
	{
		$this->text = $this->_parseLists($this->text);
	}

	/**
	 * @brief Handles lists
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $text string
	 * @return string
	 */
	function _parseLists($text)
	{
		$lists = $this->_getLists($text);
		$offset_error = 0; // Length of final string changes during the function, so offset needs to be adjusted

		foreach($lists as $list_info)
		{
			$list = $list_info[0];
			$list_offset = $list_info[1];
			$new_list = $this->_parseList($list);
			$new_list = $this->_parseLists($new_list);
			$offset_error -= strlen($text);
			$text = substr_replace($text, $new_list, $list_offset + $offset_error, strlen($list));
			$offset_error += strlen($text);
		}
		return $text;
	}


	/**
	 * @brief Replaces a block of text containing a Wiki syntax list with an HTML list
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @param $list
	 * @return string
	 * Parses only first level list (in case we have nested lists)
	 */
	function _parseList($list)
	{
		$list = str_replace(' ', '@', $list);
		$i = 0;
		$char = substr($list, $i, 1);
		while(!in_array($char, array('*', '#')))
		{
			$i++;
			$char = substr($list, $i, 1);
		}
		if($char == '*')
		{
			$list_type = 'ul';
		}
		else
		{
			$list_type = 'ol';
		}

		$current_list_indent = substr($list, 0, $i);
		// Remove indenting for current indentation level
		$regex = '/^' . trim($current_list_indent) . '(.*)/m';
		$list = preg_replace($regex, '$1', $list);

		// Replace list items
		$regex = '/^[' . $char . ']@?(.*)/m';
		$list = preg_replace($regex, '<li>$1</li>', $list);
		$list = str_replace('@', ' ', $list);

		// Add block tags
		$list = '<' . $list_type . '>' . $list . '</' . $list_type . '>';
		return $list;
	}


	/**
	 * @brief Searches for list blocks in a string
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @param $text string
	 * @return string
	 */
	function _getLists($text)
	{
		$matches = array();
		$list_finder_regex = "/ (
						  (
						   [\r]?[\n]
						   [ ]+	# At least one space
						   [*#]	# Star or #
						   (.+)	# Any number of characters
						  )+
						)/x";
		preg_match_all($list_finder_regex, $text, $matches, PREG_OFFSET_CAPTURE);
		return $matches[0];
	}


	/**
	 * @brief Handles tables: || 1 ||  2  ||  3 ||
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseTables()
	{
		// Tables
		// || 1 ||  2  ||  3 ||
		// First pass, replace <table>
		$this->text = preg_replace("/(
								(
								\|\|			# Start with ||
								.*				# Any character except newline
								\|\|			# Finish with ||
								[\r]?[\n]?		# Followed by an optional newline
								)+				# And repeat at least one time (table rows)
							)/x", "<table border=1 cellspacing=0 cellpadding=5>\n$1\n</table>", $this->text);
		// Second pass, replace rows (<tr>) and cells (<td>)
		$this->text = preg_replace("/
								^\|\|	# Line starts with ||
								/mx", "<tr><td>", $this->text);
		$this->text = preg_replace("/
								\|\|$	# Line ends with ||
								/mx", "</td></tr>", $this->text);
		$this->text = preg_replace("/
								(\|\|)	# Any || found in text
								/mx", "</td><td>", $this->text);
	}


	/**
	 * @brief Handles blockquotes
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseQuotes()
	{
		// Replace quotes
		//	  Inferred by indentation
		$this->text = preg_replace("/(
							(([\n])
							[ ]
							(.+)
							)+
							)/xe", "str_replace('$3', '', '<blockquote>$1</blockquote>')", $this->text);
	}

	/**
	 * @brief Handle horizontal rules
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseHorizontalRules()
	{
		// Replace horizontal rule
		// ----
		$this->text = preg_replace("/^-{4,}/m", "<hr />", $this->text);
	}

	/**
	 * @brief Handles paragraphs
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function parseParagraphs()
	{
		// Replace new lines with paragraphs
		$this->text = preg_replace("/\n\n(.+)/", '<p>$1</p>', $this->text);
	}
}
/* End of file ParserBase.class.php */
/* Location: ParserBase.class.php */
