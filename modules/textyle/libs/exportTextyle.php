<?PHP
@set_time_limit(0);

class ExportTextyle{
	var $oTextyle;
	var $site_info;
	var $module_srl;
	var $site_srl;
	var $category_list;
	var $language;
	var $timezone;
	var $fp;

	function TextyleExportObject($export_file){
		$export_file = FileHandler::getRealPath($export_file);
		$this->fp = fopen($export_file,'w');
	}

	function exportFile(){
		$this->writeSetting();
		$this->writeCategoryList();
		$this->writePostList();
		$this->writeGuestbookList();
		$this->close();
	}

	function close(){
		if($this->fp) fclose($this->fp);
	}

	function _write($content){
		if($this->fp && $content) fwrite($this->fp,$content);
	}

	function setTextyle($module_srl){
		$oTextyleModel = &getModel('textyle');
		$this->oTextyle = $oTextyleModel->getTextyle($module_srl);
		$this->module_srl = $module_srl;
		$this->site_srl = $oTextyle->site_srl;

		$oModuleModel = &getModel('module');
		$this->site_info = $oModuleModel->getSiteInfo($this->site_srl);

		// setting
		$setting->language = $oTextyle->default_language;
		$setting->domain = Context::getDefaultUrl();
		$setting->timezone = $oTextyle->timezone;
		$this->setting = $setting;
	}

	function getCategoryList(){
		$oDocumentModel = &getModel('document');
		$category_list = $oDocumentModel->getCategoryList($this->module_srl);
		$this->category_list = $category_list;

		return $this->_arrayCategoryList($category_list);	
	}
	function _arrayCategoryList($category_list){
		foreach($category_list as $k => $category){
			if($category->parent_srl>0){
				$category_list[$category->parent_srl]->children[] = &$category_list[$k]; 
			}
		}

		foreach($category_list as $k => $category){
			if($category->parent_srl>0){
				unset($category_list[$k]);
			}
		}

		return $category_list;
	}

	function getPostList(){
		$oDocumentList = array();

		$args->module_srl = join(',',array($this->module_srl,$this->module_srl*-1));
		$output = executeQueryArray('textyle.getExportDocumentList',$args);
		if($output->data){
			foreach($output->data as $attribute){
				$oDocument = new documentItem();
				$oDocument->setAttribute($attribute,false);
				$oDocument->category = $this->category_list[$oDocument->get('category_srl')]->title;
				$oDocumentList[] = $oDocument;
			}
		}

		return $oDocumentList;
	}

	function getCommentList($document_srl){
		$oCommentModel = &getModel('comment');
		$oCommentList = array();

		$args->document_srl = $document_srl;
		$output = executeQueryArray('textyle.getExportCommentList',$args);
		if($output->data){
			foreach($output->data as $attribute){
				$oComment = new commentItem();
				$oComment->setAttribute($attribute);
				$oCommentList[$attribute->comment_srl] = $oComment;
			}
		}
		return $this->_arrayCommentList($oCommentList);
	}

	function _arrayCommentList(&$oCommentList){
		if($oCommentList){
			foreach($oCommentList as $k => $oComment){
				if($oComment->parent_srl>0){
					$oCommentList[$oComment->parent_srl]->children[] = &$oCommentList[$k]; 
				}
			}

			foreach($oCommentList as $k => $oComment){
				if($oComment->parent_srl>0){
					unset($oCommentList[$k]);
				}
			}
		}
		return $oCommentList;
	}

	function getGuestbookList(){
		$args->module_srl = $this->module_srl;
		$output = executeQueryArray('textyle.getExportGuestbookList',$args);
		$guestbook_list = array();
		if($output->data){
			foreach($output->data as $k => $guestbook){
				$guestbook_list[$guestbook->textyle_guestbook_srl] = $guestbook;
			}
		}
		return $this->_arrayGuestbookList($guestbook_list);
	}

	function _arrayGuestbookList(&$guestbook_list){
		if($guestbook_list){
			foreach($guestbook_list as $k => $guestbook){
				if($guestbook->parent_srl>0){
					$guestbook_list[$guestbook->parent_srl]->children[] = &$guestbook_list[$k]; 
				}
			}

			foreach($guestbook_list as $k => $guestbook){
				if($guestbook->parent_srl>0){
					unset($guestbook_list[$k]);
				}
			}
		}

		return $guestbook_list;
	}
}



class TTXMLExport extends ExportTextyle{

	function TTXMLExport($export_file){
		parent::TextyleExportObject($export_file);
		$this->_write('<?xml version="1.0" encoding="utf-8" ?>'."\r\n".'<blog type="tattertools/1.0" migrational="false">'."\r\n");
	}

