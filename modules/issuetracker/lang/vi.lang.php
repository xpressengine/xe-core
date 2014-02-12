<?php
/*			░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░
			░░  * @File   :  common/lang/vi.lang.php                                              ░░
			░░  * @Author :  NHN (developers@xpressengine.com)                                                 ░░
			░░  * @Trans  :  Đào Đức Duy (ducduy.dao.vn@vietxe.net)								  ░░
			░░	* @Website:  http://vietxe.net													  ░░
			░░  * @Brief  :  Vietnamese Language Pack (Only basic words are included here)        ░░
			░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░░	   		*/

     $lang->issuetracker = 'Issue Tracker';
	 $lang->about_issuetracker = 'Issue Tracker quản lý những mốc, mã, Release và Issue.';

     $lang->cmd_project_list = 'Danh sách Project';
     $lang->cmd_view_info = 'Thông tin Project';
     $lang->cmd_project_setting = 'Thiết lập Project';
     $lang->cmd_release_setting = 'Thiết lập Issue';
     $lang->cmd_insert_package = 'Thêm gói mới';
     $lang->cmd_insert_release = 'Thêm Release';
     $lang->cmd_attach_file = 'File đính kèm';
     $lang->cmd_display_item = 'Hiển thị danh mục';

     $lang->cmd_resolve_as = 'Sửa đổi trạng thái';
     $lang->cmd_reassign = 'Sửa đổi quyền';
     $lang->cmd_accept = 'Đồng ý';

     $lang->svn_url = 'SVN URL';
     $lang->about_svn_url = "Xin hãy nhập SVN URL nơi phiên bản của những Project được quản lý.";
     $lang->svn_cmd = 'Thư mục định vị SVN';
     $lang->about_svn_cmd = 'Xin hãy nhập thư mục định vị đã liên kết với SVN. (Ví dụ: /usr/bin/svn)';
     $lang->diff_cmd = 'Thư mục định vị DIFF';
     $lang->about_diff_cmd = 'Xin hãy nhập thư mục định vị của DIFF để trả lời cho SVN. (Ví dụ: /usr/bin/diff)';
     $lang->svn_userid = 'SVN ID';
     $lang->about_svn_userid = 'Nếu cần thiết xác nhận, hãy nhập ID đăng nhập để truy cập SVN.';
     $lang->svn_passwd = 'Mật khẩu SVN';
     $lang->about_svn_passwd = 'Nếu cần thiết xác nhận, hãy nhập mật khẩu đăng nhập để truy cập SVN.';

     $lang->issue = 'Issue';
     $lang->total_issue = 'Tất cả Issue';
     $lang->milestone = $lang->milestone_srl = 'Mốc';
     $lang->priority = $lang->priority_srl = 'Quyền ưu tiên';
     $lang->type = $lang->type_srl = 'Định dạng';
     $lang->component = $lang->component_srl = 'Component';
     $lang->assignee = 'Người được quyền';
     $lang->status = 'Trạng thái';
     $lang->action = 'Hoạt động';
     $lang->display_option = 'Hiển thị tùy chọn';

     $lang->history_format_not_source = '<span class="key">[key]</span> Điều chỉnh tới <span class="target">[target]</span>';
     $lang->history_format = '<span class="key">[key]</span> Điều chỉnh từ <span class="source">[source]</span> to <span class="target">[target]</span>';

     $lang->project = 'Project';

     $lang->deadline = 'Hạn cuối';
     $lang->name = 'Tên';
     $lang->complete = 'Hoàn thành';
     $lang->completed_date = 'Ngày hoàn thành';
     $lang->order = 'Xếp loại';
     $lang->package = $lang->package_srl = 'Gói';
     $lang->release = $lang->release_srl = 'Release';
     $lang->release_note = 'Release ghi chú';
     $lang->release_changes = 'Release thay đổi';
     $lang->occured_version = $lang->occured_version_srl = 'Đã có phiên bản mới';
     $lang->attached_file = 'File đính kèm';
     $lang->filename = 'Tên File';
     $lang->filesize = 'Dung lượng';

     $lang->status_list = array(
             'new' => 'Mới',
             'reviewing' => 'Tổng quan',
             'assign' => 'Ấn định',
             'resolve' => 'Quyết định',
             'reopen' => 'Mở lại',
             'postponed' => 'Hoãn',
             'duplicated' => 'Nhân đôi',
             'invalid' => 'Thất bại',
    );

     $lang->about_milestone = 'Thiết lập mốc.';
     $lang->about_priority = 'Thiết lập quyền ưu tiên.';
     $lang->about_type = 'Chọn định dạng của Issue (Ví dụ: issue, development)';
     $lang->about_component = 'Thiết lập Components của Issue';

     $lang->project_menus = array(
             'dispIssuetrackerViewIssue' => 'Xem Issue',
             'dispIssuetrackerNewIssue' => 'Thêm Issue',
             'dispIssuetrackerViewMilestone' => 'Mốc',
             'dispIssuetrackerTimeline' => 'Timeline',
             'dispIssuetrackerViewSource' => 'Xem nguồn',
             'dispIssuetrackerDownload' => 'Download',
             'dispIssuetrackerAdminProjectSetting' => 'Thiết lập',
    );

    $lang->msg_not_attched = 'Không có File đính kèm nào';
    $lang->msg_attached = 'File đã được đính kèm';
    $lang->msg_no_releases = 'Không có Release nào được đăng kí';

    $lang->cmd_document_do = 'Bình chọn / Phê bình';
    $lang->not_assigned = 'Không ấn định';
    $lang->not_assigned_description = 'Danh sách Issue không ấn định.';
    $lang->timeline_msg = array(
        'changed' => 'Đã thay đổi',
        'created' => 'Đã tạo'
    );
    $lang->cmd_manage_issue = 'Quản lý Issue';
    $lang->msg_changes_from = 'Xem những thay đổi';
    $lang->duration = 'Khoảng thời gian';
    $lang->target_list = array(
        'issue_created' => 'Issue đã tạo',
        'issue_changed' => 'Issue đã thay đổi',
        'commit' => 'Kiểm tra kho chứa'
        );
	$lang->not_using_repository = 'Dự án này không sử dụng kho chứa.';
	$lang->revision = "Xem lại";
	$lang->repos_path = "Đường dẫn lưu trữ";
	$lang->view_log = "Xem bản ghi";
	$lang->compare_with_previous = "So sánh với phiên bản trước";
	$lang->issue_id = "ID";
	$lang->cmd_detailed_search = "Tìm kiếm nâng cao";
	$lang->about_total_count = "<em>%s</em> Issues đã có sẵn.";
	$lang->new_project_menus = array(
		'Kế hoạch phát triển' => array('dispIssuetrackerViewMilestone'),
		'Issues' => array('dispIssuetrackerViewIssue', array(
			'Danh sách Issue' => array('dispIssuetrackerViewIssue'),
			'Issue mới' => array('dispIssuetrackerNewIssue'))),
		'Tài nguyên' => array('dispIssuetrackerViewSource', array(
			'Xem tài nguyên' => array('dispIssuetrackerViewSource'))),
		'Thời gian biểu' => array('dispIssuetrackerTimeline'),
		'Tải về' => array('dispIssuetrackerDownload'));
	$lang->startdate = "Ngày bắt đầu";
	$lang->contributor = "Người đóng góp";
	$lang->time = "Thời gian";
	$lang->condition = "Điều kiện";
	$lang->parent_directory = "Thư mục chính";
	$lang->cmd_compare = "So sánh";
	$lang->progress = "Tiến độ";
	$lang->noissue = "Không có Issue nào tồn tại.";
	$lang->cmd_new_issue = "Issue mới";
	$lang->more = "Xem thêm";
    $lang->cmd_openclose = 'open / close';
    $lang->released_date = 'released date';
    $lang->cmd_show_all_version = 'show all versions';
    $lang->cmd_hide_all_version = 'hide all versions';
?>
