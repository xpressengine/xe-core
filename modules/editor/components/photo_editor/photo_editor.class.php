<?php
    /**
     * @class  NaverLab Photo Editor
     * @author celdi (celdi77@gmail.com), zero
     * @brief  네이버랩 포토 에디터 API
     **/

    class photo_editor extends EditorHandler {

        var $editor_sequence = 0;
        var $component_path = '';

        var $api_url = 'http://s.lab.naver.com/pe/service';

        /**
         * @brief editor_sequence과 컴포넌트의 경로를 받음
         **/
        function photo_editor($editor_sequence, $component_path) {
            $this->editor_sequence = $editor_sequence;
            $this->component_path = $component_path;
        }

        /**
         * @brief naver lab에서 보내주는 image url 받기
         **/
        function exportImage() {
            $oFileModel = &getModel('file');
            $oFileController = &getController('file');

            $file = Context::get('file');
            $editor_sequence = Context::get('editor_sequence');
            $module_info = Context::get('current_module_info');

            if(!$file || $_SESSION['upload_info'][$editor_sequence]->enabled!==true) return new Object(-1,'msg_invalid_request');

            // 업로드 권한이 없거나 정보가 없을시 종료
            if(!$_SESSION['upload_info'][$editor_sequence]->enabled) return new Object(-1,'msg_not_permitted');

            // upload_target_srl 구함
            $upload_target_srl = $_SESSION['upload_info'][$editor_sequence]->upload_target_srl;

            // 세션정보에도 정의되지 않았다면 새로 생성
            if(!$upload_target_srl) $_SESSION['upload_info'][$editor_sequence]->upload_target_srl = $upload_target_srl = getNextSequence();

            // 임시로 파일을 다운 받음
            $target_filename = _XE_PATH_.'files/cache/tmp/photo_editor_'.$upload_target_srl;
            FileHandler::getRemoteFile($file, $target_filename);

            // 파일 업로드 진행
            $file_info['tmp_name'] = $target_filename;
            $file_info['name'] = time();
            list($width, $height, $type, $attrs) = @getimagesize($target_filename);
            if($width<1 || $height<1) return new Object(-1,'msg_invalid_request');
            switch($type) {
                case '1' :
                        $type = 'gif';
                    break;
                case '2' :
                        $type = 'jpg';
                    break;
                case '3' :
                        $type = 'png';
                    break;
                default :
                    return new Object(-1,'msg_invalid_request');
                break;
            }
            $file_info['name'] .= '.'.$type;
            $output = $oFileController->insertFile($file_info, $module_info->module_srl, $upload_target_srl,0,true);
            if(!$output->toBool()) return $output;

            Context::set('image_file', $output->get('uploaded_filename'));
            Context::set('image_width', $width);
            Context::set('image_height', $height);
            Context::set('editor_sequence', $editor_sequence);

            // 입력된 이미지를 내용에 입력하고 창 닫기
            $tpl_path = $this->component_path;
            $tpl_file = 'insert.html';
            $oTemplate = &TemplateHandler::getInstance();
            print $oTemplate->compile($tpl_path, $tpl_file);
            Context::close();
            exit();
        }

        /**
         * @brief popup window요청시 popup window에 출력할 내용을 추가하면 된다
         **/
        function getPopupContent() {
            Context::set('api_url', $this->api_url);
            Context::set('editor_sequence', $this->editor_sequence);
            Context::loadLang($this->component_path.'lang');

            // 업로드 권한이 없거나 정보가 없으면 에러 메세지 출력
            if(!$_SESSION['upload_info'][$this->editor_sequence]->enabled) Context::set('disable_to_file_upload', true);

            // 템플릿을 미리 컴파일해서 컴파일된 소스를 return
            $tpl_path = $this->component_path;
            $tpl_file = 'popup.html';
            $oTemplate = &TemplateHandler::getInstance();
            return $oTemplate->compile($tpl_path, $tpl_file);
        }
    }
?>
