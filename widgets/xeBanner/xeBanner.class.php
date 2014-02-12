<?php
    /**
     * @class xe_news
     * @author zero (zero@nzeo.com)
     * @brief XE공식사이트의 배너 위젯
     * @version 0.1
     **/

    class xeBanner extends WidgetHandler {

        /**
         * @brief 위젯의 실행 부분
         *
         * ./widgets/위젯/conf/info.xml 에 선언한 extra_vars를 args로 받는다
         * 결과를 만든후 print가 아니라 return 해주어야 한다
         **/
        function proc($args) {

            // 위젯 변수 설정
            $widget_info->title_1 = $args->banner_title_1;
            $widget_info->title_2 = $args->banner_title_2;
            $widget_info->title_3 = $args->banner_title_3;
            $widget_info->title_4 = $args->banner_title_4;
            
            $c=0;
            if($widget_info->title_1) $c++;
            if($widget_info->title_2) $c++;
            if($widget_info->title_3) $c++;
            if($widget_info->title_4) $c++;
 
            $widget_info->description_1 = $args->banner_description_1;
            $widget_info->description_2 = $args->banner_description_2;
            $widget_info->description_3 = $args->banner_description_3;
            $widget_info->description_4 = $args->banner_description_4;

            $widget_info->url_1 = $args->banner_url_1;
            if(!$widget_info->url_1) $widget_info->url_1 = '#';
            else if(!preg_match('/^http/i',$widget_info->url_1)) $widget_info->url_1 = 'http://'.$widget_info->url_1;
            $widget_info->url_2 = $args->banner_url_2;
            if(!$widget_info->url_2) $widget_info->url_2 = '#';
            else if(!preg_match('/^http/i',$widget_info->url_2)) $widget_info->url_2 = 'http://'.$widget_info->url_2;
            $widget_info->url_3 = $args->banner_url_3;
            if(!$widget_info->url_3) $widget_info->url_3 = '#';
            else if(!preg_match('/^http/i',$widget_info->url_3)) $widget_info->url_3 = 'http://'.$widget_info->url_3;
            $widget_info->url_4 = $args->banner_url_4;
            if(!$widget_info->url_4) $widget_info->url_4 = '#';
            else if(!preg_match('/^http/i',$widget_info->url_4)) $widget_info->url_4 = 'http://'.$widget_info->url_4;


            $banner_ko = $args->banner_ko;
            $default_banner = $banner_ko;
            $banner_en = $args->banner_en;
            if(!$default_banner && $banner_en) $default_banner = $banner_en;
            $banner_jp = $args->banner_jp;
            if(!$default_banner && $banner_jp) $default_banner = $banner_jp;
            $banner_zh = $args->banner_zh;
            if(!$default_banner && $banner_zh) $default_banner = $banner_zh;
            switch(Context::getLangType()) {
                case 'jp' :
                        $banner = $banner_jp;
                    break;
                case 'zh-TW' :
                case 'zh-CN' :
                        $banner = $banner_zh;
                    break;
                case 'en' :
                        $banner = $banner_en;
                    break;
                default : 
                        $banner = $banner_ko;
                    break;
            }
            if(!$banner) $banner = $default_banner;
            $widget_info->banner_image = $banner;
            Context::set('widget_info', $widget_info);

            // 배너 이미지를 header에 추가
            if($widget_info->banner_image) Context::addHtmlHeader('<style type="text/css"> .xeBanner .section { background:url('.$widget_info->banner_image.') no-repeat;} </style>');

            // 1~3중 순서대로 처리
            if(!$_COOKIE['xb']) $_COOKIE['xb']=0;
            $_COOKIE['xb']++;
            setcookie('xb',$_COOKIE['xb']%$c);
            Context::set('xb',$_COOKIE['xb']);

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
