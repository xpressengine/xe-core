<?php
    /**
     * @class  resourceModel
     * @author NHN (developers@xpressengine.com)
     * @brief  resource model class
     **/

    class resourceModel extends resource {

        function init() {
        }

        function getPackageList($module_srl, $status = null, $category_srl = null, $member_srl = null, $page = 1) {
            $args->module_srl = $module_srl;

            if(!is_null($status) && in_array($status, array('accepted','reservation','waiting'))) $args->status = $status;
            else $args->idx_status = 'a';

            if(!is_null($category_srl)) $args->category_srl = $category_srl;
            else $args->idx_category_srl = 0;

            if(!is_null($member_srl)) $args->member_srl = $member_srl;

            $args->page = $page;

            $output = executeQueryArray('resource.getPackageList', $args);

            return $output;
        }

        function getPackage($module_srl, $package_srl, $member_srl = null) {
            $args->module_srl = $module_srl;
            $args->package_srl = $package_srl;
            if(!is_null($member_srl)) $args->member_srl = $member_srl;
            $output = executeQuery('resource.getPackage', $args);
            return $output->data;
        }

		function getPackageByPath($path)
		{
			$args->path = $path;
			$output = executeQueryArray('resource.getPackageByPath', $args);
			return $output->data[0];
		}

        function getItem($module_srl, $package_srl, $item_srl) {
            $oFileModel = &getModel('file');
            $args->module_srl = $module_srl;
            $args->package_srl = $package_srl;
            $args->item_srl = $item_srl;

            $output = executeQuery('resource.getItem', $args);
            if(!$output->toBool() || !$output->data) return null;
            $item = $output->data;
            $item->download_url = getFullUrl().$oFileModel->getDownloadUrl($item->file_srl, $item->sid);
            return $item;
        }

        function getItems($module_srl, $package_srl) {
            $oFileModel = &getModel('file');

            $args->module_srl = $module_srl;
            $args->package_srl = $package_srl;

            $output = executeQueryArray('resource.getItems', $args);
            if(!$output->data) return array();
            foreach($output->data as $key => $val) {
                if($val->voter>0) $val->star = (int)($val->voted/$val->voter);
                else $val->star = 0;
                $output->data[$key]->download_url = getFullUrl().$oFileModel->getDownloadUrl($val->file_srl, $val->sid);
            }

            return $output->data;
        }

        function getLatestItemList($module_srl, $category_srl = null, $childs = null, $member_srl = null, $search_keyword = null, $order_target = 'package.update_order', $order_type = 'asc', $page = 1, $list_count = null) {
            $oFileModel = &getModel('file');

            $args->module_srl = $module_srl;

            if(!is_null($childs) && preg_match('/^([0-9])([0-9,]+)([0-9])$/', $childs)) $args->category_srl = $childs;
            else if(!is_null($category_srl)) $args->category_srl = $category_srl;
            else $args->idx_category_srl = 0;

            if(!is_null($member_srl)) $args->member_srl = $member_srl;

            if($search_keyword) {
                $tr = explode(' ',$search_keyword);
                for($i=0,$c=count($tr);$i<$c;$i++) {
                    if(trim($tr[$i])) $t[] = trim($tr[$i]);
                }
                if(count($t)) $args->search_keyword = implode('%', $t);
            }

			if(!$list_count) $list_count = 20;
            $args->list_count = $list_count;
            $args->page = $page;
            $args->order_type = $order_type;
            $args->page_count = 10;
            if($order_target == 'download') $args->sort_index = 'package.downloaded';
            elseif($order_target == 'popular') $args->sort_index = 'package.voted';
            else {
                $args->order_type = $args->order_type=='asc'?'desc':'asc';
                $args->sort_index = 'item.list_order';
            }

            $output = executeQueryArray('resource.getLatestItemList', $args);
            if($output->data) {
                foreach($output->data as $key => $val) {
                    if($val->package_voter>0) $output->data[$key]->package_star = (int)($val->package_voted/$val->package_voter);
                    else $output->data[$key]->package_star = 0;
                    $output->data[$key]->download_url = getFullUrl().$oFileModel->getDownloadUrl($val->item_file_srl, $val->sid);
                }
            }

            return $output;
        }

        function getLatestItem($package_srl) {
            $args->package_srl = $package_srl;
            $output = executeQuery('resource.getLatestItem', $args);
            return $output->data;

        }

        function getDependency($module_srl, $item_srl) {
            $args->module_srl = $module_srl;
            $args->item_srl = $item_srl;
            $output = executeQueryArray('resource.getDependency', $args);
            return $output->data;
        }

        function hasVoted($module_srl, $package_srl, $item_srl, $member_srl) {
            $args->module_srl = $module_srl;
            $args->package_srl = $package_srl;
            $args->item_srl = $item_srl;
            $args->member_srl = $member_srl;
            $output = executeQuery('resource.hasVoted', $args);
            return $output->data->count>0?true:false;

        }

        function getCategoryPacakgeCount($module_srl) {
            $count_args->module_srl = $module_srl;
            $output = executeQueryArray('resource.getCategoryPackageCount', $count_args);
            if(!$output->data) return array();
            foreach($output->data as $key => $val) {
                $result[0]->count +=$val->count;
                $result[$val->category_srl] = $val;
            }
            return $result;
        }
        /**
         * @brief return module name in sitemap
         **/
		function triggerModuleListInSitemap(&$obj)
		{
			array_push($obj, 'resource');
		}
    }
?>
