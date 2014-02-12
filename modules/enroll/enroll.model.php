<?php
    /**
     * @class  enrollModel
     * @author sol (sol@nhn.com)
     * @brief  enroll model class
     **/

    class enrollModel extends enroll {

        function init() {
        }

        function getEnrollItem($obj){
            $args->module_srl = $obj->module_srl;
            $args->enroll_srl = $obj->enroll_srl;
            $output = executeQuery('enroll.getEnrollItem', $args);
            return $output;
        }

        function getEnrollItemListUser($obj){
            $args->module_srl = $obj->module_srl;
            $args->status = $obj->status;
            $args->page = $obj->page ? $obj->page: 1;
            $args->list_count = 20;
            $args->order_type = 'asc';
            $output = executeQueryArray('enroll.getEnrollItemListUser', $args);
            return $output;
        }
        function getEnrollItemList($obj){
            $args->module_srl = $obj->module_srl;
            $args->status = $obj->status;
            $args->page = $obj->page ? $obj->page: 1;
            $args->list_count = 20;
            $args->order_type = 'asc';
            $output = executeQueryArray('enroll.getEnrollItemList', $args);
            return $output;
        }

        function getEnrollItemCount($obj){
            $args->module_srl = $obj->module_srl;
            $args->status = $obj->status;
            $output = executeQuery('enroll.getEnrollItemCount', $args);
            if(!$output->toBool()) return 0;
            return $output->data->count;
        }

	function getEnrollItemByEmailAddress($obj) {
	   $args->module_srl = $obj->module_srl;
	   $args->email_address = $obj->email_address;
	   $args->member_srl = $obj->member_srl;

	   $output = executeQuery('enroll.getEnrollItemByEmailAddress',$args);
           return $output;
	}
	function getEnrollItemLogin($obj) {
	   $args->module_srl = $obj->module_srl;
	   $args->email_address = $obj->email_address;
	   $args->password = md5($obj->password);

	   $output = executeQuery('enroll.getEnrollItemLogin',$args);
	   if($output->data->enroll_srl) return $output->data->enroll_srl;
	   else return false;
	}
	function validEnrollItem() {
		$obj = Context::gets('module_srl','email_address','password');
		$value = $this->getEnrollItemLogin($obj);
		if($value) $this->add('validation',true);
		else $this->add('validation',false);

	}
	function getCntEnrollItemByEmailAddress($obj) {
	   $args->module_srl = $obj->module_srl;
	   $args->email_address = $obj->email_address;
	   $output = executeQuery('enroll.getCntEnrollItemByEmailAddress',$args);
            if(!$output->toBool()) return 0;
            return $output->data->count;
	}
    }
?>
