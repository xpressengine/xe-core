<?php
/* Copyright (C) NAVER <http://www.navercorp.com> */

/**
 * @class  getSyndicationList
 * @author NAVER (developers@xpressengine.com)
 * @brief syndication model class of the module
 **/

class syndicationModel extends syndication
{
	private $site_url = null;
	private $uri_scheme = 'http://';
	private $syndication_password= null;
	private $year = null;
	private $langs = array();
	private $granted_modules = array();
	static private $modules = array();

	function init() {
		$oModuleModel = getModel('module');
		$config = $oModuleModel->getModuleConfig('syndication');
		if(Context::getSslStatus() == 'always') $this->uri_scheme = 'https://';

		$this->site_url = preg_replace('/\/+$/is', '', $config->site_url);
		$this->syndication_password = $config->syndication_password;
		$this->year = $config->year;

		$output = executeQueryArray('syndication.getGrantedModules');
		if($output->data) {
			foreach($output->data as $key => $val) {
				$this->granted_modules[] = $val->module_srl;
			}
		}

		$this->gzhandler_enable = FALSE;
	}

	function isExceptedModules($module_srl) {
		$args = new stdClass;
		$args->module_srl = $module_srl;

		$output = executeQuery('syndication.getExceptModule', $args);
		if($output->data->count) return TRUE;

		$output = executeQuery('syndication.getGrantedModule', $args);
		if($output->data->count) return TRUE;

		return FALSE;
	}

	function getExceptModuleSrls()
	{
		$output = executeQueryArray('syndication.getExceptModuleSrls');
		$module_srls = array();
		if (is_array($output->data))
		{
			foreach($output->data as $val)
			{
				$module_srls[] = $val->module_srl;
			}
		}
		return $module_srls;
	}

	function getLang($key, $site_srl)
	{
		if(!$this->langs[$site_srl])
		{
			$this->langs[$site_srl] = array();
			$args = new stdClass;
			$args->site_srl = $site_srl;
			$args->lang_code = Context::getLangType();
			$output = executeQueryArray("syndication.getLang", $args);
			if(!$output->toBool() || !$output->data) return $key;

			foreach($output->data as $value)
			{
				$this->langs[$site_srl][$value->name] = $value->value;
			}
		}
		if($this->langs[$site_srl][$key])
		{
			return $this->langs[$site_srl][$key];
		}
		else return $key;
	}

	function handleLang($title, $site_srl)
	{
		$matches = NULL;
		if(!preg_match("/\\\$user_lang->(.+)/", $title, $matches))
		{
			return $title;
		}
		else
		{
			return $this->getLang($matches[1], $site_srl);
		}
	}

