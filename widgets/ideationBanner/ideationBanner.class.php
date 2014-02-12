<?php
    /**
     * @class ideationBanner
     * @author zero (zero@nzeo.com)
     * @brief 공지 형식의 배너
     * @version 0.1
     **/

    class ideationBanner extends WidgetHandler {

        /**
         * @brief 위젯의 실행 부분
         *
         * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
         * 결과를 만든후 print가 아니라 return 해주어야 한다
         **/
        function proc($args) {
            // 위젯 변수 설정
            for($i=1;$i<=3;$i++) {
                if(!$args->{'banner_sub_title_'.$i}) continue;
		unset($obj);
                $obj->sub_title = $args->{'banner_sub_title_'.$i};
                $obj->title = $args->{'banner_title_'.$i};
                $obj->description = $args->{'banner_description_'.$i};
                $obj->url_title = $args->{'banner_url_title_'.$i};
                $obj->url = $args->{'banner_url_'.$i};
                $widget_info->list[] = $obj;
            }
            Context::set('widget_info', $widget_info);

            // 템플릿의 스킨 경로를 지정 (skin, colorset에 따른 값을 설정)
            $tpl_path = sprintf('%sskins/%s', $this->widget_path, $args->skin);
            Context::set('colorset', $args->colorset);

            // 템플릿 파일을 지정
            $tpl_file = 'banner';

            // 템플릿 컴파일
            $oTemplate = &TemplateHandler::getInstance();
            return $oTemplate->compile($tpl_path, $tpl_file);
        }
    }
?>
