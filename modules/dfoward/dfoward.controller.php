<?php
    /**
     * @class  dfowardController
     * @author zero (zero@nzeo.com)
     * @brief  dfoward 모듈의 Controller class
     * dfoward의 controller 클래스는 사용자가 도메인포워딩에 글을 쓰거나 댓글을 쓰는등의 동작을 제어한다.
     **/

    class dfowardController extends dfoward {

        /**
         * @brief 초기화
         **/
        function init() {
        }

        /**
         * @brief 문서 입력
         **/
        function procDfowardInsert() {
            return new Object(-1, 'service_stop_info');

            global $lang; 

            // 로그인 되어 있지 않으면 오류 
            $oMember = getModel('member');
            if(!$oMember->isLogged()) return new Object(-1, 'msg_not_permitted');

            $member_info = $oMember->getLoggedInfo();
            $member_srl = $member_info->member_srl;

            /**
             * 등록시 필요한 변수를 세팅한다.
             **/
            $obj = Context::gets('hostname', 'target_url', 'title', 'content');
            if(!$obj->hostname) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('hostname')));
            if(!$obj->target_url) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('target_url')));
            if(!$obj->title) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('title')));
            //if(!$obj->content) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('content')));

            // hostname의 중복 체크를 함
            $oDfowardModel = &getModel('dfoward');
            $host_info = $oDfowardModel->getDfowardHostname($obj->hostname);
            if($host_info->dfoward_srl) return new Object(-1,'msg_exists_hostname');

            // 나머지 변수 모두 세팅
            $obj->dfoward_srl = getNextSequence();
            $obj->list_order = -1 * $obj->dfoward_srl;
            $obj->member_srl = $member_srl;

            // 입력
            $output = executeQuery('dfoward.insert', $obj);
            if(!$output->toBool()) return $output;

            $this->setMessage('success_registed');
        }

        /**
         * @brief 수정
         **/
        function procDfowardUpdate() {
            return new Object(-1, 'service_stop_info');
            global $lang; 

            // 로그인 되어 있지 않으면 오류 
            $oMember = getModel('member');
            if(!$oMember->isLogged()) return new Object(-1, 'msg_not_permitted');
            $member_info = $oMember->getLoggedInfo();
            $member_srl = $member_info->member_srl;

            // 원래 대상의 정보를 구함
            $dfoward_srl = Context::get('dfoward_srl');
            if(!$dfoward_srl) return new Object(-1, 'msg_inavlid_request');

            $oDfowardModel = &getModel('dfoward');
            $dfoward_info = $oDfowardModel->getDfoward($dfoward_srl);

            // 등록된 대상이 없으면 에러 
            if($dfoward_info->dfoward_srl != $dfoward_srl) return new Object(-1, 'msg_inavlid_request');

            // 권한 체크
            if($member_info->is_admin != 'Y' && $member_info->member_srl != $member_srl) return new Object(-1, 'msg_not_permitted');

            /**
             * 수정시 필요한 변수를 세팅한다.
             **/
            $obj = Context::gets('hostname', 'target_url', 'title', 'content');
            $obj->dfoward_srl = $dfoward_srl;
            if(!$obj->hostname) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('hostname')));
            if(!$obj->target_url) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('target_url')));
            if(!$obj->title) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('title')));
            //if(!$obj->content) return new Object(-1, sprintf($lang->filter->isnull, Context::getLang('content')));

            // hostname의 중복 체크를 함
            $host_info = $oDfowardModel->getDfowardHostname($obj->hostname);
            if($host_info->dfoward_srl && $host_info->dfoward_srl != $obj->dfoward_srl) return new Object(-1,'msg_exists_hostname');

            // 입력
            $output = executeQuery('dfoward.update', $obj);
            if(!$output->toBool()) return $output;

            $this->setMessage('success_updated');
        }

        /**
         * @brief 삭제
         **/
        function procDfowardDelete() {
            return new Object(-1, 'service_stop_info');
            global $lang; 

            // 로그인 되어 있지 않으면 오류 
            $oMember = getModel('member');
            if(!$oMember->isLogged()) return new Object(-1, 'msg_not_permitted');
            $member_info = $oMember->getLoggedInfo();
            $member_srl = $member_info->member_srl;

            // 원래 대상의 정보를 구함
            $dfoward_srl = Context::get('dfoward_srl');
            if(!$dfoward_srl) return new Object(-1, 'msg_inavlid_request');

            $oDfowardModel = &getModel('dfoward');
            $dfoward_info = $oDfowardModel->getDfoward($dfoward_srl);

            // 등록된 대상이 없으면 에러 
            if($dfoward_info->dfoward_srl != $dfoward_srl) return new Object(-1, 'msg_inavlid_request');

            // 권한 체크
            if($member_info->is_admin != 'Y' && $member_info->member_srl != $member_srl) return new Object(-1, 'msg_not_permitted');

            /**
             * 삭제시 필요한 변수를 세팅한다.
             **/
            $obj->dfoward_srl = $dfoward_srl;

            // 입력
            $output = executeQuery('dfoward.delete', $obj);
            if(!$output->toBool()) return $output;

            $this->setMessage('success_deleted');
        }

    }
?>
