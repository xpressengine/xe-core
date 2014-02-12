<?php
    /**
     * @class  dfowardAdminView
     * @author zero (zero@nzeo.com)
     * @brief  dfoward 모듈의 admin view class
     * 도메인포워딩 관리자 기능은 생성된 도메인포워딩의 목록, 신규 등록 및 수정, 권한과 스킨의 설정으로 이루어진다.
     **/

    class dfowardAdminView extends dfoward {

        /**
         * @brief 초기화
         * 손쉬운 사용을 위해서 module_srl이 넘어올 경우 해당 도메인포워딩의 모듈 정보를 미리 구해서 세팅해 놓도록 한다.
         * 각 method에서 하거나 별도의 method를 만들어서 호출하면 되지만 코드의 양을 줄이고 직관성을 높이기 위해서 설정한 코드이다.
         **/
        function init() {
            // 템플릿 경로 지정, 관리자 페이지를 위한 템플릿은 별도의 스킨 기능이 없이 ./modules/모듈/tpl/ 에 위치해 놓기에 바로 지정을 해 놓는다.
            $template_path = sprintf("%stpl/",$this->module_path);
            $this->setTemplatePath($template_path);
        }

        /**
         * @brief 생성된 도메인포워딩들의 목록을 보여줌
         **/
        function dispDfowardAdminContent() {
            $args->page = Context::get('page'); ///< 현재 페이지를 설정
            $args->list_count = 40; ///< 한페이지에 40개씩 보여주기로 고정.
            $args->page_count = 10; ///< 페이지의 수는 10개로 제한.

            // 등록된 도메인포워딩 목록을 가져옴
            $oDfowardModel = &getModel('dfoward');
            $output = $oDfowardModel->getDfowardList($args);

            if(count($output->data)) {
                foreach($output->data as $key => $val) {
                    if(!eregi("^http:\/\/",$val->target_url)) $output->data[$key]->target_url = "http://".$val->target_url;
                }
            }

            /**
             * 템플릿에 쓰기 위해서 context::set
             * xml query에 navigation이 있고 list_count가 정의되어 있으면 결과 변수에 아래 5가지의 값이 세팅이 된다.
             **/
            Context::set('total_count', $output->total_count);
            Context::set('total_page', $output->total_page);
            Context::set('page', $output->page);
            Context::set('dfoward_list', $output->data);
            Context::set('page_navigation', $output->page_navigation);

            // 템플릿 파일 지정 (./modules/dfoward/tpl/index.html파일이 지정이 됨)
            $this->setTemplateFile('index');
        }

        /**
         * @brief 선택된 도메인포워딩의 정보 출력
         **/
        function dispDfowardAdminInfo() {
            // dfoward_srl 값이 없다면 그냥 index 페이지를 보여줌
            $dfoward_srl = Context::get('dfoward_srl');
            if(!$dfoward_srl) return $this->dispDfowardAdminContent();

            $oDfowardModel = &getModel('dfoward');
            $dfoward_info = $oDfowardModel->getDfoward($dfoward_srl);
            Context::set('dfoward_info', $dfoward_info);


            // 템플릿 파일 지정
            $this->setTemplateFile('dfoward_info');
        }

    }
?>
