<?php
    /**
     * @class  dfowardView
     * @author zero (zero@nzeo.com)
     * @brief  dfoward 모듈의 View class
     * dfoward의 view 클래스는 사용자가 도메인포워딩의 목록을 보고 글을 쓰거나 댓글을 쓸수 있게 하는 사용자 부분의 display를 관장한다.
     **/

    class dfowardView extends dfoward {

        /**
         * @brief 초기화
         *
         * 사용자부분의 목록 및 기타 페이지 출력을 위해 스킨 정보라든지 스킨의 템플릿 파일 위치 등을 선언해 놓는다.
         **/
        function init() {
            /**
             * 모듈정보에서 넘어오는 skin값을 이용하여 최종 출력할 템플릿의 위치를 출력한다.
             * $this->module_path는 ./modules/dfoward/의 값을 가지고 있다
             **/
            $template_path = sprintf("%stpl/", $this->module_path);
            $this->setTemplatePath($template_path);
        }

        /**
         * @brief 목록 및 입력항목 출력
         **/
        function dispDfowardContent() {
            return new Object(-1, 'service_stop_info');

            // 로그인 되어 있지 않으면 오류 
            $oMember = getModel('member');
            if(!$oMember->isLogged()) return $this->stop('msg_not_permitted');
            $member_info = $oMember->getLoggedInfo();
            $member_srl = $member_info->member_srl;

            // 현재 회원의 도메인 포워딩 등록 정보를 구함
            $oDfowardModel = &getModel('dfoward');
            $output = $oDfowardModel->getDfowards($member_srl);

            if(count($output)) {
                foreach($output as $key => $val) {
                    if(!eregi("^http:\/\/",$val->target_url)) $output[$key]->target_url = "http://".$val->target_url;
                }
            }
            Context::set('dfoward_list', $output);

            /**
             * 템플릿 파일을 지정한다.
             * 이미 template path는 init()에서 정의를 하였다.
             **/
            $this->setTemplateFile('dfoward_list');
        }

        /**
         * @brief 글 수정 화면 출력
         **/
        function dispDfowardModify() {
        }

    }
?>
