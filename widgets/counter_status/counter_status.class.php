<?php
    /**
     * @class counter_status
     * @author NAVER (developers@xpressengine.com)
     * @version 1.7
     * @brief Display counter status by using data in the counter module
     **/


    class counter_status extends WidgetHandler {


        /**
         * @brief Widget execution
         * Get extra_vars declared in ./widgets/widget/conf/info.xml as arguments
         * After generating the result, do not print but return it.
         */
        function proc($args) {
            // 전체, 어제, 오늘 접속 현황을 가져옴
            $oCounterModel = &getModel('counter');


            $site_module_info = Context::get('site_module_info');
            $output = $oCounterModel->getStatus(array('00000000', date('Ymd', $_SERVER['REQUEST_TIME']-60*60*24), date('Ymd')), $site_module_info->site_srl);
            if(count($output) > 0)
            {
            foreach($output as $key => $val)
                {
                    if(!$key) Context::set('total_counter', $val);
                    elseif($key == date("Ymd")) Context::set('today_counter', $val);
                    else Context::set('yesterday_counter', $val);
                }
            }
            // 가입한 회원수 출력
            $oMemberModel = &getModel('member');
            $args->date = date("Ymd000000", $_SERVER['REQUEST_TIME']-60*60*24);
            $today = date("Ymd");
            $output = executeQueryArray("admin.getMemberStatus", $args);
            if(count($output) > 0) {
                foreach($output->data as $var) {
                    if($var->date == $today) {
                        $status->member->today = $var->count;
                    } else {
                        $status->member->yesterday = $var->count;
                    }
                }
            }
            if($args->member_count == 'Y')
            {
                $output = executeQuery("admin.getMemberCount", $args);
                $status->member->total = $output->data->count;
            }
            Context::set('start_module', $output->data);
            Context::set('status', $status);


            // 전체글수
            $output = executeQueryArray("admin.getDocumentStatus", $args);
            if(count($output) > 0) {
                foreach($output->data as $var) {
                    if($var->date == $today) {
                        $status->document->today = $var->count;
                    } else {
                        $status->document->yesterday = $var->count;
                    }
                }
            }
			if($args->document_count == 'Y')
			{
                $output = executeQuery("admin.getDocumentCount", $args);
                $status->document->total = $output->data->count;
			}
            Context::set('start_module', $output->data);
            Context::set('status', $status);

            // 전체 댓글수
            $output = executeQueryArray("admin.getCommentStatus", $args);
            if(count($output) > 0) {
                foreach($output->data as $var) {
                    if($var->date == $today) {
                        $status->comment->today = $var->count;
                    } else {
                        $status->comment->yesterday = $var->count;
                    }
                }
            }

			if($args->comment_count == 'Y')
			{
                $output = executeQuery("admin.getCommentCount", $args);
                $status->comment->total = $output->data->count;
			}
            Context::set('start_module', $output->data);
            Context::set('status', $status);

            // 엮인글수
			$oTrackbackModel = getModel('trackback');
			if ($oTrackbackModel)
			{
			$output = executeQueryArray("admin.getTrackbackStatus", $args);
                if(count($output) > 0) {
                    foreach($output->data as $var) {
                        if($var->date == $today) {
                            $status->trackback->today = $var->count;
                        } else {
                            $status->trackback->yesterday = $var->count;
                        }
                    }
                }
				
                if($args->trackback_count == 'Y')
                {
                    $output = executeQuery("admin.getTrackbackCount", $args);
                    $status->trackback->total = $output->data->count;
                }
                Context::set('start_module', $output->data);
                Context::set('status', $status);
            } else {
                return;
            }

            // 첨부파일수
            $output = executeQueryArray("admin.getFileStatus", $args);
            if(count($output) > 0) {
                foreach($output->data as $var) {
                    if($var->date == $today) {
                        $status->file->today = $var->count;
                    } else {
                        $status->file->yesterday = $var->count;
                    }
                }
            }

            if($args->file_count == 'Y')
            {
                $output = executeQuery("admin.getFileCount", $args);
                $status->file->total = $output->data->count;
            }
            Context::set('start_module', $output->data);
            Context::set('status', $status);

            // Set a path of the template skin (values of skin, colorset settings)
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            Context::set('colorset', $args->colorset);

            // Specify a template file
            $tpl_file = 'counter_status';

            // Compile a template
            $oTemplate = &TemplateHandler::getInstance();
            return $oTemplate->compile($tpl_path, $tpl_file);
        }
    }
    /* End of file counter_status.class.php */
    /* Location: ./widgets/counter_status/counter_status.class.php */
?>
