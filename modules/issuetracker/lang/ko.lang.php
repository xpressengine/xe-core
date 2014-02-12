<?php
    /**
     * @file   modules/issuetracker/lang/ko.lang.php
     * @author NHN (developers@xpressengine.com)
     * @brief  Issuetracker 모듈의 기본 언어팩
     **/

     $lang->issuetracker = '이슈트래커';
     $lang->about_issuetracker = '프로젝트 관리를 위한 계획표, 코드열람, 문제관리와 배포판을 관리할 수 있는 모듈입니다';

     $lang->cmd_project_list = '프로젝트 목록';
     $lang->cmd_view_info = '프로젝트 정보';
     $lang->cmd_project_setting = '프로젝트 설정';
     $lang->cmd_release_setting = '배포 설정';
     $lang->cmd_insert_package = '패키지 추가';
     $lang->cmd_insert_release = '배포 추가';
     $lang->cmd_attach_file = '파일 첨부';
     $lang->cmd_display_item = '대상 표시';

     $lang->cmd_resolve_as = '상태 변경';
     $lang->cmd_reassign = '소유자 변경';
     $lang->cmd_accept = '수락하기';

     $lang->svn_url = 'SVN 주소';
     $lang->about_svn_url = '프로젝트의 버전관리가 이루어지는 SVN 주소를 입력해주세요';
     $lang->svn_cmd = 'SVN 실행파일 위치';
     $lang->about_svn_cmd = 'SVN 연동을 위해 svn client 실행파일의 위치를 입력해주세요. (ex: /usr/bin/svn)';
     $lang->diff_cmd = 'DIFF 실행파일 위치';
     $lang->about_diff_cmd = 'SVN revision들의 비교를 위한 diff 실행파일의 위치를 입력해주세요. (ex: /usr/bin/diff)';
     $lang->svn_userid = 'SVN 인증 아이디';
     $lang->about_svn_userid = '인증이 필요한 경우 아이디를 입력해주세요';
     $lang->svn_passwd = 'SVN 인증 패스워드';
     $lang->about_svn_passwd = '인증이 필요한 경우 패스워드를 입력해주세요';

     $lang->issue = '문제';
     $lang->total_issue = '전체 문제';
     $lang->milestone = $lang->milestone_srl = '계획';
     $lang->priority = $lang->priority_srl = '우선순위';
     $lang->type = $lang->type_srl = '타입';
     $lang->component = $lang->component_srl = '구성요소';
     $lang->assignee = '소유자';
     $lang->status = '상태';
     $lang->action = '동작';
     $lang->display_option = '표시 옵션';

     $lang->history_format_not_source = '<span class="target">[target]</span> 으로 <span class="key">[key]</span> 변경';
     $lang->history_format = '<span class="source">[source]</span> 에서 <span class="target">[target]</span> 으로 <span class="key">[key]</span> 변경';

     $lang->project = '프로젝트';

     $lang->deadline = '완료기한';
     $lang->name = '이름';
     $lang->complete = '완료';
     $lang->completed_date = '완료일';
     $lang->order = '순서';
     $lang->package = $lang->package_srl = '패키지';
     $lang->release = $lang->release_srl = '배포판';
     $lang->release_note = '배포 기록';
     $lang->release_changes = '변경 사항';
     $lang->occured_version = $lang->occured_version_srl = '발생버전';
     $lang->attached_file = '첨부 파일';
     $lang->filename = '파일이름';
     $lang->filesize = '파일크기';

     $lang->status_list = array(
             'new' => '신규',
             'reviewing' => '검토중',
             'assign' => '할당',
             'resolve' => '해결',
             'reopen' => '재발',
             'postponed' => '보류',
             'duplicated' => '중복',
             'invalid' => '문제아님',
    );

     $lang->about_milestone = '개발계획을 설정합니다';
     $lang->about_priority = '우선순위를 설정합니다.';
     $lang->about_type = '문제의 타입를 설정합니다 (ex. 문제, 개선사항)';
     $lang->about_component = '문제의 대상 구성요소를 설정합니다';

     $lang->project_menus = array(
             'dispIssuetrackerViewMilestone' => '개발계획',
             'dispIssuetrackerViewIssue' => '이슈 열람',
             'dispIssuetrackerNewIssue' => '이슈 등록',
             'dispIssuetrackerTimeline' => '타임 라인',
             'dispIssuetrackerViewSource' => '코드 열람',
             'dispIssuetrackerDownload' => '다운로드',
             'dispIssuetrackerAdminProjectSetting' => '설정',
    );

	$lang->new_project_menus = array(
		'개발계획' => array('dispIssuetrackerViewMilestone'),
		'이슈' => array('dispIssuetrackerViewIssue', array(
			'이슈 열람' => array('dispIssuetrackerViewIssue'),
			'이슈 등록' => array('dispIssuetrackerNewIssue'))),
		'코드' => array('dispIssuetrackerViewSource', array(
			'코드 열람' => array('dispIssuetrackerViewSource'))),
		'타임라인' => array('dispIssuetrackerTimeline'),
		'다운로드' => array('dispIssuetrackerDownload'));

	$lang->mobile_it_menu = array(
		'dispIssuetrackerViewMilestone' => '개발계획',
		'dispIssuetrackerViewIssue' => '이슈 열람',
		'dispIssuetrackerNewIssue' => '이슈 등록',
		'dispIssuetrackerTimeline' => '타임라인');

    $lang->msg_not_attched = '파일을 첨부해주세요';
    $lang->msg_attached = '파일이 등록되었습니다';
    $lang->msg_no_releases = '등록된 배포판이 없습니다';

    $lang->cmd_document_do = '이 문제를.. ';
    $lang->not_assigned = '할당 안됨';
    $lang->not_assigned_description = '할당 안된 문제들의 목록입니다.';
    $lang->timeline_msg = array(
        'changed' => '이슈변경',
        'created' => '이슈생성'
    );

    $lang->cmd_manage_issue = '이슈 관리';
    $lang->msg_changes_from = '시작 날짜';
    $lang->duration = '기간';
    $lang->target_list = array(
        'issue_created' => '생성된 이슈',
        'issue_changed' => '변경된 이슈',
        'commit' => '소스변경'
        );

	$lang->not_using_repository = '저장소를 사용하지 않고 있습니다';
	$lang->revision = "리비전";
	$lang->repos_path = "저장소 경로";
	$lang->view_log = "로그 보기";
	$lang->compare_with_previous = "이전 버전과 비교";
	$lang->issue_id = "ID";
	$lang->cmd_detailed_search = "상세검색";
	$lang->about_total_count = "<em>%s</em> 개의 이슈가 있습니다.";
	$lang->startdate = "시작일";
	$lang->contributor = "공헌자";
	$lang->time = "시간";
	$lang->condition = "조건";
	$lang->parent_directory = "상위 디렉토리";
	$lang->cmd_compare = "비교";
	$lang->progress = "진행률";
	$lang->noissue = "등록된 이슈가 없습니다.";
	$lang->cmd_new_issue = "이슈 등록";
	$lang->more = "더보기";
    $lang->cmd_openclose = '열기/닫기';
    $lang->released_date = '배포 날짜';
    $lang->cmd_show_all_version = '지난 버전 모두 보기';
    $lang->cmd_hide_all_version = '지난 버전 숨기기';
?>
