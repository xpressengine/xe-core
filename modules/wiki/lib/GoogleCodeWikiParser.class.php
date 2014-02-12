<?php
/* require_once ('SyntaxParser.interface.php'); // Commented for backwards compatibility with PHP4 */
require_once ('ParserBase.class.php'); 
require_once ('WikiText.class.php');

/**
 * @brief Converts Google Wiki Syntax to HTML
 * @developer Corina Udrescu (xe_dev@arnia.ro)
 */
class GoogleCodeWikiParser extends ParserBase
{
	/**
	 * @brief Constructor
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $wiki_site  WikiSite
	 * @return
	 */
	function __construct($wiki_site) 
	{
		parent::__construct($wiki_site);
	}

	/**
	 * @brief Overrides parseText in base
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @override
	 * @access protected
	 * @return
	 */
	function parseText()
	{
		parent::parseText();
        $parser = new WTParser($this->text, 'googlecode', $this->wiki_site);
        $this->text = $parser->toString(true);
	}

	/**
	 * @brief Parses headings
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 *
	 * TODO: Also add anchors (for local links)
	 */
	function parseHeadings()
	{
		// Replace headings
		// ====== Heading 6 ======
		$this->text = preg_replace("/^====== (.+?) ======( *)$/m", "<h6>$1</h6>", $this->text);
		// ===== Heading 5 =====
		$this->text = preg_replace("/^===== (.+?) =====( *)$/m", "<h5>$1</h5>", $this->text);
		// ==== Heading 4 ====
		$this->text = preg_replace("/^==== (.+?) ====( *)$/m", "<h4>$1</h4>", $this->text);
		// === Heading 3 ===
		$this->text = preg_replace("/
								^===\s     # Line starts with three equal signs, followed by a space
								(.+?)      # One or more characters (of any type except line breaks)
								\s===      # Followed by another space and three equal signs
								(\s*)$     # The line can end directly, or there can be spaces
								/mx", "<h3>$1</h3>", $this->text); // The m modifier specifies that matches are per line, instead of per document
		// == Heading 2 ==
		$this->text = preg_replace("/^== (.+?) ==( *)$/m", "<h2>$1</h2>", $this->text);
		// = Heading 1 =
		$this->text = preg_replace("/^= (.+?) =( *)$/m", "<h1>$1</h1>", $this->text);
	}

	/**
	 * @brief Escapes anything the syntax needs
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access protected
	 * @return
	 */
	function escapeWhateverThereIsToEscape()
	{
		// Escape <img /> tags
		$this->text = preg_replace_callback("~
												([<]img
												.*
												[/]?[>])
											~x", array($this, "_escapeBlock"), $this->text);

		// Escape <a> tags
		$this->text = preg_replace_callback("~
												([<]a
												.*
												[<][/]a[>])
											~x", array($this, "_escapeBlock"), $this->text);

		// Escape CamelCase text square brackets' description
		$this->text = preg_replace_callback("~
												[\[]			# Starts with opening square bracket [
												.*?              # Followed by anything
												[ ]				 # Has to begin with one space - we only escape CamelCase in descriptions
												(
												(				# Sequence of letters that ..
													[A-Z]	    # Start with an uppercase letter
													[a-z0-9]+	# Followed by at least one lowercase letter
												){2,}			# Repeated at least two times
												)
												.*?				# Followed by anything
												[\]]			# Ends with closing square bracket
											~x", array($this, "_escapeCamelCaseInSquareBracketLinks"), $this->text);

		// Escape CamelCase inside external links
		$this->text = preg_replace_callback("~
												(https?|ftp|file)
												://
			                                    [^ ]*?
												(
												(				# Sequence of letters that ..
													[A-Z]	    # Start with an uppercase letter
													[a-z0-9]+	# Followed by at least one lowercase letter
												){2,}			# Repeated at least two times
												)
			                                    [^ ]*?
											~x", array($this, "_escapeCamelCaseInExternalLinks"), $this->text);


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
	 * @brief Callback function for CamelCase text inside square bracket links
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array()
	 * @return string
	 */
	function _escapeCamelCaseInSquareBracketLinks(&$matches)
	{
		$this->batch_count++;
		$replacement = "%%%" . $this->batch_count . "%%%";
		$this->escaped_blocks[$replacement] = $matches[1];
		return str_replace($matches[1], $replacement, $matches[0]);
	}

	/**
	 * @brief Callback function for CamelCase text inside square bracket links
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access private
	 * @param $matches array()
	 * @return string
	 */
	function _escapeCamelCaseInExternalLinks(&$matches)
	{
		$this->batch_count++;
		$replacement =  "%%%" . $this->batch_count . "%%%";
		$this->escaped_blocks[$replacement] = $matches[2];
		return str_replace($matches[2], $replacement, $matches[0]);
	}
}
/* End of file GoogleCodeWikiParser.class.php */
/* Location: GoogleCodeWikiParser.class.php */
