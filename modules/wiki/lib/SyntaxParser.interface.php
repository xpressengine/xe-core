<?php

/**
 * @brief Interface for a wiki syntax parsers 
 * @developer Corina Udrescu (xe_dev@arnia.ro)
 */
interface SyntaxParser
{
	/**
	 * @brief Converts a certain wiki syntax to HTML
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $text string
	 * @return string
	 */
	function parse($text);
	
	/**
	 * @brief Finds all internal links in a text and returns document aliases
	 * @developer Corina Udrescu (xe_dev@arnia.ro)
	 * @access public
	 * @param $text string
	 * @return array Array of document aliases 
	 */
	function getLinkedDocuments($text);
}
/* End of file SyntaxParser.interface.php */
/* Location: SyntaxParser.interface.php */