	function getSyndicationList() {
		$oModuleModel = getModel('module');
		$config = $oModuleModel->getModuleConfig('syndication');
		if(!$config->year || !$config->site_url || !$config->syndication_token) 
		{
			return $this->makeObject(-1,'msg_check_syndication_config');
		}

		$id = Context::get('id');
		$type = Context::get('type');

		$startTime = Context::get('start-time');
		$endTime = Context::get('end-time');

		$page = Context::get('page');
		if(!$page)
		{
			$page = 1;
		}
		$vars = Context::getRequestVars();
		if(!$id || !$type)
		{
			return $this->makeObject(-1,'msg_invalid_request');
		}

		if(!preg_match('/^tag:([^,]+),([0-9]+):(site|channel|article)(.*)$/i',$id,$matches)) 
		{
			return $this->makeObject(-1,'msg_invalid_request');
		}

		if($config->syndication_password != Context::get('syndication_password'))
		{
			return $this->makeObject(-1,'msg_invalid_request');
		}

		$url = $matches[1];
		$year = $matches[2];
		$target = $matches[3];
		$id = $matches[4];
		if($id && $id{0}==':')
		{
			$id = substr($id, 1);
		}

		$module_srl = null;
		$document_srl = null;
		if($id && strpos($id,'-')!==false) 
		{
			list($module_srl, $document_srl) = explode('-', $id);
		}
		elseif($id) 
		{
			$module_srl = $id;
		}

		if(!$url || !$year || !$target)
		{
			return $this->makeObject(-1,'msg_invalid_request');
		}

		$time_zone = substr($GLOBALS['_time_zone'], 0, 3).':'.substr($GLOBALS['_time_zone'], 3);
		Context::set('time_zone', $time_zone);

		$site_module_info = Context::get('site_module_info');

		if($target == 'channel' && !$module_srl)
		{
			$target = 'site';
		}

		if($module_srl)
		{
			$args = new stdClass;
			$args->module_srls = $module_srl;
			$output = executeQuery('syndication.getModules', $args);
			$module_info = $output->data;
			self::$modules[$module_srl] = $output->data;
		}

		if($target == 'channel' && $module_srl)
		{
			if($module_info) 
			{
				$args->module_srl = $module_srl;
				$output = executeQuery('syndication.getExceptModules', $args);
				if($output->data->count) 
				{
					$error = 'target is not founded';
				}
			} 
			else 
			{
				$error = 'target is not founded';
			}

			unset($args);
		}

		if(!$error) 
		{
			Context::set('target', $target);
			Context::set('type', $type);

			$oMemberModel = getModel('member');
			$member_config = $oMemberModel->getMemberConfig();

			$oModuleModel = getModel('module');
			$site_config = $oModuleModel->getModuleConfig('module');

			switch($target) 
			{
				case 'site' :
						$site_info = new stdClass;
						$site_info->id = $this->getID('site');
						$site_info->site_url = getFullSiteUrl($this->uri_scheme . $this->site_url, '');
						$site_info->site_title = $this->handleLang($site_module_info->browser_title, $site_module_info->site_srl);
						$site_info->title = $site_info->site_title;

						if($module_srl)
						{
							$args->module_srl = $module_srl;
							$site_info->title = $this->handleLang($module_info->browser_title, $module_info->site_srl);
							if(!$site_info->title)
							{
								$site_info->title = $site_info->site_title;
							}
						}
						else
						{
							$except_module_output = executeQueryArray('syndication.getExceptModuleSrls');
							if(is_array($except_module_output->data))
							{
								$except_module_srls = array();
								foreach($except_module_output->data as $val)
								{
									$except_module_srls[] = $val->module_srl;
								}
								$args->except_modules = implode(',', $except_module_srls);
							}
						}

						$output = executeQuery('syndication.getSiteUpdatedTime', $args);

						if($output->data)
						{
							$site_info->updated = date("Y-m-d\\TH:i:s", ztime($output->data->last_update)).$time_zone;
						}

						$site_info->self_href = $this->getSelfHref($site_info->id,$type);
						Context::set('site_info', $site_info);

						$this->setTemplateFile('site');
						switch($type) {
							case 'article' :
								// 문서 전체를 신디케이션에 추가
								Context::set('articles', $this->getArticles($module_srl, $page, $startTime, $endTime, 'article',$site_info->id));
								$next_url = Context::get('articles')->next_url;
								
								break;
							case 'deleted' :
								// 문서 전체를 신디케이션에서 삭제
								Context::set('deleted', $this->getArticles($module_srl, $page, $startTime, $endTime, 'deleted',$site_info->id));
								$next_url = Context::get('deleted')->next_url;
								break;
							default :
								$this->setTemplateFile('site.info');
								break;
						}

						// 다음 페이지가 있다면 다시 신디케이션 호출
						if($next_url)
						{
							$oSyndicationController = getController('syndication');
							$oSyndicationController->ping(Context::get('id'), Context::get('type'), ++$page);
						}
					break;
				case 'channel' :
						$channel_info = new stdClass;
						$channel_info->id = $this->getID('channel', $module_info->module_srl);
						$channel_info->site_title = $this->handleLang($site_module_info->browser_title, $site_module_info->site_srl);
						$channel_info->title = $this->handleLang($module_info->browser_title, $module_info->site_srl);
						$channel_info->updated = date("Y-m-d\\TH:i:s").$time_zone;
						$channel_info->self_href = $this->getSelfHref($channel_info->id, $type);
						$channel_info->site_url = getFullSiteUrl($this->uri_scheme . $this->site_url, '');
						$channel_info->alternative_href = $this->getChannelAlternativeHref($module_info->module_srl);
						$channel_info->summary = $module_info->description;
						if($module_info->module == "textyle")
						{
							$channel_info->type = "blog";
							$channel_info->rss_href = getFullSiteUrl($module_info->domain, '', 'mid', $module_info->mid, 'act', 'rss');
						}
						else
						{
							$channel_info->type = "web";
						}
						$except_module_srls = $this->getExceptModuleSrls();
						if($except_module_srls)
						{
							$args->except_modules = implode(',',$except_module_srls);
						}

						$output = executeQuery('syndication.getSiteUpdatedTime', $args);
						if($output->data) $channel_info->updated = date("Y-m-d\\TH:i:s", ztime($output->data->last_update)).$time_zone;
						Context::set('channel_info', $channel_info);

						$this->setTemplateFile('channel');
						switch($type) {
							case 'article' :
									Context::set('articles', $this->getArticles($module_srl, $page, $startTime, $endTime, 'article', $channel_info->id));
								break;
							case 'deleted' :
									Context::set('deleted', $this->getDeleted($module_srl, $page, $startTime, $endTime, 'deleted', $channel_info->id));
								break;
							default :
									$this->setTemplateFile('channel.info');
								break;
						}
					break;

					case 'article':
						$channel_info = new stdClass;
						$channel_info->id = $this->getID('channel', $module_info->module_srl);
						$channel_info->title = $this->handleLang($module_info->browser_title, $module_info->site_srl);
						$channel_info->site_title = $site_config->siteTitle;
						if(!$channel_info->site_title) {
							$channel_info->site_title = $channel_info->title;
						}
						$channel_info->updated = date("Y-m-d\\TH:i:s").$time_zone;
						$channel_info->self_href = $this->getSelfHref($channel_info->id, $type);
						$channel_info->site_url = getFullSiteUrl($this->uri_scheme . $this->site_url, '');
						$channel_info->alternative_href = $this->getChannelAlternativeHref($module_info->module_srl);
						$channel_info->webmaster_name = $member_config->webmaster_name;
						$channel_info->webmaster_email = $member_config->webmaster_email;

						$except_module_srls = $this->getExceptModuleSrls();
						if($except_module_srls)
						{
							$args->except_modules = implode(',',$except_module_srls);
						}

						$output = executeQuery('syndication.getSiteUpdatedTime', $args);
						if($output->data) $channel_info->updated = date("Y-m-d\\TH:i:s", ztime($output->data->last_update)).$time_zone;
						Context::set('channel_info', $channel_info);
						Context::set('member_config', $member_config);

						$this->setTemplateFile('channel');
						switch($type) {
							case "article" :
								$articles = new stdClass; 
								$articles->list = array($this->getArticle($document_srl));
								Context::set('articles', $articles);
							break;

							case "deleted" :
								$deleted = new stdClass; 
								$deleted->list = $this->getDeletedByDocumentSrl($document_srl);
								Context::set('deleted', $deleted);
							break;
						}
					break;
			}
		} else {
			Context::set('message', $error);
			$this->setTemplateFile('error');
		}

		$this->setTemplatePath($this->module_path.'tpl');
		Context::setResponseMethod('XMLRPC');
	}

