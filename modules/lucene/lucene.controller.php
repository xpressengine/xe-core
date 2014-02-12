<?php

/**
 * @class   luceneController
 * @author  NHN (developers@xpressengine.com)
 * @brief   lucene 모듈의 controller 클래스
 **/
class luceneController extends lucene {

	/**
	 * @brief 글 작성시 색인 갱신 트리거
	 **/
	function triggerInsertDocument(&$obj)
	{
		//$output = $this->doIndexNow('_document');
		return $output;
	}

	/**
	 * @brief 댓글 작성시 색인 갱신 트리거
	 **/
	function triggerInsertComment(&$obj)
	{
		//$output = $this->doIndexNow('_comment');
		return $output;
	}

	/**
	 * @brief 글 삭제시 색인 갱신 트리거
	 **/
	function triggerDeleteDocument(&$obj)
	{
		//$output = $this->doIndexNow('_document');
		return $output;
	}

	/**
	 * @brief 댓글 삭제시 색인 갱신 트리거
	 **/
	function triggerDeleteComment(&$obj)
	{
		//$output = $this->doIndexNow('_comment');
		return $output;
	}

	function doIndexNow($target) {
		$oModuleModel = &getModel('module');
		$config = $oModuleModel->getModuleConfig('lucene');

		$json_obj->serviceName = $config->service_name_prefix.$target;
		$param = json_encode2($json_obj);

		$apiUrl = $config->searchUrl.'lucene_index_bloc-1.0/IndexDocumentBrokerBO/doIndex';
		$res = FileHandler::getRemoteResource($apiUrl, $params, 3, "POST", "application/json", array(), array());
		return new Object();
	}
}

?>
