<?php
class moumiModel extends moumi
{
	function init()
	{
	}

	function searchPackageList()
	{
		$logged_info = Context::get('logged_info');
		if($logged_info->is_admin != 'Y') return new Object(-1, 'msg_not_permitted');

		$args->keyword = Context::get('keyword');
		$args->page = Context::get('page');
		$args->list_count = 20;
		$args->page_count = 10;
		$output = executeQueryArray('moumi.searchPackageList', $args);
		if(!$output->toBool()) return $output;

		$oModuleModel = &getModel('module');
		if(!$output->data) $output->data = array();
		foreach($output->data as $no => $val)
		{
			$module_info = $oModuleModel->getModuleInfoByModuleSrl($val->module_srl);
			$url = getUrl('', 'mid', $module_info->mid, 'package_srl', $val->package_srl);
			$output->data[$no]->url = $url;
		}

		Context::set('package_list', $output->data);
		Context::set('navigation', $output->page_navigation);
		Context::set('page', $output->page);

		$tpl_path = sprintf('%stpl/', $this->module_path);
		$oTemplate = &TemplateHandler::getInstance();
		$output = $oTemplate->compile($tpl_path, 'SearchPackageList');

		$this->add('output', $output);
	}

	function getPackageList()
	{
		$output = executeQueryArray('moumi.getPackageList');
		if(!$output->toBool()) return $output;
		if(!$output->data) $output->data = array();

		$result = new Object();
		$result->add('package_list', $output->data);

		return $result;
	}

	function getPackage($package_srl)
	{
		$args->package_srl = $package_srl;
		$output = executeQuery('moumi.getPackage', $args);
		if(!$output->toBool()) return $output;

		$result = new Object();
		$result->add('package_info', $output->data);

		return $result;
	}

	function getStatistics()
	{
		$package = $this->getPackageStatistics();
		$document = (int)$this->getDocumentStatistics();
		$comment = (int)$this->getCommentStatistics();
		$member = (int)$this->getMemberStatistics();
		$location = $this->getLocationInfo();

		$result = new stdClass;
		$result->packages = $package->package;
		$result->count_package = $package->count_packages;
		$result->document_count = $document;
		$result->comment_count = $comment;
		$result->member_count = $member;
		$result->location_info = $location;

		return $result;
	}

	function getNews()
	{
		$output = $this->getPackageList();
		if(!$output->toBool()) return $output;
		$package_list = $output->get('package_list');

		$news = array();

		foreach($package_list as $package_no => $package)
		{
			if($package->include_news)
			{
				$item_list = $this->getResourceItems($package->package_srl);
				$latest = &$item_list[0];
				$news[$package->package_srl]->title = $package->title;
				$news[$package->package_srl]->package_srl = (int)$latest->package_srl;
				$news[$package->package_srl]->latest_version = $latest->version;
				$news[$package->package_srl]->download_link = getNotEncodedFullUrl('', 'mid','download', 'package_srl' ,$latest->package_srl, 'item_srl', $latest->item_srl);;

				foreach($item_list as $idx => $item)
				{
					$_new = &$news[$package->package_srl]->items[$idx];
					$_new->title = $package->title . ' ver. ' . $item->version;
					$_new->url = getNotEncodedFullUrl('', 'mid','download', 'package_srl' ,$item->package_srl, 'item_srl', $item->item_srl);
					$_new->regdate = $item->regdate;
					$_new->item_srl = (int)$item->item_srl;
					$_new->version = $item->version;
				}
			}
		}

		return $news;
	}

	function getPackageStatistics()
	{
		$output = $this->getPackageList();
		if(!$output->toBool()) return $output;
		$package_list = $output->get('package_list');

		$result = new stdClass;
		$result->count_packages = array();
		$result->package = array();

		$cond = new stdClass;
		$cond->module_srl = 18322904;
		$output = executeQueryArray('moumi.getStatisticsPackage', $cond);

		foreach($package_list as $package_no => &$package)
		{
			$package->title = $package->title;
			$package->package_srl = (int)$package->package_srl;
			$package->downloaded = (int)$package->downloaded;
			$result->package[$package->package_srl] = $package;
		}

		if($output->data && count($output->data))
		{
			foreach($output->data as &$category)
			{
				if(strpos($category->category_title, '$user_lang') !== FALSE)
				{
					$category->category_title = $this->getLang($category->category_title);
				}
				$category->category_srl = (int)$category->category_srl;
				$category->count = (int)$category->count;

				$result->count_packages[$category->category_srl] = $category;
			}
		}

		return $result;
	}

