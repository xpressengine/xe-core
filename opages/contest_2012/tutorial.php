<?php
    if(!defined("__ZBXE__")) exit();
?>

<script type="text/javascript">
	function getSourceCopy()
	{
		alert("getSourceCopy");
	}
	function putIn(where)
	{
		alert(where);
	}
	// 영상을 재생한다.
	function playVideo(flashID)
	{
		// 플래시 Object를 찾는다.
		var flashObj = nhn.FlashObject.find(flashID);
		// 플래시 안의 addCallback 함수를 호출한다.
		flashObj.playVideo();
	}
	// 영상을 정지한다.
	function stopVideo(flashID)
	{
		// 플래시 Object를 찾는다.
		var flashObj = nhn.FlashObject.find(flashID);

		// 플래시 안의 addCallback 함수를 호출한다.
		flashObj.stopVideo();
	}
	function changeMovie(flashID, idx)
	{
		var flashObj = nhn.FlashObject.find(flashID);
		flashObj.playSelectedMovie(idx);
	}
	function clickHandler1(movieName)
	{
		var movieBase = 'http://contest.xpressengine.com/opages/flv/';
		nhn.FlashObject.find("xePlayer1").playSelectedMovie(movieBase + movieName);
	}
	function clickHandler2(movieName)
	{
		var movieBase = 'http://contest.xpressengine.com/opages/flv/';
		nhn.FlashObject.find("xePlayer2").playSelectedMovie(movieBase + movieName);
	}
</script>
<script type="text/javascript" src="http://contest.xpressengine.com/opages/flashObject.js"></script>

<div id="contest_help">
<h1 class="h1"><img src="http://contest.xpressengine.com/layouts/xe_contest/img_content/h1Tutorial.gif" width="241" height="55" alt="동영상 강의" /></h1>
     <h2 class="h2">프로그램 개발의 기본 <sub>by</sub> <em>SOL</em></h2>
     <div class="tutorial">
      <div class="item">
       <dl>
        <dt>강의 내용</dt>
		<dd><button type="button" onClick="clickHandler1('module/Module_1.flv');">01/ 모듈이란</button></dd>
		<dd><button type="button" onClick="clickHandler1('module/Module_2.flv');">02/ 실습준비</button></dd>
		<dd><button type="button" onClick="clickHandler1('module/Module_3.flv');">03/ Hello XE 1</button></dd>
		<dd><button type="button" onClick="clickHandler1('module/Module_4.flv');">04/ Hello XE 2</button></dd>
		<dd><button type="button" onClick="clickHandler1('module/Module_5.flv');">05/ 메모모듈 1</button></dd>
		<dd><button type="button" onClick="clickHandler1('module/Module_6.flv');">06/ 메모모듈 2</button></dd>
		<dd><button type="button" onClick="clickHandler1('module/Module_7.flv');">07/ OpenAPI</button></dd>
       </dl>
       <p class="downloadMov"><a href="http://mov.xpressengine.com/xecamp/download/XE_modules.avi">동영상 다운로드</a></p>
       <p class="downloadPdf"><a href="http://contest.xpressengine.com/opages/flv/module/ModulePDF.zip">PDF/소스 다운로드</a></p>
      </div>
      <div class="movie">
       <!--<p class="notYet">아직 동영상 강의가 준비되지 않았습니다.</p>-->
		<script type="text/javascript">
		   var flashVars = "imgURL=http://contest.xpressengine.com/opages/xePlayer.gif&flvURL=http://contest.xpressengine.com/opages/flv/module/Module_1.flv&autoPlay=false"; 
		   var obj = {}; 
		   obj.wmode = "window"; 
		   obj.flashVars = flashVars; 
		   nhn.FlashObject.show("http://contest.xpressengine.com/opages/xePlayer.swf", "xePlayer1",421, 320, obj);
		</script>
      </div>
     </div>
     <p class="tx1"><a href="http://xe.xpressengine.net/?mid=wiki&act=dispWikiTreeIndex" target="_blank">XE 프로그램 제작 가이드</a>를 함께 참고하세요!</p>
     <div class="hr"></div>
     <h2 class="h2">XE 스킨 만드는 방법 <sub>by</sub> <em>정찬명</em></h2>
     <div class="tutorial">
      <div class="item">
       <dl>
        <dt>강의 내용</dt>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_1.flv');">01/ 스킨가이드</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_2.flv');">02/ 템플릿문법</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_3.flv');">03/ 실습준비</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_4.flv');">04/ 모듈내려받기</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_5.flv');">05/ 새문서만들기</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_6.flv');">06/ 스킨구조</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_7.flv');">07/ 레이아웃생성</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_8.flv');">08/ CSS+JS연결</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_9.flv');">09/ 메뉴위젯</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_10.flv');">10/ 로그인위젯</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_11.flv');">11/ 통합검색창</button></dd>
		<dd><button type="button" onClick="clickHandler2('skin/xeSkin_12.flv');">12/ 본문출력</button></dd>
       </dl>
       <p class="downloadMov"><a href="http://mov.xpressengine.com/xecamp/download/XE_skin.avi">동영상 다운로드</a></p>
       <p class="downloadPdf"><a href="http://contest.xpressengine.com/opages/flv/skin/xeSkinPDF.zip">PDF 다운로드</a></p>
      </div>
      <div class="movie">
       <!--<p class="notYet">아직 동영상 강의가 준비되지 않았습니다.</p>-->
		<script type="text/javascript">
		   var flashVars = "imgURL=http://contest.xpressengine.com/opages/xePlayer.gif&flvURL=http://contest.xpressengine.com/opages/flv/skin/xeSkin_1.flv&autoPlay=false"; 
		   var obj = {}; 
		   obj.wmode = "window"; 
		   obj.flashVars = flashVars; 
		   nhn.FlashObject.show("http://contest.xpressengine.com/opages/xePlayer.swf", "xePlayer2",421, 320, obj);
		</script>
      </div>
     </div>
     <p class="tx1"><a href="http://xe.xpressengine.net/wiki/18250394" target="_blank">XE 스킨 제작 가이드</a>를 함께 참고하세요!</p>
 
</div>