	function close(){
		$this->_write('</blog>');
		parent::close();
	}

	function writeSetting(){
		$setting[] = '<setting>';
		$setting[] = '<name>%s</name>';
		$setting[] = '<secondaryDomain></secondaryDomain>';
		$setting[] = '<defaultDomain>0</defaultDomain>';
		$setting[] = '<title>%s</title>';
		$setting[] = '<description/>';
		$setting[] = '<banner><name/><content/></banner>';
		$setting[] = '<useSlogan>1</useSlogan>';
		$setting[] = '<postsOnPage>1</postsOnPage>';
		$setting[] = '<postsOnList>10</postsOnList>';
		$setting[] = '<postsOnFeed>10</postsOnFeed>';
		$setting[] = '<publishWholeOnFeed>1</publishWholeOnFeed>';
		$setting[] = '<acceptGuestComment>1</acceptGuestComment>';
		$setting[] = '<acceptCommentOnGuestComment>1</acceptCommentOnGuestComment>';
		$setting[] = '<language>%s</language>';
		$setting[] = '<timezone>%s</timezone>';
		$setting[] = '</setting>';

		$setting = join("\r\n",$setting);

		$setting = sprintf($setting
							,$this->setting->title
							,$this->setting->title
							,$this->setting->language
							,$this->setting->timezone);

							
		$this->_write($setting);
	}

	function writePostList(){
		$oDocumentList = $this->getPostList();
		if($oDocumentList){
			foreach($oDocumentList as $oDocument){
				$this->_writePost($oDocument);
			}
		}
	}

	function _writePost(&$oDocument){

		$this->post_srl = $oDocument->document_srl;

		$member_srl = $oDocument->get('member_srl');
		if($member_srl){
			$password = $oDocument->member_password;
		}else{
			$password = $oDocument->password;
		}

		$post[] = "<post slogan=\"\" format=\"1.1\">";
		$post[] = "<author domain=\"%s\">%s</author>";
		$post[] = "<id>%s</id>";
		$post[] = "<visibility>syndicated</visibility>";
		$post[] = "<title>%s</title>";
		$post[] = "<content>%s</content>";
		$post[] = "<location></location>";
		$post[] = "<password>%s</password>";
		$post[] = "<acceptComment>1</acceptComment>";
		$post[] = "<acceptTrackback>1</acceptTrackback>";
		$post[] = "<published>%s</published>";
		$post[] = "<created>%s</created>";
		$post[] = "<modified>%s</modified>";
		$post[] = "<category>%s</category>";
		$post[] = "%s";
		$post = join("\r\n",$post);

		$content = sprintf($post
						,$this->setting->domain
						,$oDocument->get('member_srl')
						,$oDocument->document_srl
						,$this->_replaceHtml($oDocument->getTitle())
						,$this->_replacePostContent($oDocument)
						,$password
						,$this->_getTime($oDocument->get('last_update'))
						,$this->_getTime($oDocument->get('regdate'))
						,$this->_getTime($oDocument->get('last_update'))
						,$oDocument->category
						,$this->_getTag($oDocument)
					);

		$this->_write($content);

		$trackback_list = $oDocument->getTrackbacks();
		$this->_writeTrackbackList($trackback_list);

		$this->_writeCommentList($oDocument->document_srl);
		$this->_writeAttachmentList($oDocument->document_srl);
		$this->_write("</post>\r\n");
	}
	
	function _replacePostContent($oDocument){
		$content = $oDocument->getContent(false,false, false, true, true);
		$content = preg_replace_callback('/<(a|img) +([^>]+)>/i',array(&$this,'_replaceFilePath'),$content);

		return $this->_replaceHtml($content);
	}

	function _replaceFilePath($matches){
		$attr = $this->_htmlAttributeArray($matches[2]);
		$return = $matches[0];

		$oFileModel = &getModel('file');
		$file_list = $oFileModel->getFiles($this->post_srl);
		if($file_list){	
			foreach($file_list as $file){
				$change_filename = $file->sid.'.'.substr($file->uploaded_filename,strrpos($file->uploaded_filename,".")+1);
				if(strtolower($matches[1])=='a'){
					if(strpos($attr['href'],$file->download_url) !==false){
						$return = sprintf('<%s href="[##_ATTACH_PATH_##]/%s">',$matches[1],$change_filename);
					}
				}else if(strtolower($matches[1])=='img'){
					if(strpos($attr['src'],substr(str_replace(' ','%20',$file->uploaded_filename),2)) !==false){
						$return = sprintf('<%s src="[##_ATTACH_PATH_##]/%s" />',$matches[1],$change_filename);
					}
				}
			}
		}

		return $return;
	}

