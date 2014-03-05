<?php
class moumiAPI extends moumi
{
	function init()
	{
		Context::setRequestMethod('JSON');
	}

	function getStatistics()
	{
		if(!$this->isAllowed()) return new Object(-1, 'msg_not_permitted');

		$oModel = &getModel('moumi');
		$result = $oModel->getStatistics();

		$this->add('statistics', $result);
		$this->add('date', date('YmdHis'));
	}

	function getCronResultPormotion()
	{	
		$oModel = &getModel('moumi');
		$args = new stdClass;
		$args->regdate = date("Ymd");
		$result = $oModel->getCronResultPormotion($args);


		$this->add('statistics', $result);
		$this->add('date', date('YmdHis'));
	}

	function getNews()
	{
		if(!$this->isAllowed()) return new Object(-1, 'msg_not_permitted');

		$oModel = &getModel('moumi');
		$result = $oModel->getNews();

		$this->add('data', $result);
	}
}
