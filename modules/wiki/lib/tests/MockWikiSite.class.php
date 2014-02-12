<?php
/* require_once('..\WikiSite.interface.php'); // Commented for backwards compatibility with PHP4 */

class MockWikiSite /* implements WikiSite // Commented for backwards compatibility with PHP4 */
{
	
	function currentUserCanCreateContent() {
			return true;
		}
		
	function documentExists($document_name) {
			return $document_name;
		}

	function getFullLink($document_name) {
		return $document_name;
	}

    function getEditPageUrlForCurrentDocument($section=null) {
        return 'http://edit.com?section='.$section;
    }
}