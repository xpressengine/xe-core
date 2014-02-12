<?php
    /**
     * @file   modules/issuetracker/lang/zh-TW.lang.php
     * @author NHN (developers@xpressengine.com) 翻譯：royallin
     * @brief  問題追蹤(Issuetracker)模組正體中文語言
     **/

     $lang->issuetracker = '問題追蹤';
     $lang->about_issuetracker = '版本管理，原始碼，問題與發佈等問題追蹤。';

     $lang->cmd_project_list = '專案清單';
     $lang->cmd_view_info = '專案資訊';
     $lang->cmd_project_setting = '專案設置';
     $lang->cmd_release_setting = '發佈設置';
     $lang->cmd_insert_package = '新增套裝軟體';
     $lang->cmd_insert_release = '新增發佈版';
     $lang->cmd_attach_file = '新增附加檔案';
     $lang->cmd_display_item = '顯示專案';

     $lang->cmd_resolve_as = '修改狀態';
     $lang->cmd_reassign = '修改所有者';
     $lang->cmd_accept = '接受';

     $lang->svn_url = 'SVN 位址';
     $lang->about_svn_url = '請輸入專案的 SVN 位址。';
     $lang->svn_cmd = 'SVN 應用程式位置';
     $lang->about_svn_cmd = '請輸入 SVN Client 應用程式位置。(例: /usr/bin/svn)';
     $lang->diff_cmd = 'DIFF 應用程式位置';
     $lang->about_diff_cmd = '為了比較 SVN 版本，請輸入 diff 應用程式位置。(例: /usr/bin/diff)';
     $lang->svn_userid = 'SVN 帳號';
     $lang->about_svn_userid = '必須要驗證時，請輸入帳號來登入 SVN 檔案庫';
     $lang->svn_passwd = 'SVN 密碼';
     $lang->about_svn_passwd = '必須要驗證時，請輸入密碼來登入 SVN 檔案庫';

     $lang->issue = '問題';
     $lang->total_issue = '所有問題';
     $lang->milestone = $lang->milestone_srl = '版本';
     $lang->priority = $lang->priority_srl = '優先順序';
     $lang->type = $lang->type_srl = '種類';
     $lang->component = $lang->component_srl = '組件';
     $lang->assignee = '所有者';
     $lang->status = '狀態';
     $lang->action = '動作';
     $lang->display_option = '顯示選項';

     $lang->history_format_not_source = '<span class="key">[key]</span>修改為<span class="target">[target]</span>';
     $lang->history_format = '<span class="key">[key]</span>，從<span class="source">[source]</span>修改為<span class="target">[target]</span>';

     $lang->project = '專案';

     $lang->deadline = '完成期限';
     $lang->name = '名稱';
     $lang->complete = '完成';
     $lang->completed_date = '結束日期';
     $lang->order = '順序';
     $lang->package = $lang->package_srl = '套裝軟體';
     $lang->release = $lang->release_srl = '發佈版';
     $lang->release_note = '發佈記錄';
     $lang->release_changes = '更新日誌';
     $lang->occured_version = $lang->occured_version_srl = '目前版本';
     $lang->attached_file = '附加檔案';
     $lang->filename = '檔案名稱';
     $lang->filesize = '檔案大小';

     $lang->status_list = array(
             'new' => '新建',
             'reviewing' => '審查',
             'assign' => '分配',
             'resolve' => '解決',
             'reopen' => '重新開始',
             'postponed' => '保留',
             'duplicated' => '重複',
             'invalid' => '無效',
    );

     $lang->about_milestone = '設置開發計劃。';
     $lang->about_priority = '設置優先順序。';
     $lang->about_type = '設置問題種類。 (例如：問題，改善項目)';
     $lang->about_component = '設置問題組件。';

     $lang->project_menus = array(
             'dispIssuetrackerViewMilestone' => '版本開發',
             'dispIssuetrackerViewIssue' => '問題清單',
             'dispIssuetrackerNewIssue' => '發表問題',
             'dispIssuetrackerTimeline' => '時間軸',
             'dispIssuetrackerViewSource' => '檢視原始碼',
             'dispIssuetrackerDownload' => '下載',
             'dispIssuetrackerAdminProjectSetting' => '設置',
    );

	$lang->new_project_menus = array(
		'開發計畫' => array('dispIssuetrackerViewMilestone'),
		'問題' => array('dispIssuetrackerViewIssue', array(
			'問題清單' => array('dispIssuetrackerViewIssue'),
			'發表問題' => array('dispIssuetrackerNewIssue'))),
		'原始碼' => array('dispIssuetrackerViewSource', array(
			'檢視' => array('dispIssuetrackerViewSource'))),
		'時間軸' => array('dispIssuetrackerTimeline'),
		'下載' => array('dispIssuetrackerDownload'));

	$lang->mobile_it_menu = array(
		'dispIssuetrackerViewMilestone' => '開發計畫',
		'dispIssuetrackerViewIssue' => '問題清單',
		'dispIssuetrackerNewIssue' => '發表問題',
		'dispIssuetrackerTimeline' => '時間軸');

    $lang->msg_not_attched = '請新增附檔。';
    $lang->msg_attached = '檔案已新增。';
    $lang->msg_no_releases = '尚未新增的發佈版本。';

    $lang->cmd_document_do = '將此問題...';
    $lang->not_assigned = '尚未分配';
    $lang->not_assigned_description = '尚未分配的問題';
    $lang->timeline_msg = array(
        'changed' => '變更',
        'created' => '建立'
    );

    $lang->cmd_manage_issue = '問題管理';
    $lang->msg_changes_from = '開始日期';
    $lang->duration = '期間';
    $lang->target_list = array(
        'issue_created' => '建立問題',
        'issue_changed' => '變更問題',
        'commit' => '檔案更新'
        );

	$lang->not_using_repository = '此專案未使用檔案庫。';
	$lang->revision = "版本";
	$lang->repos_path = "檔案庫路徑";
	$lang->view_log = "檢視日誌";
	$lang->compare_with_previous = "與舊版本比較";
	$lang->issue_id = "ID";
	$lang->cmd_detailed_search = "進階搜尋";
	$lang->about_total_count = "總共有 <em>%s</em> 個問題";
	$lang->startdate = "建立日期";
	$lang->contributor = "貢獻者";
	$lang->time = "時間";
	$lang->condition = "條件";
	$lang->parent_directory = "根目錄";
	$lang->cmd_compare = "比較";
	$lang->progress = "進展";
	$lang->noissue = "無任何問題";
	$lang->cmd_new_issue = "發表問題";
	$lang->more = "More";
    $lang->cmd_openclose = 'open / close';
    $lang->released_date = 'released date';
    $lang->cmd_show_all_version = 'show all versions';
    $lang->cmd_hide_all_version = 'hide all versions';
?>
