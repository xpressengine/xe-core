<?php
    /**
     * @class  dfowardModel
     * @author zero (zero@nzeo.com)
     * @brief  dfoward 모듈의 model 클래스
     **/

    class dfowardModel extends dfoward {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @biref 특정 dfoward_srl에 대한 정보를 return
         **/
        function getDfowardHostInfo() {
            return new Object(-1, 'service_stop_info');
            $dfoward_srl = Context::get('dfoward_srl');
            $dfoward_info = $this->getDfoward($dfoward_srl);

            if($dfoward_info->dfoward_srl != $dfoward_srl) return new Object(-1, 'msg_inavlid_request');

            $this->add('dfoward_srl', $dfoward_info->dfoward_srl);
            $this->add('hostname', $dfoward_info->hostname);
            $this->add('target_url', $dfoward_info->target_url);
            $this->add('title', $dfoward_info->title);
        }

        /**
         * @brief 특정 hostname의 정보를 구함
         **/
        function getDfowardHostname($hostname) {
            $args->hostname = $hostname;
            $output = executeQuery('dfoward.getHostname',$args);
            return $output->data;
        }

        /**
         * @brief 특정 도메인포워딩 정보 가져오기
         **/
        function getDfoward($dfoward_srl) {
            $args->dfoward_srl = $dfoward_srl;
            $output = executeQuery('dfoward.getDfoward',$args);
            return $output->data;
       }

        /**
         * @brief 특정 회원의 도메인포워딩 정보 가져오기
         **/
        function getDfowards($member_srl) {
            $args->member_srl = $member_srl;
            $output = executeQueryArray('dfoward.getDfowards', $args);
            return $output->data;
        }

        /**
         * @brief 전체 도메인 포워딩 정보 가져오기
         **/
        function getDfowardList($obj) {
            // 변수 체크
            $args->page = $obj->page?$obj->page:1;
            $args->list_count = $obj->list_count?$obj->list_count:20;
            $args->page_count = $obj->page_count?$obj->page_count:10;

            // dfoward.getDocumentList 쿼리 실행
            $output = executeQuery('dfoward.getDfowardList', $args);

            return $output;
        }

    }
?>