	// @DEPRECATED
	function getChannels() {
		if($module_srls) $args->module_srls = $module_srls;
		if(count($this->granted_modules)) $args->except_module_srls = implode(',',$this->granted_modules);
		$output = executeQueryArray('syndication.getModules', $args);

		$time_zone = substr($GLOBALS['_time_zone'],0,3).':'.substr($GLOBALS['_time_zone'],3);
		Context::set('time_zone', $time_zone);

		if($output->data) {
			foreach($output->data as $module_info) {
				unset($obj);
				$obj = new stdClass;
				$obj->id = $this->getID('channel', $module_info->module_srl);
				$obj->title = $this->handleLang($module_info->browser_title, $module_info->site_srl);
				$obj->updated = date("Y-m-d\\TH:i:s").$time_zone;
				$obj->self_href = $this->getSelfHref($obj->id, 'channel');
				$obj->alternative_href = $this->getChannelAlternativeHref($module_info);
				$obj->summary = $module_info->description;
				if($module_info->module == "textyle")
				{
					$obj->type = "blog";
					$obj->rss_href = getFullSiteUrl($module_info->domain, '', 'mid', $module_info->mid, 'act', 'rss');
				}
				else
				{
					$obj->type = "web";
				}

				$list[] = $obj;
			}
		}
		return $list;
	}

