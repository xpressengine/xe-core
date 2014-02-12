<?php
    /**
     * @file   zh-TW.lang.php
     * @author NHN (developers@xpressengine.com) 翻譯：royallin
     * @brief  部落格 (Textyle) 模組正體中文語言
     **/

    $lang->textyle = 'Textyle';
    $lang->about_textyle = 'Textyle是運作於 XpressEngine 的部落格模組。';
    $lang->msg_requried_version = "Textyle需要 XE Core %s 以上的版本。";

	$lang->cmd_open_id = 'OpenID 登入';
	$lang->cmd_common_id = '一般登入';
	$lang->msg_create_textyle = '已建立 Textyle';
	$lang->init_category_title = '預設分類';
	$lang->add_category = '新增分類';
    $lang->textyle_admin = 'Textyle 管理員(ID)';
    $lang->textyle_title = 'Textyle 標題';
    $lang->today_visitor = '今日 <strong>訪問者數</strong>';
    $lang->today_comments = '今日 <strong>評論數</strong>';
    $lang->today_trackbacks = '今日 <strong>引用/通告數</strong>';
    $lang->textyle_summary = '部落格概要';
    $lang->item_unit = '篇';
    $lang->newest_documents ='最新文章';
    $lang->newest_no_documents ='尚未寫任何文章。寫些文章吧!';
    $lang->posts = '文章數量';
    $lang->newest_materials = '最新素材';
	$lang->newest_no_materials = '目前沒有素材，<a href="%s">安裝素材蒐集器</a>';
	$lang->newest_comments = '最新迴響';
	$lang->newest_no_comments = '目前沒有迴響。';
	$lang->newest_guestbooks = '最新留言';
	$lang->newest_no_guestbooks = '目前沒有留言。';
	$lang->reserve = '預訂';
	$lang->publish = '發佈';
    $lang->publish_go = '發佈...';
    $lang->publish_update_go = '再發佈...';
    $lang->published = '已發佈';
    $lang->publish_option = '發佈選項';
	$lang->publish_this_blog = '發佈此部落格';
    $lang->ask_time_publish = '是否要立刻發佈或更新這篇文章?';
    $lang->ask_micro_publish = '是否要發佈到微網誌?';
    $lang->noti_publish = '發佈通知';
    $lang->send_trackback = '發送引用';
    $lang->add_input_trackback = '新增引用位址';
    $lang->saved = '儲存';
    $lang->temp = '臨時';
    $lang->more = '更多';
    $lang->last_week = '上週';
    $lang->this_week = '本週';
    $lang->display_name = '顯示名稱';
    $lang->about_display_name = '如果沒有輸入顯示名稱，將會以帳號顯示。';
    $lang->about_email = '如果沒有輸入電子郵件，當您忘記密碼時將無法找回。';
    $lang->no_profile_image = '沒有個人圖片。';
    $lang->allow_profile_image_type = '允許上傳 <strong>jpg, gif, png</strong> 等檔案。';
    $lang->allow_profile_image_size = '圖片大小會自動調整成 <strong>%dx%d</strong> px(像素)。';
    $lang->signature = '自我介紹';
    $lang->default_config = '基本設定';
    $lang->blog_title = '部落格標題';
    $lang->about_blog_title = '為自己的部落格取個名字吧';
    $lang->blog_description = '部落格簡介';
    $lang->about_blog_description = '是否顯示簡介取決於面板，或者可藉由 HTML&middot;CSS 編輯顯示。';
    $lang->favicon = '網站圖示';
    $lang->registed_favicon = '上傳網站圖示';
    $lang->about_favicon = '可上傳的檔案大小 <strong>16x16px</strong> <strong>ico</strong> 格式。 ';
    $lang->about_addon = '애드온은 html결과물을 출력하기 보다 동작을 제어하는 역할을 합니다. 원하시는 애드온을 on/ off하시는 것만으로 사이트 운영에 유용한 기능을 연동할 수 있습니다.';
    $lang->addon_using = '使用';
    $lang->lang_time_zone = '語言/時區';
    $lang->language = '語言';
    $lang->timezone = '時區';
    $lang->edit_style = '選擇編輯器';
    $lang->textyle_editor = '段落編輯器';
    $lang->font_style = '字型設定';
    $lang->font_family = '字型種類';
    $lang->font_size = '字型大小';
    $lang->about_font_family = '可選擇內容預設字型。(例:%s)';
    $lang->font_family_list = array(
		'돋움, Dotum'=> 'Dotum',
		'굴림, Gulim'=> 'Gulim',
		'바탕, Batang'=> 'Batang',
		'맑은 고딕, Malgun Gothic'=> 'Malgun Gothic',
		'나눔고딕, NanumGothic'=> 'Nanum Gothic',
		'나눔명조, NanumMyeongjo'=> 'Nanum Myeongjo'
	);
    $lang->about_font_size = '可選擇預設字型大小(例: 12px 或 1em - 必須要有單位)';
    $lang->about_textyle_editor = 'Textyle 預設編輯器：可分別編輯文章段落';
    $lang->etc_editor = '其他編輯器';
    $lang->about_etc_editor = '所見即得編輯器';
    $lang->set_editor_components = '編輯器元件設定';
    $lang->set_prefix = '頁首設定';
    $lang->about_prefix = '每篇主題都會包含以下內容。(可使用 HTML)';
    $lang->set_suffix = '頁尾設定';
    $lang->about_suffix = '每篇主題都會包含以下內容。(可使用 HTML)';
    $lang->blogapi = 'Blog API';
    $lang->blogapi_service = 'Blog API 功能';
    $lang->about_blogapi_service = 'Blog API를 이용한 원격 발행을 지원하는 서비스를 선택하세요.<br/>서비스에 없을 경우 직접 입력을 선택하시면 됩니다';
    $lang->blogapi_hosted = '서비스 블로그';
    $lang->blogapi_custom = '직접 입력';
    $lang->blogapi_host_provider = '서비스 블로그 제공자';
    $lang->about_blogapi_host_provider = '등록을 원하는 서비스 제공자를 선택하세요';
    $lang->blogapi_type = 'API 類型';
    $lang->about_blogapi_type = '등록하고자 하는 BlogAPI 의 형식을 선택하셔야 합니다';
    $lang->blogapi_support = 'Blog API(Meta Weblog API)를 이용한 원격 발행이 가능합니다.';
    $lang->blogapi_example = '例) Window Live Writer, Google Docs, MS Word 2007 等';
    $lang->not_permit_blogapi = 'API 연결확인이 되지 않았습니다.';
    $lang->blogapi_url = 'API URL';
	$lang->cmd_textyle_blogapi_service = 'Blog API 服務設定';
	$lang->api_type = 'API 類型';
	$lang->service_name = 'API 服務名稱';
	$lang->url_description = 'API 位址輸入方式';
	$lang->id_description = 'API 名稱輸入方式';
	$lang->password_description = 'API 密碼輸入方式';
    $lang->target_site_url = '網站位址';
    $lang->blog_first_page = '部落格首頁';
    $lang->blog_display_target = '顯示目標';
    $lang->content_body = '本文';
    $lang->content_summary = '摘要';
    $lang->content_list = '列表';
    $lang->blog_display_count = '文章數量';
    $lang->feed_format = 'Feed 類型';
    $lang->rss_type = 'RSS 類型';
    $lang->rss_total = '主題 + 所有內容';
    $lang->rss_summary = '主題 + 摘要';
    $lang->visitor_editor_style = '迴響與留言板樣式';
    $lang->host = '主機';
    $lang->referer = '訪問路徑';
    $lang->link_word = '關鍵字連結';
    $lang->link_document = '文章連結';
    $lang->visitor = '訪客';
    $lang->about_referer = '可檢視訪客的訪問路徑';
    $lang->website_address = '網址';
    $lang->required = '必填';
    $lang->selected = '選填';
    $lang->notaccepted = '不使用';
    $lang->comment_editor = '回覆編輯器';
    $lang->guestbook_editor = '留言板編輯器';
    $lang->comment_list_count = '每頁回覆數量';
    $lang->guestbook_list_count = '每頁留言數量';
    $lang->comment_grant = '回覆權限';
    $lang->about_comment_grant = '可設定只限會員或不限制';
    $lang->disable_comment= '無法回覆，請先登入。';
    $lang->grant_to_all = '不限制';
    $lang->grant_to_member = '會員';
    $lang->guestbook_grant = '留言權限';
    $lang->about_guestbook_grant = '可設定只限會員或不限制';
    $lang->disable_guestbook = '無法留言，請先登入。';
    $lang->current_password = '目前密碼';
    $lang->textyle_password1 = '新密碼';
    $lang->textyle_password2 = '確認新密碼';
    $lang->about_change_password = '請再次輸入新密碼';
    $lang->name_nick = '名稱(暱稱)';
    $lang->manage_trackback ='引用管理';
    $lang->manage_guestbook ='留言板管理';
    $lang->manage_comment ='回覆管理';
    $lang->document_list ='文章列表';
    $lang->type = '類型';
    $lang->trackback_site = '引用/ 網站';
    $lang->total_result_count = '全部 <strong>%d</strong>';
    $lang->search_result_count = '搜尋結果 <strong>%d</strong> ';
    $lang->no_result_count = '查無結果';
    $lang->selected_articles = '選擇項目';
    $lang->avatar = 'Avatar';
    $lang->status = '狀態';
    $lang->pingback = '通告';
    $lang->recent_tags = '最近使用的標籤';
    $lang->total_tag_result = '總共使用 <strong>%d</strong> 個標籤。';
    $lang->align_by_char = 'Alphabetical order가나다순 정렬';
    $lang->used_documents = 'Number of Occurrences';
    $lang->order_desc = '내림차순 정렬';
    $lang->update_tag = '修改/刪除標籤';
    $lang->tag_name = '標籤';
    $lang->tag_with_tags = '同組標籤';
    $lang->total_materials = 'Total <strong>%d</strong> ingredients are stored.';
    $lang->none_materials = 'There is no ingredient.';
    $lang->install_bookmarklet = '安裝素材蒐集器！';
    $lang->none_tags = '沒有標籤';
    $lang->bookmarklet_install = '安裝書籤';
	$lang->about_bookmarklet = '隨時隨地蒐集';
    $lang->about_set_bookmarklet = '素材蒐集器';
    $lang->data_export = '資料備份';
    $lang->data_import = '資料匯入';
    $lang->migration_prepare = '檔案分析中';
    $lang->data_import_progress = '匯入進度';
    $lang->data_export_progress = '匯出進度';
    $lang->about_export_xexml = '可使用 XpressEngine 專用的 XML(XE XML) 檔案格式匯入資料。';
    $lang->about_export_ttxml = '可使用 Textcube 專用的 XML(TTXML) 檔案格式匯入資料。';
    $lang->migration_file_path = 'XML 檔案 (URL或路徑)';
    $lang->msg_migration_file_is_null = '請輸入 XML 檔案 URL 或路徑。';
    $lang->cmd_import = '匯入';
    $lang->send_me2 = '發佈到 Me2Day';
    $lang->about_send_me2 = '寫篇文章，將標題發佈到 Me2Day 吧。';
    $lang->me2day_userid = 'Me2Day ID';
    $lang->about_me2day_userid = '輸入 Me2Day ID "http://me2day.net/ID"';
    $lang->me2day_userkey = 'Me2Day UserKey';
    $lang->about_me2day_userkey = '輸入 Me2Day User key';
    $lang->check_me2day_info = '檢查連線';
    $lang->msg_success_to_me2day = '連線成功，所輸入的資料正確。';
    $lang->msg_fail_to_me2day = '連線失敗，請檢查 ID 和 User Key';

    $lang->send_twitter = '發佈到 Twitter';
    $lang->about_send_twitter = '將標題發佈到 Twitter 吧。';
    $lang->twitter_userid = 'Twitter ID';
    $lang->about_twitter_userid = '輸入 Twitter ID';
    $lang->twitter_password = 'Twitter 密碼';
    $lang->about_twitter_password = '輸入 Twitter 密碼';

    $lang->blogapi_publish = 'BlogAPI 發佈';
    $lang->about_blog_api = '텍스타일로 작성한 글을 BlogAPI를 이용하여 다른 블로그 또는 게시판등에 동시 발행/ 수정/ 삭제할 수 있습니다<br/>지원하는 BlogAPI는 MetaWebLog 뿐이며 다른 API는 곧 지원할 수 있도록 하겠습니다<br/>텍스타일이 설치된 서버설정에 따라 BlogAPI 이용이 제한될 수 있습니다';
    $lang->cmd_registration_blogapi = 'BlogAPI 網站登錄';
    $lang->cmd_modification_blogapi = 'BlogAPI 網站資料編輯';
    $lang->blogapi_site_url = '目標網站';
    $lang->about_blogapi_site_url = '請輸入 BlogAPI 網站位址。';
    $lang->blogapi_site_title = 'blogAPI 網站標題';
    $lang->about_blogapi_site_title = '請輸入 BlogAPI 網站的標題。';
    $lang->blogapi_api_url = 'API URL';
    $lang->about_blogapi_url = '請輸入 BlogAPI URL 網址。(서비스 블로그의 경우 [id]값이나 도메인을 변경해주세요)';
    $lang->blogapi_published = '已發佈';
    $lang->blogapi_user_id = '用戶 ID';
    $lang->about_blogapi_user_id = 'API 대상 사이트에서 사용하는 사용자 아이디를 입력해주세요';
    $lang->blogapi_password = '用戶密碼';
    $lang->about_blogapi_password = 'API 대상 사이트에서 사용하는 사용자 비밀번호를 입력해주세요';
    $lang->cmd_get_site_info = '사이트 정보 구하기';
    $lang->cmd_check_api_connect = 'API 連結測試';
    $lang->msg_url_is_invalid = "입력하신 URL 접근을 할 수 없습니다\n\nURL을 다시 확인해주세요";
    $lang->msg_remove_api = '是否刪除 API 資料?';
    $lang->msg_blogapi_registration = array(
        '請輸入 API 目標網站位址',
        '請輸入網站標題',
        '請輸入 API 位址',
        '請輸入帳號',
        '請輸入密碼',
    );

    $lang->about_use_bookmarklet = '如何使用書籤';
    $lang->about_use_bookmarklet_item = array(
        '新增 "素材蒐集器" 書籤到瀏覽器',
        '當你發現適合寫文章的素材後，再點擊書籤',
        '選擇正確的類型 (例︰文字、圖片、影片)，編輯後儲存',
        '然後那些素材將儲存到此頁面'
    );
    $lang->basket_management = '垃圾桶管理';
    $lang->basket_list = '垃圾桶列表';
    $lang->basket_empty = '垃圾桶已清空。 ^^;';

    $lang->document_all = '全部檢視';
    $lang->document_published = '檢視已發佈';
    $lang->document_reserved = '檢視臨時儲存';

    $lang->my_document_management = '文章管理';
    $lang->set_publish = '公開設定';
    $lang->document_open = '公開';
    $lang->document_close = '不公開';

    $lang->category = '分類';
    $lang->comm_management = '設定';
    $lang->publish_date = '發布時間';
    $lang->publish_now = '現在';
    $lang->publish_reserve = '預定';
    $lang->ymd = '年.月.日';
    $lang->calendar = '日曆';
    $lang->close_calendar_layer = '關閉';
    $lang->select_calendar_layer = '選擇日期';

    $lang->insert_title = '請輸入標題';
    $lang->new_post = '發表文章';
    $lang->modify_post = '修改文章';
    $lang->posting_option = '迴響選項';
    $lang->post_url = '文章網址';
    $lang->about_tag = '多個標籤時，請用逗號(,)區隔';
	$lang->success_temp_saved = '臨時儲存成功';

    $lang->daily = '每天';
    $lang->weekly = '每週';
    $lang->monthly = '每月';
    $lang->before_day = '一天前';
    $lang->after_day = '一天後';
    $lang->before_month = '上個月';
    $lang->after_month = '下個月';
    $lang->this_month = '本月';
    $lang->today = '今天';
    $lang->day_current = '當天';
    $lang->day_before = '前天';
    $lang->none_visitor = '期間內沒有訪客。';
    $lang->about_status = array(
        'day'=>'可觀看每小時的訪客數量。',
        'week'=>'可觀看每週的訪客數量。',
        'month'=>'可觀看每月的訪客數量。',
    );
    $lang->about_unit = array(
        'day'=>'時間',
        'week'=>'年.月.日',
        'month'=>'年.月',
    );
    $lang->visit_count = '訪問次數';
    $lang->visit_per = '比率';
    $lang->trackback_division = '多個引用連結時利用 Enter 鍵換行';

    $lang->about_supporter = '粉絲是指會發表迴響、留言與發送引用的用戶。';
    $lang->supporter_rank = '粉絲排行';
    $lang->rank = '排行';
    $lang->user = '用戶';
    $lang->guestbook = '留言';
	$lang->add_denylist = '新增至過濾列表';
    $lang->summary = '總共';
    $lang->no_supporter = '目前沒有粉絲';
    $lang->about_popular = '熱門主題是指擁有很多點閱、迴響、通告與引用數量的文章。';
    $lang->popular_rank = '熱門主題排行';
    $lang->read = '檢視';
    $lang->no_popular = '目前沒有熱門主題';
    $lang->resize_vertical = '調整輸入框大小';

    $lang->textyle_first_menus = array(
        array('dispTextyleToolDashboard','管理首頁'),
        array('','文章管理'),
        array('','交流管理'),
        array('','統計管理'),
        array('','介面管理'),
        array('','設定管理'),
    );

    $lang->textyle_second_menus = array(
        array(),
        array(
            'dispTextyleToolPostManageWrite'=>'發表文章',
            'dispTextyleToolPostManageList'=>'文章列表',
            'dispTextyleToolPostManageDeposit'=>'素材管理',
            'dispTextyleToolPostManageCategory'=>'分類管理',
            'dispTextyleToolPostManageTag'=>'標籤管理',
            'dispTextyleToolPostManageBasket'=>'垃圾桶',
        ),
        array(
            'dispTextyleToolCommunicationComment'=>'迴響管理',
            'dispTextyleToolCommunicationGuestbook'=>'留言管理',
            'dispTextyleToolCommunicationTrackback'=>'引用管理',
            'dispTextyleToolCommunicationSpam'=>'過濾管理',
        ),
        array(
            'dispTextyleToolStatisticsVisitor'=>'訪問統計',
            'dispTextyleToolStatisticsVisitRoute'=>'訪問路徑',
            'dispTextyleToolStatisticsSupporter'=>'粉絲',
            'dispTextyleToolStatisticsPopular'=>'熱門主題',
        ),
        array(
            'dispTextyleToolLayoutConfigSkin'=>'面板選擇',
            'dispTextyleToolLayoutConfigEdit'=>'HTML&middot;CSS 編輯',
            'dispTextyleToolLayoutConfigMobileSkin'=>'모바일스킨선택',
        ),
        array(
            'dispTextyleToolConfigProfile'=>'個人檔案',
            'dispTextyleToolConfigInfo'=>'部落格設定',
            'dispTextyleToolConfigPostwrite'=>'發表設定',
            'dispTextyleToolConfigEditorComponents'=>'編輯器元件',
            'dispTextyleToolConfigCommunication'=>'公開&middot設定',
            'dispTextyleToolConfigBlogApi'=>'遠端發佈',
            'dispTextyleToolConfigAddon'=>'附加元件管理',
            'dispTextyleToolConfigData'=>'資料管理',
            'dispTextyleToolConfigChangePassword'=>'變更密碼',
            'dispTextyleToolExtraMenuList'=>'附加選單',
        ),
    );

    $lang->cmd_go_help = '使用說明';
    $lang->cmd_textyle_setup = '基本設定';
    $lang->cmd_textyle_list = 'Textyle 列表';
    $lang->cmd_textyle_creation = '建立 Textyle';
    $lang->cmd_textyle_custom_menu = '自訂 Textyle 選單';
    $lang->cmd_new_post = '發表文章';
    $lang->cmd_go_blog = '前往部落格';
    $lang->cmd_send_suggestion = 'Send a suggestion';
    $lang->cmd_view_help = 'View Help';
    $lang->cmd_folding_menu = '展開/收合選單';
    $lang->cmd_folding_xe_news = '展開/收合';
    $lang->cmd_apply = '應用';
    $lang->cmd_delete_favicon = '刪除圖示';
    $lang->cmd_change_password = '變更密碼';
    $lang->cmd_deny = '過濾';
    $lang->cmd_reply_comment = '回覆';
    $lang->cmd_change_secret = 'Make it secret';
    $lang->cmd_write_relation = 'Write a related post';
    $lang->cmd_delete_materials = '刪除素材';
    $lang->cmd_delete_selected_materials = '刪除所選素材';
    $lang->cmd_delete_all_materials = '刪除所有素材';
    $lang->cmd_restore = '復原';
    $lang->cmd_empty = '清空';
    $lang->cmd_empty_basket = '清空垃圾桶';
    $lang->cmd_change_category = '修改分類';
    $lang->cmd_save = '儲存';
    $lang->cmd_publish = '發佈';
    $lang->cmd_save_publish = '儲存後發佈';
    $lang->cmd_edit_htmlcss = 'HTML&middot;CSS 編輯';
    $lang->cmd_edit_html = '編輯 HTML';
    $lang->cmd_edit_css = '編輯 CSS';
    $lang->cmd_use_ing = '使用中';
    $lang->cmd_new_window = '新視窗';
    $lang->cmd_select_skin = '套用';
    $lang->msg_select_skin = '所選擇面板將套用到 Textyle 部落格。\n\n此操作將刪除正在使用的面板資料。\n\n確定要變換面板嗎?';
    $lang->cmd_preview_skin = '預覽';
    $lang->cmd_generate_widget_code = '建立 Widget 原始碼';
    $lang->msg_already_used_url = 'The URL is already being used.';
    $lang->alert_reset_skin = '重置面板將會刪除所有修改過的 HTML&middot;CSS 原始碼。\n\n確定要重置嗎?';
    $lang->msg_used_disabled_function = 'It contains forbidden functions.';

	$lang->msg_close_before_write = "變更的內容尚未被儲存。";

    $lang->no_post = '目前沒有文章， <a href="%s">寫篇文章吧!</a>';
    $lang->no_trash = '垃圾筒已清空!';
    $lang->no_comment = '目前沒有迴響';
    $lang->no_guestbook = '目前沒有留言';
    $lang->no_trackback = '目前沒有引用';

    // service
    $lang->view_all = '全部檢視';
    $lang->search_result = '搜尋結果';
    $lang->category_result = '分類';
    $lang->newest_document = '最近文章';
    $lang->newest_comment = '最近迴響';
    $lang->newest_trackback = '最近引用';
    $lang->archive = '封存';
    $lang->link = '連結';
    $lang->visitor_count = '訪客';

    $lang->mail = '郵件';
    $lang->ip = 'IP';

    $lang->cmd_hide = '隱藏';

	$lang->secret_comment = '秘密迴響';
	$lang->insert_comment = '發表迴響';
	$lang->content_list = '內容列表';
	$lang->msg_input_email_address = '請輸入電子郵件地址';
	$lang->msg_input_homepage = '請輸入網址';
	$lang->msg_confirm_delete_post = '臨時儲存的文章無法復原。確定要刪除嗎?';

    $lang->sample_title = '歡迎來到 Textyle 的世界!';
    $lang->sample_tags = 'textyle,  Textyle 編輯器,  素材蒐集器';
    $lang->msg_preparation = '準備中';
    $lang->msg_not_user = '用戶不存在';
    $lang->success_textyle_init = '重置成功';
	$lang->textyle_init = '重置';
    $lang->cmd_textyle_init = '開始重置';
    $lang->msg_textyle_init_about = '所有文章/迴響/引用/留言板/版面將會被刪除與重置。重置後將無法復原。';
    $lang->msg_confirm_textyle_init = '確定要重置嗎?';

	$lang->textyle_skin_userimage = '用戶相片';
	$lang->msg_check_userimage = '允許上傳 gif, png, jpg, swf 等檔案。';
    $lang->cmd_textyle_skin_export = '匯出';
    $lang->textyle_skin_import = '匯入面板';
    $lang->about_textyle_skin_import = '上傳客製化面板。 目前的面板會被刪除。';

	$lang->success_upload = '上傳成功';
	$lang->cmd_textyle_export_request ='備份記錄';
	$lang->textyle_export_recode = '備份記錄';
	$lang->textyle_export_waiting = '審查中';
	$lang->textyle_export_request = '備份記錄';
	$lang->textyle_export_type = '匯出類型';
	$lang->textyle_export_date = '檔案建立時間';
	$lang->textyle_export_file = '資料檔案';
	$lang->cmd_textyle_export_file = '匯出檔案';

	$lang->menu_name = '選單名稱';
	$lang->msg_module_count_exceed = '已超過可新增的模組數量';
	$lang->msg_limit_module = '剩下 %s 個';
    $lang->about_textyle_extra_menu = 'XpressEngine에 설치된 모듈을 텍스타일 메뉴로 추가할 수 있습니다.';
    $lang->cmd_textyle_extra_menu_config = '設定';
    $lang->textyle_extra_menu_limit_count = '數量限制';
	$lang->msg_limit_textyle_extra_mid = '只允許使用英文 + [ 英文，數字及底線 ]';

	$lang->msg_microblog_setup = '마이크로블로그에 동시 발행을 원하시면 %s을 먼저 해야 합니다.';
	$lang->config_edit_components = '編輯器設定';
	$lang->textyle_bug_report = '錯誤回報';
	$lang->cmd_open_close = '열기/닫기';
	$lang->msg_write_comment = '댓글쓰기';
	$lang->msg_mobile_skin_use_not = '현재 모바일 스킨을 사용하지 않고 있습니다. 사용하려면 스킨적용 버튼을 누르세요.';
	$lang->skip_content = '본문 건너뛰기';
?>
