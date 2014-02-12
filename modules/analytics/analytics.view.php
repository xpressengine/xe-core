<?php
	class analyticsView extends analytics
	{
		function init()
		{
			$this->setTemplatePath($this->module_path.'tpl');
		}

		/**
		 * @brief 선그래프 디자인 설정 파일 생성 
		 **/
		function dispAnalyticsChartDesign()
		{
			global $lang;

			Context::setResponseMethod('XMLRPC');

			$method = Context::get('method');
			$value_names =  $lang->analytics_api_valuname[$method];
			// value name중 날짜 관련 내용은 사용하지 않는다.
			array_shift($value_names);

			Context::set('names', $value_names);
			Context::set('color_set', $this->chart_colorset);

			$this->setTemplateFile('visit_chartDesign');
		}

		function dispAnalyticsAPIData()
		{
			$api_key = Context::get('api_key');
			$method = Context::get('method');
			$param = array();
			$start_date = Context::get('start_date');
			if ($start_date)
				$param['start_date'] = $start_date;

			$end_date = Context::get('end_date');
			if ($end_date)
				$param['end_date'] = $end_date;

			$xml_obj = $this->_getXMLData($api_key, $method, $param);

			if (!$xml_obj || $xml_obj->response->error->body != 0) return new Object(-1, 'msg_invalid_request');
			
			if(!method_exists(&$this, '_parse'.ucfirst($method))) return new Object(-1, 'msg_undefined_method');
			
			$parsed_data = call_user_method('_parse'.ucfirst($method), &$this, $xml_obj);
			$parsed_data->method = $method;

			Context::setResponseMethod('XMLRPC');
			Context::set('parsed_data', $parsed_data);
		
			switch($method)
			{
				case 'visitStayTime':
				case 'visitBack':
				case 'visitPath':
				case 'comeEngine':
				case 'comeSearchText':
					$this->setTemplateFile('flash.pie.data');
					break;
				default :
					$this->setTemplateFile('flash.data');
			}
		}

		function _parseVisit($xml_obj)
		{
			global $lang;

			$response_obj = $xml_obj->response;

			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;
			$parsed_data->unit = '일';
		
			$parsed_data->data = array();
			$_tmp_array = $lang->analytics_api_valuname['visit'];
			array_shift($_tmp_array);
			$parsed_data->value_names = join("|", $_tmp_array);
			$_max = 0;

			if (!$response_obj->data)
			{
				$parsed_data->data[date('YmdHis', strtotime($parsed_data->end_date))] = '0|0|0|0';	
			}
			else
			{
				// 결과값이 1개인 경우 stdClass로 연산 필요
				if(is_array($response_obj->data))
				{
					foreach($response_obj->data as $key => $val)
					{
						$parsed_data->data[date('YmdHis', strtotime($val->day->body))] = $val->uv->body.'|'.$val->visit_count->body.'|'.$val->newvisit_uv->body.'|'.$val->revisit_uv->body;
						$_row_max = max($val->uv->body, $val->visit_count->body, $val->newvisit_uv->body, $val->revisit_uv->body);
						if ($_max < $_row_max) $_max = $_row_max;
					}
				}
				else
				{
					$_data = $response_obj->data;

					$parsed_data->data[date('YmdHis', strtotime($_data->day->body))] = $_data->uv->body.'|'.$_data->visit_count->body.'|'.$_data->newvisit_uv->body.'|'.$_data->revisit_uv->body;
					$_max = max($_data->uv->body, $_data->visit_count->body, $_data->newvisit_uv->body, $_data->revisit_uv->body);
				}
			}
			$parsed_data->unitMax = $_max;
		
			return $parsed_data;
		}
		
		function _parseVisitPageView($xml_obj)
		{
			global $lang;
			
			$response_obj = $xml_obj->response;
			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;
			$parsed_data->unit = '일';
			
			$parsed_data->data = array();
			$_tmp_array = $lang->analytics_api_valuname['visitPageView'];
			array_shift($_tmp_array);
			$parsed_data->value_names = join("|", $_tmp_array);

			$_max = 0;
			
			if (!$response_obj->data)
			{
				$parsed_data->data[date('YmdHis', strtotime($parsed_data->end_date))] = '0|0|0|0';	
			}
			else
			{
				if(is_array($response_obj->data))
				{
					foreach($response_obj->data as $key => $val)
					{
						$parsed_data->data[date('YmdHis', strtotime($val->day->body))] = $val->pv->body.'|'
																					.$val->pv_per_visit->body.'|'
																					.$val->newvisit_pv->body.'|'
																					.$val->revisit_pv->body;
						$_row_max = max($val->pv->body, $val->pv_per_visit->body, $val->newvisit_pv->body, $val->revisit_pv->body);
						if ($_max < $_row_max) $_max = $_row_max;
					}
				}
				else
				{
					$_data = $response_obj->data;

					$parsed_data->data[date('YmdHis', strtotime($_data->day->body))] = $_data->pv->body.'|'
																					.$_data->pv_per_visit->body.'|'
																					.$_data->newvisit_pv->body.'|'
																					.$_data->revisit_pv->body;

					$_max = max($_data->pv->body, $_data->pv_per_visit->body, $_data->newvisit_pv->body, $_data->revisit_pv->body);
				}
			}
			$parsed_data->unitMax = $_max;
			
			return $parsed_data;
		}
			
		function _parseVisitTime($xml_obj)
		{
			global $lang;
			
			$response_obj = $xml_obj->response;
			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;
			$parsed_data->unit = '시간';
				
			$parsed_data->data = array();
			$_tmp_array = $lang->analytics_api_valuname['visitTime'];
			array_shift($_tmp_array);
			$parsed_data->value_names = join("|", $_tmp_array);

			$_max = 0;
			
			if (!$response_obj->data)
			{
				$parsed_data->data[date('YmdHis', strtotime($parsed_data->end_date))] = '0|0|0|0|0';	
			}
			else
			{
				if(is_array($response_obj->data))
				{
					foreach($response_obj->data as $key => $val)
					{
						$parsed_data->data[date('YmdHis', strtotime($val->day->body.'-'.$val->timezone->body))] = $val->sumvisit_count->body.'|'
																						.$val->sumvisitor->body.'|'
																						.$val->sumrevisitor->body.'|'
																						.$val->sumnewvisitor->body.'|'
																						.$val->sumpv->body;
						$_row_max = max($val->sumvisit_count->body, $val->sumvisitor->body, $val->sumrevisitor->body, $val->sumnewvisitor->body, $val->sumpv->body);
						if ($_max < $_row_max) $_max = $_row_max;
					}
				}
				else
				{
					$val = $response_obj->data;
					
					$parsed_data->data[date('YmdHis', strtotime($val->day->body.'-'.$val->timezone->body))] = $val->sumvisit_count->body.'|'
																						.$val->sumvisitor->body.'|'
																						.$val->sumrevisitor->body.'|'
																						.$val->sumnewvisitor->body.'|'
																						.$val->sumpv->body;
					$_max = max($val->sumvisit_count->body, $val->sumvisitor->body, $val->sumrevisitor->body, $val->sumnewvisitor->body, $val->sumpv->body);
				}
			}
			$parsed_data->unitMax = $_max;

			return $parsed_data;
		}

		function _parseVisitDay($xml_obj)
		{
			global $lang;
			
			$response_obj = $xml_obj->response;
			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;
			$parsed_data->unit = '요일';
			
			$parsed_data->data = array();
			$_tmp_array = $lang->analytics_api_valuname['visitDay'];
			array_shift($_tmp_array);
			$parsed_data->value_names = join("|", $_tmp_array);
			
			$_max = 0;
			
			if (!$response_obj->data)
			{
				$parsed_data->data[date('YmdHis', strtotime($parsed_data->end_date))] = '0|0|0|0|0';	
			}
			else
			{
				if(is_array($response_obj->data))
				{
					foreach($response_obj->data as $key => $val)
					{
						$parsed_data->data[date('YmdHis', strtotime($val->day->body))] = $val->avgvisit->body.'|'
																						.$val->avguv->body.'|'
																						.$val->avgnewvisit->body.'|'
																						.$val->avgrevisit->body.'|'
																						.$val->avgpv->body;
						$_row_max = max($val->avgvisit->body, $val->avguv->body, $val->avgnewvisit->body, $val->avgrevisit->body, $val->avgpv->body);
						if ($_max < $_row_max) $_max = $_row_max;
					}
				}
				else
				{
					$val = $response_obj->data;

					$parsed_data->data[date('YmdHis', strtotime($val->day->body))] = $val->avgvisit->body.'|'
																					.$val->avguv->body.'|'
																					.$val->avgnewvisit->body.'|'
																					.$val->avgrevisit->body.'|'
																					.$val->avgpv->body;
					$_max = max($val->avgvisit->body, $val->avguv->body, $val->avgnewvisit->body, $val->avgrevisit->body, $val->avgpv->body);

				}
			}
			$parsed_data->unitMax = $_max;

			return $parsed_data;
		}

		function _parseVisitBack($xml_obj)
		{
			global $lang;
			
			$response_obj = $xml_obj->response;
			$parsed_data->end_date = $response_obj->end_date->body;
			
			if (!$response_obj->data) return new Object('msg_none_data');
			
			$parsed_data->data = array();
			$_sum_etc = 0;
			foreach($response_obj->data as $key => $val)
			{
				if ($val->freq->body < 7)
				{
					$parsed_data->data[$lang->analytics_api_valuname['visitBack'][$val->freq->body.'day']] = round($val->vc->body/$response_obj->countdata->sumtotalvc->body * 100);
				}
				else
				{
					$_sum_etc += $val->vc->body;
				}
			}
			$parsed_data->data[$lang->analytics_api_valuname['visitBack']['etc']] = round($_sum_etc / $response_obj->countdata->sumtotalvc->body * 100);
			
			return $parsed_data;
		}

		function _parseVisitStayTime($xml_obj)
		{
			global $lang;

			$response_obj = $xml_obj->response;
			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;

			if (!$response_obj->countdata) return new Object('msg_none_data');

			$parsed_data->data = array();
			
			$total_count = $response_obj->countdata->sumtotalvc->body;
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_under_30sec']] = round($response_obj->countdata->vc_under_30sec->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_31_60']] = round($response_obj->countdata->vc_31_60->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_2min']] = round($response_obj->countdata->vc_2min->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_3min']] = round($response_obj->countdata->vc_3min->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_4min']] = round($response_obj->countdata->vc_4min->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_5min']] = round($response_obj->countdata->vc_5min->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_10min']] = round($response_obj->countdata->vc_10min->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_30min']] = round($response_obj->countdata->vc_30min->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_60min']] = round($response_obj->countdata->vc_60min->body/$total_count * 100);
			$parsed_data->data[$lang->analytics_api_valuname['visitStayTime']['vc_60high']] = round($response_obj->countdata->vc_60high->body/$total_count * 100);
		
			return $parsed_data;
		}

		function _parseVisitPath($xml_obj)
		{
			global $lang;
			
			$response_obj = $xml_obj->response;
			
			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;
			
			if (!$response_obj->data) return new Object('msg_none_data');
			
			$parsed_data->data = array();
			
			$_sum_data = array('one'=>0
							  ,'four'=>0
							  ,'ten'=>0
							  ,'fifteen'=>0
							  ,'twenty'=>0
							  ,'thirty'=>0
							  ,'fourty'=>0
							  ,'fifty'=>0
							  ,'high'=>0);
			$_total = 0;

			if(!is_array($response_obj->data))
				$response_obj->data = array($response_obj->data);

			foreach($response_obj->data as $val)
			{
				$_sum_data['one'] += $val->one->body;
				$_sum_data['four'] += $val->four->body;
				$_sum_data['ten'] += $val->ten->body;
				$_sum_data['fifteen'] += $val->fifteen->body;
				$_sum_data['twenty'] += $val->twenty->body;
				$_sum_data['thirty'] += $val->thirty->body;
				$_sum_data['fourty'] += $val->fourty->body;
				$_sum_data['fifty']+= $val->fifty->body;
				$_sum_data['high'] += $val->high->body;
				$_total += ($val->one->body
						+$val->four->body
						+$val->ten->body
						+$val->fifteen->body
						+$val->twenty->body
						+$val->thirty->body
						+$val->fourty->body
						+$val->fifty->body
						+$val->high->body);
			}

			foreach($_sum_data as $key => $val)
			{
				$parsed_data->data[$lang->analytics_api_valuname['visitPath'][$key]] = round($val / $_total * 100);
			}
		
			return $parsed_data;
		}


		//유입분석
		function _parseComeEngine($xml_obj)
		{
			$response_obj = $xml_obj->response;
			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;

			if (!$response_obj->data) return new Object('msg_none_data');

			$parsed_data->data = array();
			$_sum_total = 0;	
			
			if(is_array($response_obj->data))
			{
				foreach($response_obj->data as $key => $val)
				{
					$_sum_total += $val->sumqc->body;
				}
	
				foreach($response_obj->data as $key => $val)
				{
					$parsed_data->data[$val->searchengine->body] = sprintf('%0.2f', $val->sumqc->body / $_sum_total * 100);
				}
			}
			else
			{
				$val = $response_obj->data;
				$parsed_data->data[$val->searchengine->body] = 100;
			}
			
			return $parsed_data;
		}

		function _parseComeSearchText($xml_obj)
		{
			global $lang;

			$response_obj = $xml_obj->response;

			$parsed_data->end_date = $response_obj->end_date->body;
			$parsed_data->start_date = $response_obj->start_date->body;

			if (!$response_obj->data) return new Object('msg_none_data');

			$parsed_data->data = array();

			$_sum_total = $response_obj->data_totalpv->body;

			$_etc = 0;
			foreach($response_obj->data as $key => $val)
			{
				if ($key <= 4)
					$_etc += $val->sumqc->body;
			}
	
			foreach($response_obj->data as $key => $val)
			{
				if($key > 4)
					break;

				$parsed_data->data[$val->query->body] = round($val->sumqc->body / $_sum_total * 100);
			}
			
			$parsed_data->data[$lang->etc] = round(($_sum_total - $_etc) / $_sum_total * 100);

			return $parsed_data;
		}
	}
?>