	function _getTag(&$oDocument){
		$tag_list = $oDocument->get('tag_list');
		$tag = '';
		if($tag_list){
			foreach($tag_list as $t){
				$tag .= '<tag>';
				$tag .= $this->_replaceHtml($t);
				$tag .= '</tag>';
			}
		}

		return $tag;
	}


	function writeGuestbookList(){
		$this->_write("<guestbook>\r\n");

		$guestbook_list = $this->getGuestbookList();
		if($guestbook_list){
			foreach($guestbook_list as $guestbook){
				$this->_writeGuestbook($guestbook);
			}
		}

		$this->_write("</guestbook>\r\n");
	}

	function _writeGuestbook($guestbook){
		$member_srl = $guestbook->member_srl;
		if($member_srl){
			$password = $guestbook->member_password;
		}else{
			$password = $guestbook->password;
		}

		$content[] = '<comment>';
		$content[] = '<commenter id="%s" email="%s">';
		$content[] = '<name>%s</name>';
		$content[] = '<homepage>%s</homepage>';
		$content[] = '<ip>%s</ip>';
		$content[] = '</commenter>';
		$content[] = '<content>%s</content>';
		$content[] = '<password>%s</password>';
		$content[] = '<secret>%s</secret>';
		$content[] = '<written>%s</written>';
		$content[] = '<isFiltered>0</isFiltered>';
		$content = join("\r\n",$content);

		$this->_write(sprintf($content
								,$guestbook->member_srl
								,$guestbook->email_address
								,$guestbook->nick_name
								,$guestbook->homepage
								,$guestbook->ipaddress
								,$this->_replaceHtml($guestbook->content)
								,$passowrd
								,$guestbook->is_secret>0?'1':'0'
								,$this->_getTime($guestbook->regdate))
					);

		if($guestbook->children){
			foreach($guestbook->children as $child_guestbook){
				$this->_writeGuestbook($child_guestbook);
			}
		}

		$this->_write("</comment>\r\n");
	}


	function _writeCommentList($document_srl){
		$oCommentList = $this->getCommentList($document_srl);

		if($oCommentList){
			foreach($oCommentList as $oComment){
				$this->_writeComment($oComment);
			}
		}
	}

	function _writeComment($oComment){
		$member_srl = $oComment->get('member_srl');
		if($member_srl){
			$password = $oComment->member_password;
		}else{
			$password = $oComment->password;
		}

		$content[] = '<comment>';
		$content[] = '<commenter id="%s" email="%s">';
		$content[] = '<name>%s</name>';
		$content[] = '<homepage>%s</homepage>';
		$content[] = '<ip>%s</ip>';
		$content[] = '</commenter>';
		$content[] = '<content>%s</content>';
		$content[] = '<password>%s</password>';
		$content[] = '<secret>%s</secret>';
		$content[] = '<written>%s</written>';
		$content[] = '<isFiltered>0</isFiltered>';
		$content = join("\r\n",$content);

		$this->_write(sprintf($content
								,$oComment->member_srl
								,$oComment->get('email_address')
								,$oComment->getNickName()
								,$oComment->getHomepageUrl()
								,$oComment->get('ipaddress')
								,$this->_replaceHtml($oComment->getContent(false,false,false))
								,$passowrd
								,$oComment->isSecret()?'1':'0'
								,$this->_getTime($oComment->get('regdate'))
					));

		if($oComment->children){
			foreach($oComment->children as $child_comment){
				$this->_writeComment($child_comment);
			}
		}

		$this->_write("</comment>\r\n");
	}

	function _writeTrackbackList($trackback_list){
		if($trackback_list){
			foreach($trackback_list as $trackback){
				$this->_writeTrackback($trackback);
			}
		}
	}
	
	function _writeTrackback($trackback){
		$content[] = "<traback>";
		$content[] = "<url>%s</url>";
		$content[] = "<site>%s</site>";
		$content[] = "<title>%s</title>";
		$content[] = "<ip>%s</ip>";
		$content[] = "<received>%s</received>";
		$content[] = "</traback>";
		join("\r\n",$content);

		$this->_write(sprintf($content
								,$trackback->url
								,$this->_replaceHtml($trackback->blog_name)
								,$this->_replaceHtml($trackback->title)
								,$trackback->ipaddress
								,$this->_getTime($trackback->regdate)
								
					));
	}


	function _writeAttachmentList($document_srl){
		$oFileModel = &getModel('file');
		$file_list = $oFileModel->getFiles($document_srl);
		if(count($file_list)) {
			foreach($file_list as $file) {
				if(file_exists(FileHandler::getRealPath($file->uploaded_filename))) $this->_writeAttachment($file);
			}
		}
	}