	function getDocumentStatistics()
	{
		// $target_date = date('Ymd', strtotime('-1 day'));

		// $cond = new stdClass();
		// $cond->start_date = $target_date . '000000';
		// $cond->end_date = $target_date . '595959';
		$output = executeQuery('moumi.getDocumentCount', $cond);

		return $output->data->count;
	}

	function getCommentStatistics()
	{
		// $target_date = date('Ymd', strtotime('-1 day'));

		// $cond = new stdClass();
		// $cond->start_date = $target_date . '000000';
		// $cond->end_date = $target_date . '595959';
		$output = executeQuery('moumi.getCommentCount', $cond);

		return $output->data->count;
	}

	function getMemberStatistics()
	{
		// $target_date = date('Ymd', strtotime('-1 day'));

		// $cond = new stdClass();
		// $cond->start_date = $target_date . '000000';
		// $cond->end_date = $target_date . '595959';
		$output = executeQuery('moumi.getMemberCount', $cond);

		return $output->data->count;
	}

	function getLocationInfo()
	{
		$result->location = _XE_LOCATION_;
		$result->location_site = _XE_LOCATION_SITE_;
	}

	function getResourceItems($package_srl)
	{
		$args->package_srl = $package_srl;
		$output = executeQueryArray('moumi.getResourceItems', $args);
		if(!$output->data) return array();

		return $output->data;
	}

	function getLang($key, $site_srl = 0)
	{
		$key = str_replace('$user_lang->', '', $key);

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

	function getMemberGroupMember($args) 
	{
		$output = executeQueryArray('moumi.getMemberGroupMember', $args);
		if(!$output->data) return array();

		return $output->data;
	}

	function insertCronResult($args)
	{
		if(!isset($args->result_type))
			return false;

		if(!isset($args->regdate))
			return false;

		$regdate = substr($args->regdate, 0, 8);
		$getArgs = new stdClass;
		$getArgs->result_type = $args->result_type;
		if(isset($args->unique_date)) {
			$getArgs->more_regdate = $regdate . "000000";
			$getArgs->less_regdate = $regdate . "235959";
		} else {
			$getArgs->regdate = $args->regdate;
		}

		$row = $this->getCronResult($getArgs);
		if(!$row) 
			$this->_insertCronResult($args);
		else {
			$args->regdate = $row->regdate;
			$this->_updateCronResult($args);
		}

		return true;
	}

	function getCronResult($args)
	{
		$output = executeQuery('moumi.getCronResult', $args);
		if(!$output->toBool()) return array();

		return $output->data;
	}

	function getCronResultArray($args)
	{
		$output = executeQueryArray('moumi.getCronResult', $args);
		if(!$output->toBool()) return array();

		return $output->data;
	}

	function _insertCronResult($args)
	{
		if(!isset($args->result_type))
			return false;

		if(!isset($args->regdate))
			return false;

		executeQuery('moumi.insertCronResult', $args);
		return true;
	}

	function _updateCronResult($args)
	{
		if(!isset($args->result_type))
			return false;

		if(!isset($args->regdate))
			return false;

		executeQuery('moumi.updateCronResult', $args);
		return true;
	}

	function getCronResultPormotion($args) 
	{
		$getArgs = new stdClass;
		$getArgs->result_type = "promotion_group";
		if(isset($args->regdate))  {
			$getArgs->more_regdate = $args->regdate . "000000";
			$getArgs->less_regdate = $args->regdate . "235959";
		}
		if(isset($args->more_regdate))
			$getArgs->more_regdate = $args->less_regdate;
		
		if(isset($args->less_regdate))
			$getArgs->less_regdate = $args->less_regdate;
		$data = $this->getCronResult($getArgs);	
		return json_decode($data->result, true);
	}

}