	function getArticle($document_srl) {
		if($this->site_url==null) $this->init();

		$oDocumentModel = getModel('document');
		$oDocument = $oDocumentModel->getDocument($document_srl,false,false);
		if(!$oDocument->isExists()) return;

		$val = $oDocument->getObjectVars();

		$time_zone = substr($GLOBALS['_time_zone'],0,3).':'.substr($GLOBALS['_time_zone'],3);
		Context::set('time_zone', $time_zone);

		$mdoule_info = self::$modules[$oDocument->get('module_srl')];

		$article = new stdClass();
		$article->id = $this->getID('article', $oDocument->get('module_srl').'-'.$oDocument->get('document_srl'));
		$article->updated = date("Y-m-d\\TH:i:s", ztime($oDocument->get('last_update'))).$time_zone;
		$article->published = date("Y-m-d\\TH:i:s", ztime($oDocument->get('regdate'))).$time_zone;
		$article->alternative_href = $this->getAlternativeHref($oDocument->get('document_srl'), $oDocument->get('module_srl'));
		$article->channel_alternative_href = $this->getChannelAlternativeHref($oDocument->get('module_srl'));
		$article->nick_name = (!$oDocument->get('nick_name')) ? $oDocument->get('user_name') : $oDocument->get('nick_name');
		$article->title = $oDocument->getTitle();
		$article->content = $oDocument->get('content');
		if($val->category_srl) {
			$category = $oDocumentModel->getCategory($val->category_srl);
			$category_title = $category->title;
			$article->category = new stdClass();
			$article->category->term = $val->category_srl;
			$article->category->label = $category_title;
		}

		return $article;
	}

	function getArticles($module_srl = null, $page=1, $startTime = null, $endTime = null, $type = null, $id = null) {
		if($this->site_url==null) $this->init();

		$args = new stdClass;
		if($module_srl) $args->module_srl = $module_srl;
		if($startTime) $args->start_date = $this->getDate($startTime);
		if($endTime) $args->end_date = $this->getDate($endTime);
		if(count($this->granted_modules)) $args->except_module_srls = implode(',',$this->granted_modules);
		$args->page = $page;
		$output = executeQueryArray('syndication.getDocumentList', $args);
		$cur_page = $output->page_navigation->cur_page;
		$total_page = $output->page_navigation->last_page;

		$result = new stdClass;
		$result->next_url = null;
		$result->list = array();

		$time_zone = substr($GLOBALS['_time_zone'],0,3).':'.substr($GLOBALS['_time_zone'],3);
		Context::set('time_zone', $time_zone);

		if($cur_page<$total_page) {
			$next_url = $this->getSelfHref($id, $type);
			if($startTime) $next_url .= '&startTime='.$startTime;
			if($endTime) $next_url .= '&endTime='.$endTime;
			$result->next_url = $next_url.'&page='.($cur_page+1);
		}

		if($output->data) {
			foreach($output->data as $key => $val) {
				$article = new stdClass();
				$article->id = $this->getID('article', $val->module_srl.'-'.$val->document_srl);
				$article->updated = date("Y-m-d\\TH:i:s", ztime($val->last_update)).$time_zone;
				$article->published = date("Y-m-d\\TH:i:s", ztime($val->regdate)).$time_zone;
				$article->alternative_href = getFullSiteUrl($this->uri_scheme . $this->site_url, '', 'document_srl', $val->document_srl);
				$article->channel_alternative_href = $this->getChannelAlternativeHref($val->module_srl);
				$article->nick_name = (!$val->nick_name) ? $val->user_name : $val->nick_name;
				$article->content = $val->content;
				$result->list[] = $article;
			}
		}
		return $result;
	}

	function getDeleted($module_srl = null, $page = 1, $startTime = null, $endTime = null, $type = null, $id = null) {
		if($this->site_url==null) $this->init();

		$args = new stdClass;
		if($module_srl) $args->module_srl= $module_srl;
		if($startTime) $args->start_date = $this->getDate($startTime);
		if($endTime) $args->end_date = $this->getDate($endTime);
		$args->page = $page;

		$output = executeQueryArray('syndication.getDeletedList', $args);

		$cur_page = $output->page_navigation->cur_page;
		$total_page = $output->page_navigation->last_page;

		$result = new stdClass;
		$result->next_url = null;
		$result->list = array();

		$time_zone = substr($GLOBALS['_time_zone'],0,3).':'.substr($GLOBALS['_time_zone'],3);
		Context::set('time_zone', $time_zone);

		if($cur_page<$total_page) {
			$next_url = $this->getSelfHref($id, $type);
			if($startTime) $next_url .= '&startTime='.$startTime;
			if($endTime) $next_url .= '&endTime='.$endTime;
			$result->next_url = $next_url . '&page='.($cur_page+1);
		}

		if($output->data) {
			foreach($output->data as $key => $val) {
				$val->id = $this->getID('article', $val->module_srl.'-'.$val->document_srl);
				$val->deleted = date("Y-m-d\\TH:i:s", ztime($val->regdate)).$time_zone;
				$val->alternative_href = getFullSiteUrl($this->uri_scheme . $this->site_url, '', 'document_srl', $val->document_srl);
				$val->channel_id = $this->getID('channel', $val->module_srl.'-'.$val->document_srl);
				$output->data[$key] = $val;
			}
			$result->list = $output->data;
		}
		return $result;
	}