	function _writeAttachment($file){
		$content[] = '<attachment mime="%s" size="%s" width="%s" height="%s">';
		$content[] = '<name>%s</name>';
		$content[] = '<label>%s</label>';
		$content[] = '<enclosure>0</enclosure>';
		$content[] = '<attached>%s</attached>';
		$content[] = '<downloads>%s</downloads>';
		$content[] = '<content>';
		$content = join("\r\n",$content);
	
		$real_file = FileHandler::getRealPath($file->uploaded_filename);

		$fileext = strtolower(substr($file->uploaded_filename, strrpos($file->uploaded_filename,".")+1));
		if(in_array($fileext,array('gif','png','jpg','jpeg'))){
			list($width,$height) = getImageSize($real_file);
		}else{
			$width = 0;
			$height = 0;
		}

		$this->_write(sprintf($content
								,$this->_getMimeType($file->uploaded_filename)
								,$file->file_size
								,$width
								,$height
								,$file->sid.'.'.$fileext
								,$file->source_filename
								,$this->_getTime($file->regdate)
								,$file->download_count
					));
		
		$fp = fopen($real_file,'r');
		$this->_write(base64_encode(fread($fp,$file->file_size)));
		fclose($fp);

		$this->_write("</content>\r\n");	
		$this->_write("</attachment>\r\n");	
	}

	function writeCategoryList(){
		$category_list = $this->getCategoryList();
		if($category_list){
			foreach($category_list as $k => $category){
				$this->_writeCategory($category);
			}
		}
	}

	function _writeCategory($category){
		$this->_write("<category>\r\n");
		$this->_write(sprintf("<name>%s</name>\r\n",$category->text));
		$this->_write(sprintf("<priority>%s</priority>\r\n",$k));
		if(count($category->children)){
			foreach($category->children as $k => $c){
				$this->_writeCategory($c);
			}
		}
		$this->_write("</category>\r\n");
	}


	function _getTime($time){
		if(strlen($time)<14) return '';

		$Y = substr($time,0,4);
		$m = substr($time,4,2);
		$d = substr($time,6,2);
		$H = substr($time,8,2);
		$i = substr($time,10,2);
		$s = substr($time,12,2);
		$output = mktime($H,$i,$s,$m,$d,$Y);
		return $output;
	}

	function _replaceHtml($content){
		return str_replace(array('&','<','>','"'),array('&amp;','&lt;','&gt;','&quot;'), $content);
	}

	function _htmlAttributeArray($str){
	    $result = array();
	    $split = explode(" ",$str);
		if($split){
			foreach($split as $cur_attr){
				$value = trim(substr($cur_attr,strpos($cur_attr,"=")+1)," \t\"");
				$result[substr($cur_attr,0,strpos($cur_attr,"="))] = $value;
			}
		}
	    return $result;
	}

	function _getMimeType($filename){
		preg_match("|\.([a-z0-9]{2,4})$|i", $filename, $fileSuffix);

		switch(strtolower($fileSuffix[1])){
			case "js" :
				return "application/x-javascript";
			case "json" :
				return "application/json";
			case "jpg" :
				case "jpeg" :
				case "jpe" :
				return "image/jpg";
			case "png" :
				case "gif" :
				case "bmp" :
				case "tiff" :
				return "image/".strtolower($fileSuffix[1]);
			case "css" :
				return "text/css";
			case "xml" :
				return "application/xml";
			case "doc" :
				case "docx" :
				return "application/msword";
			case "xls" :
				case "xlt" :
				case "xlm" :
				case "xld" :
				case "xla" :
				case "xlc" :
				case "xlw" :
				case "xll" :
				return "application/vnd.ms-excel";
			case "ppt" :
				case "pps" :
				return "application/vnd.ms-powerpoint";
			case "rtf" :
				return "application/rtf";
			case "pdf" :
				return "application/pdf";
			case "html" :
				case "htm" :
				case "php" :
				return "text/html";
			case "txt" :
				return "text/plain";
			case "mpeg" :
				case "mpg" :
				case "mpe" :
				return "video/mpeg";
			case "mp3" :
				return "audio/mpeg3";
			case "wav" :
				return "audio/wav";
			case "aiff" :
				case "aif" :
				return "audio/aiff";
			case "avi" :
				return "video/msvideo";
			case "wmv" :
				return "video/x-ms-wmv";
			case "mov" :
				return "video/quicktime";
			case "zip" :
				return "application/zip";
			case "tar" :
				return "application/x-tar";
			case "swf" :
				return "application/x-shockwave-flash";

			default :
				if(function_exists("mime_content_type")) {
					$fileSuffix = mime_content_type($filename);
				}
				return "unknown/" . trim($fileSuffix[0], ".");
		}
	}
}
?>
