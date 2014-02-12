<?php
    /**
     * @file   modules/lucene/lang/ko.lang.php
     * @author nhn (developers@xpressengine.com)
     * @brief  한국어 언어팩 (기본적인 내용만 수록)
     **/

    $lang->integration_search = '통합검색';

    $lang->sample_code = '샘플코드';
    $lang->about_target_module = '선택된 모듈만 검색 대상으로 정합니다. 권한설정에 대한 주의를 바랍니다.';
    $lang->about_sample_code = '위 코드를 레이아웃, 스킨 등에 추가하시면 통합검색이 가능합니다.';
    $lang->msg_no_keyword = '검색어를 입력해주세요.';
    $lang->msg_document_more_search  = '\'계속 검색\' 버튼을 선택하시면 아직 검색하지 않은 부분까지 계속 검색 하실 수 있습니다.';

    $lang->is_result_text = "<strong>'%s'</strong> 에 대한 검색결과 <strong>%d</strong>건";
    $lang->multimedia = '이미지/동영상';
    
    $lang->include_search_target = '선택된 대상만 검색';
    $lang->exclude_search_target = '선택된 대상을 검색에서 제외';
    $lang->uselucene = 'nLucene 사용';

    $lang->apiurl = 'nLucene 위치';
    $lang->is_search_option = array(
        'document' => array(
            'title_content' => '제목+내용',
            'title' => '제목',
            'content' => '내용',
            'tag' => '태그',
        ),
        'trackback' => array(
            'url' => '대상 URL',
            'blog_name' => '대상 사이트 이름',
            'title' => '제목',
            'excerpt' => '내용',
        ),
    );

    $lang->is_sort_option = array(
        'regdate' => '등록일',
        'comment_count' => '댓글 수',
        'readed_count' => '조회 수',
        'voted_count' => '추천 수',
    );
    $lang->cmd_indexsetup = '색인 설정';
    $lang->cmd_searchsetup = '검색 설정';
	$lang->cmd_indices_status = '색인 현황';
    $lang->service_name = '색인 이름';
    $lang->repo_path = '색인 경로';
    $lang->renew_interval = '색인 갱신 간격';
    $lang->about_renew_interval = '색인이 갱신되는 간격입니다. 분 단위로 입력해주시면 됩니다.';
    $lang->about_repo_path = 'nLucene 서비스가 실제로 색인을 저장하는 경로입니다.';
    $lang->about_db_whether_lucene = '기존 통합검색을 사용하실지 루씬을 사용하실지 선택하실 수 있습니다.';
    $lang->about_apiurl = 'nLucene이 설치된 URL을 http://도메인:포트/ 형태로 입력합니다. 기본 포트번호는 5001 입니다.';
    $lang->about_service_name_prefix = 'nLucene 내에서 만들어질 색인의 이름입니다. 영문으로 입력하셔야 합니다.';
    $lang->about_set_apiurl_first1 = '색인 설정을 하시기 전에 ';
    $lang->about_set_apiurl_first2 = '를 먼저 설정해 주셔야 합니다.';
    $lang->about_uselucene = '글과 댓글의 검색을 위해 nLucene을 사용합니다.';
    $lang->set_for_nlucene = '검색 설정';
    $lang->about_config_share = "Naver Lucene 모듈은 nLucene 서버 및 색인 생성에 대한 설정만 가능합니다.\n스킨 설정이나 검색 대상 모듈에 대한 설정은 통합 검색 모듈의 설정을 공유합니다.\n이러한 설정을 변경하실 때에는 통합 검색 모듈의 관리자 페이지를 이용해 주세요.";
?>
