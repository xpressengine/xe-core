<?php if(!defined("__ZBXE__")) exit();?>
<style type="text/css">
.content{padding-top:40px}
.community{position:relative;height:132px;margin:0 0 10px 0;background:url(bgWidget.gif) no-repeat;font-family:돋움, Dotum, Tahoma, Geneva, sans-serif}
.community h2,
.community .more{font-family:나눔고딕, NanumGothic, "맑은 고딕", "Malgun Gothic"}
.community h2{width:130px;height:0;overflow:hidden;padding:90px 0 0 0;margin:0;font-size:15px;font-weight:normal;text-align:center;float:left;background:url(icTheme.gif) no-repeat 0 16px}
.community a.more{position:absolute;top:99px;left:29px;width:74px;color:#666;text-align:center;font:bold 9px/20px Tahoma, Geneva, sans-serif;text-decoration:none}
.community a.more:hover,
.community a.more:active,
.community a.more:focus{color:#5f4ebe}
.community.freeboard h2{background-position:0 -104px}
.community.tip h2{background-position:0 -224px}
.community.qna h2{background-position:0 -344px}
.community.agent h2{background-position:0 -464px}
.community.hosting h2{background-position:0 -584px}
.community.job h2{background-position:0 -704px}
.community ul{margin:0;padding:15px 160px 0 20px;list-style:none}
.community ul a{text-decoration:none}
.community ul a:hover,
.community ul a:active,
.community ul a:focus{text-decoration:underline}
.community li{position:relative;margin:0 0 18px 0}
.community li.next{position:absolute;top:101px;left:150px;text-indent:50px}
.community li.next .title{font-weight:normal;color:#767676}
.community .title{color:#333;font-weight:bold}
.community .summary{margin:0;padding:8px 0 0 0;line-height:1.3}
.community .summary a{color:#666}
.community .author{position:absolute;top:23px;left:490px;white-space:nowrap;text-decoration:none !important;color:#767676;font-size:11px}
.community li.next .author{top:0;left:440px}
.community .author img{margin:0 5px 0 0 !important}
.community .date{position:absolute;top:40px;left:490px;color:#767676;white-space:nowrap;font-size:11px;padding:0 0 0 18px;line-height:14px;background:url(icBtn.gif) no-repeat 0 0}
.community .hour{position:absolute;top:40px;left:570px;color:#767676;white-space:nowrap;font-size:11px}
.community .replyNum{position:absolute;top:57px;left:490px;font-size:11px !important;font-style:normal;padding:0 0 0 18px;line-height:14px;color:#767676;background:url(icBtn.gif) no-repeat 0 -30px}
.community .replyNum a{color:#767676} 
.community .next .replyNum{display:none}
</style>
<div class="community userforum">
	<h2>사용자 포럼</h2>
	<img widget="content" skin="default" content_type="document" module_srls="19778968" list_type="title_content" markup_type="list" list_count="2" subject_cut_size="60" content_cut_size="240" option_view="title,content,nickname,regdate" show_comment_count="Y" order_type="desc" />
	<a href="/userForum/" class="more">Read More</a>
</div>
<div class="community freeboard">
	<h2>자유게시판</h2>
	<img widget="content" skin="default" content_type="document" module_srls="18537513" list_type="title_content" markup_type="list" list_count="2" subject_cut_size="60" content_cut_size="240" option_view="title,content,nickname,regdate" show_comment_count="Y" order_type="desc" />
	<a href="/freeboard/" class="more">Read More</a>
</div>
<div class="community tip">
	<h2>XE 팁</h2>
	<img widget="content" skin="default" content_type="document" module_srls="121" list_type="title_content" markup_type="list" list_count="2" subject_cut_size="60" content_cut_size="240" option_view="title,content,nickname,regdate" show_comment_count="Y" order_type="desc" />
	<a href="/tip/" class="more">Read More</a>
</div>
<div class="community qna">
	<h2>묻고 답하기</h2>
	<img widget="content" skin="default" content_type="document" module_srls="122" list_type="title_content" markup_type="list" list_count="2" subject_cut_size="60" content_cut_size="240" option_view="title,content,nickname,regdate" show_comment_count="Y" order_type="desc" />
	<a href="/qna/" class="more">Read More</a>
</div>
<div class="community agent">
	<h2>제작의뢰&sdot;지원</h2>
	<img widget="content" skin="default" content_type="document" module_srls="16326834" list_type="title_content" markup_type="list" list_count="2" subject_cut_size="60" content_cut_size="240" option_view="title,content,nickname,regdate" show_comment_count="Y" order_type="desc" />
	<a href="/agent/" class="more">Read More</a>
</div>
<div class="community hosting">
	<h2>계정홍보&sdot;공유</h2>
	<img widget="content" skin="default" content_type="document" module_srls="13271165" list_type="title_content" markup_type="list" list_count="2" subject_cut_size="60" content_cut_size="240" option_view="title,content,nickname,regdate" show_comment_count="Y" order_type="desc" />
	<a href="/hosting/" class="more">Read More</a>
</div>
<script type="text/javascript" src="community.js"></script>
