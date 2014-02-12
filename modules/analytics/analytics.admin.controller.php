<?php

	class analyticsAdminController extends analytics {
		
		function init(){
		}
		
		function procAnalyticsAdminInsertAPIKey() {
			// API KEY 값을 가지고 온다.
			$args = Context::gets('api_key');
			
			// module controlle 객체 생성하여 Key값 저장
			$oModuleController = &getController('module');
			$output = $oModuleController->insertModuleConfig('analytics', $args);
			return $output;
		}
        
        function procAnalyticsAdminCheckAPIKey(){
			global $lang;

			$api_key = Context::get('api_key');
			
			if(!$api_key) return new Object(-1,'msg_invalid_request');
            
            $url = $this->_getApiUrl('checkApiKey', $api_key);
			$data = FileHandler::getRemoteResource($url, null, 3, 'GET', '');
			

			if (!$data) return new Object(-1, 'msg_invalid_request');

			$oParser = new XmlParser();
			
			$xml_obj = $oParser->parse($data);
			
            $result = $xml_obj->response->message->body;

			$message = $lang->cmd_check_api_key_fail;

			if($result == 'success')
			{
				$message = $lang->cmd_check_api_key_success;
			}
			
			$this->add('result_status',$message);
		}
	}
?>