	function getDeletedByDocumentSrl($document_srl)
	{
		$args = new stdClass;
		$args->document_srl = $document_srl;
		$output = executeQueryArray('syndication.getDeletedList', $args);
		foreach($output->data as $key => $val) {
			$val->id = $this->getID('article', $val->module_srl.'-'.$val->document_srl);
			$val->deleted = date("Y-m-d\\TH:i:s", ztime($val->regdate)).$time_zone;
			$val->alternative_href = getFullSiteUrl($this->uri_scheme . $this->site_url, '', 'document_srl', $val->document_srl);
			$val->channel_id = $this->getID('channel', $val->module_srl.'-'.$val->document_srl);
			$output->data[$key] = $val;
		}

		return $output->data;
	}

	function getID($type, $target_id = null) {
		if($this->site_url==null) $this->init();

		return sprintf('tag:%s,%d:%s', $this->site_url, $this->year, $type) . ($target_id?':'.$target_id:'');
	}

	function getChannelAlternativeHref($module_srl) {
		static $module_info = array();
		if(!isset($module_info[$module_srl])) {
			$args = new stdClass;
			$args->module_srl = $module_srl;
			$output = executeQuery('syndication.getModuleSiteInfo', $args);
			if($output->data) $module_info[$module_srl] = $output->data;
			else $module_info[$module_srl] = null;
		}

		if(is_null($module_info[$module_srl])) return $this->site_url;

		$domain = $module_info[$module_srl]->domain;
		$url = getFullSiteUrl($domain, '', 'mid', $module_info[$module_srl]->mid);
		if(substr($url,0,1)=='/') $domain = $this->uri_scheme . $this->site_url . $url;
		return $url;
	}

	function getSelfHref($id, $type = null) {
		if($this->site_url==null) $this->init();

		return  sprintf('%s/?module=syndication&act=getSyndicationList&id=%s&type=%s&syndication_password=%s', $this->uri_scheme . $this->site_url, $id, $type, $this->syndication_password);
	}

	/**
	 * 문서의 고유 URL 반환
	 */
	function getAlternativeHref($document_srl, $module_srl) {
		if($this->site_url==null) $this->init();

		if(!self::$modules[$module_srl]) {
			$args = new stdClass;
			$args->module_srls = $module_srl;
			$output = executeQuery('syndication.getModules', $args);
			$module_info = $output->data;
			self::$modules[$module_srl] = $module_info;
		} else {
			$module_info = self::$modules[$module_srl];
		}

		$domain = $module_info->domain;
		$url = getFullSiteUrl($domain, '', 'mid', $module_info->mid, 'document_srl', $document_srl);
		if(substr($url,0,1)=='/') $domain = $this->uri_scheme . $this->site_url.$url;
		return $url;
	}

	function getDate($date) {
		$time = strtotime($date);
		if($time == -1) $time = ztime(str_replace(array('-','T',':'),'',$date));
		return date('YmdHis', $time);
	}

	function getResentPingLogPath()
	{
		$target_filename = _XE_PATH_.'files/cache/tmp/syndication_ping_log';
		if(!file_exists($target_filename))
		{
			FileHandler::writeFile($target_filename, '');
		}
		return $target_filename;
	}

	function setResentPingLog($msg)
	{
		$file_path = $this->getResentPingLogPath();

		$args = new stdClass;
		$args->regdate = date('YmdHis');
		$args->message = urlencode($msg);

		$list = $this->getResentPingLog();
		if(count($list)>=10)
		{
			array_pop($list);
		}
		array_unshift($list, $args);
		FileHandler::writeFile($file_path, serialize($list));

		return true;
	}

	function getResentPingLog()
	{
		$file_path = $this->getResentPingLogPath();
		$str = FileHandler::readFile($file_path);
		$list = array();
		if($str)
		{
			$list = unserialize($str);
		}

		return $list;
	}
}
