<?php
    if(!defined("__ZBXE__")) exit();
?>

{@ $targetDate = ztime("20100126"); }
{@ $gap = $targetDate - time(); }
{@ $days = (int)($gap/24/60/60) + 1; }
{@ $gap -= $days*24*60*60; }
{@ $hrs = (int)($gap/60/60); }

<!-- #contest_honor -->
<div id="contest_honor">
	<h1 class="h1"><img src="http://contest.xpressengine.com/layouts/xe_contest/img_content/h1Honor.gif" width="125" height="50" alt="수상작 보기" /></h1>
	<p>안녕하세요, XE 개발팀입니다. <br />
		제1회 XE 공모전의 수상작들을 발표합니다. </p>
	<h2>XE 상 <em style="font-style:normal; color:#ccc"><span>(애플 맥북에어)</span></em><span></span></h2>
	<h3>WizardXE (프로그램) <sup>by zirho</sup> </h3>
	<p><img src="/opages/img/wizardXe.png" width="200" height="77" alt="Wizard XE 모듈 프로그램" class="thumb" />XE는 웹사이트 만들기를 쉽게 해주는 프로그램입니다. WizardXE는 XE로 웹사이트를 구성하는 과정을 한층 더 쉽고 편하게 해주는 ‘마법사’ 프로그램입니다. 사용자의 관점에서 문제를 짚어내고, 꼼꼼한 UI 설계로 높은 완성도를 성취했습니다. 프로그램 자체의 완성도도 높지만, 소개 동영상이나 웹사이트 등 결과물을 전달하는 과정까지 주의 깊게 배려한 점은 매우 인상적입니다. WizardXE는 공모전 전 부문을 통틀어 가장 많은 추천을 받았으며, 심사위원들도 만장일치로 대상 수여를 결정했습니다. 축하합니다! <br />
	[<a href="http://contest.xpressengine.com/18603910">자세히</a> | <a href="http://www.zirho.co.kr/?mid=intro&iid=1">데모영상</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18603911&sid=72b4ecb9a98b9b8c9ab339565630ba5d">다운로드</a> | <a href="http://www.zirho.co.kr/">제작자 웹사이트</a>]</p>
	<h2>Community 상 <em style="font-style:normal; color:#ccc">(삼성전자 센스 넷북 NT-310)</em></h2>
	<h3>포털형 카라 팬페이지 카밀린넷 (웹사이트) <sup>by wooam</sup> </h3>
	<p><img src="/opages/img/kamilin.gif" width="200" height="100" alt="카밀린넷 웹사이트" class="thumb" />카밀린넷은 여성그룹 '카라'의 팬들을 위한 사이트로, XE의 장점들을 잘 드러내고 있는 웹사이트입니다. 독창적이고 깔끔한 레이아웃과 스킨, 사이트 구성 역시 완성도가 높고, 인기검색어 위젯이나 스케줄러 모듈 등 XE의 확장기능을 적재적소에 잘 활용한 점 등이 골고루 높은 점수를 받았습니다. 축하합니다!	<br />
	[<a href="http://contest.xpressengine.com/18594213">자세히</a> | <a href="http://www.kamilin.net/">웹사이트</a>] </p>
	<h2>Open Source 상 <em style="font-style:normal; color:#ccc">(애플 아이팟 터치 32G)</em></h2>
	<h3>출석부XE 1.5.1 (프로그램) <sup>by 매실茶</sup></h3>
	<p><img src="/opages/img/attendance.gif" width="200" height="100" alt="출석부XE 모듈 프로그램" class="thumb" />커뮤니티 회원들의 출석정보를 기록하고 관리하며 출석에 따라 포인트를 줄 수 있도록 해주는 프로그램입니다. 이미 다수의 커뮤니티 관리자 여러분들이 유용하고 만족스럽게 쓰고 계십니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18603857">자세히</a> | <a href="http://maesiltea.linuxstudy.pe.kr/zbxe/?mid=attendance">데모</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18603859&sid=4d1b3fccf7d757bc2ebe69abc41c560f">다운로드</a> | <a href="http://maesiltea.linuxstudy.pe.kr/zbxe/">제작자 웹사이트</a>]</p>
	<h3>쪽지함 관리 모듈 ver 0.1 (프로그램) <sup>by LK군</sup></h3>
	<p><img src="/opages/img/msgAdmin.gif" width="200" height="80" alt="쪽지함 관리 모듈 프로그램" class="thumb" />많은 커뮤니티 관리자 분들이 갈증을 느끼고 있던 쪽지 기능을 강화해주는 프로그램입니다. 쪽지의 전체발송, 그룹별 발송 등을 가능하게 해줍니다. 꾸준한 업데이트가 뒤따르기를 바랍니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18603904">자세히</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18603908&sid=65f54759c0bffd1064b3434778269188">다운로드</a>]</p>
	<h3>Level D Theme for XE Board Module (스킨) <sup>by levelD</sup></h3>
	<p><img src="/opages/img/levelD.gif" width="200" height="100" alt="Level D 게시판 스킨" class="thumb" />매우 세련된 디자인의 게시판 스킨입니다. 간결하면서도 개성 있는, 동시에 아름다운 스킨입니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18604051">자세히</a> | <a href="http://somniloquy.net/board">데모</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18604061&sid=ea64f5dc1758bc360f33b6331bc2dfe5">다운로드</a> | <a href="http://somniloquy.net/">제작자 웹사이트</a>]</p>
	<h3>Blooz Layout Ver 3.0 (스킨) <sup>by 블루즈</sup></h3>
	<p><img src="/opages/img/blooz.gif" width="200" height="100" alt="Blooz 레이아웃 스킨" class="thumb" />완성도가 높고 여러 사이트에 무리 없이 사용할 수 있는 범용적인 레이아웃 스킨입니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18603971">자세히</a> | <a href="http://blooz.net/main/blooz_layout_ver3">데모</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18603974&sid=bc68e6cce340dbcfc309fd61aa870200">다운로드</a> | <a href="http://blog.blooz.net/">제작자 웹사이트</a>]</p>
	<h2>Create 상 <em style="font-style:normal; color:#ccc">(아이리버 스토리)</em></h2>
	<h3>저니 로그맵 Jowrney Logmap (프로그램) <sup>by Jowrney</sup></h3>
	<p><img src="/opages/img/jowrneyLogmap.gif" width="200" height="100" alt="저니 로그맵 모듈 프로그램" class="thumb" />GPS를 이용해 기록한 이동경로를 구글 지도 위에 얹어서 보여주는 에디터 확장 컴포넌트입니다. 앞으로도 Open API를 이용한 매시업이 많이 나오기를 바랍니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18603873">자세히</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18603874&sid=cda13457f82c4fcbadeaa1b0beecb101">다운로드</a> | <a href="http://jowrney.com/">제작자 웹사이트</a>]</p>
	<h3>CashbookXE (프로그램) <sup>by 제디슨</sup></h3>
	<p><img src="/opages/img/cashBook.gif" width="200" height="100" alt="캐시북XE 모듈 프로그램" class="thumb" />종합자산관리 프로그램을 목표로 하는 가계부 프로그램입니다. 기술적인 완성도도 높고 문서화도 매우 잘 되어있습니다. 축하합니다!	데모 사이트	접근 아이디와 비밀번호는 'cashbook_demo/111111' 입니다. <br />
	[<a href="http://contest.xpressengine.com/18603852">자세히</a> | <a href="http://www.xgenesis.org/xe/cashbook">데모</a> | <a href="http://cashbook.xpressengine.net/?mid=issuetracker&act=dispIssuetrackerDownload">다운로드</a> | <a href="http://www.xgenesis.org/">제작자 웹사이트</a>] </p>
	<h3>이상형 월드컵 위젯 (프로그램) <sup>by 구이92</sup></h3>
	<p><img src="/opages/img/idealType.gif" width="200" height="100" alt="이상형 월드컵 모듈 프로그램" class="thumb" />마음에 드는 연예인을 선택하면 이상형을 찾아준다는, 독창적인 아이디어가 눈에 띄는 위젯입니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18603913">자세히</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18620430&sid=7fdc0df95285acf71ab80079e0f160dc">다운로드1</a>, <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18620431&sid=cd46506926405fd87c1a2ec67fe82e27">다운로드2</a>]</p>
	<h3>‘조용한’ 게시판 2.0 (스킨) <sup>by June Oh</sup></h3>
	<p><img src="/opages/img/quietBoard.gif" width="200" height="85" alt="조용한 게시판 스킨" class="thumb" />깔끔함이 눈에 띄는 게시판 스킨입니다. 접근성과 사용성도 뛰어나며, 매우 높은 완성도를 보여줍니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18604002">자세히</a> | <a href="http://juneoh.net/quiet_board">데모</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18604004&sid=b70f1e4ba1b7ac38d21fcbb14646334b">다운로드</a> | <a href="http://juneoh.net/">제작자 웹사이트</a>]</p>
	<h3>Sky2 Layout (스킨) <sup>by 엘카</sup></h3>
	<p><img src="/opages/img/sky2.gif" width="200" height="100" alt="스카이2 레이아웃 스킨" class="thumb" />보기 드물게 플래시를 사용한 레이아웃 스킨입니다. 업데이트를 통해 접근성이 개선되기를 바랍니다. 축하합니다! <br />
	[<a href="http://contest.xpressengine.com/18604070">자세히</a> | <a href="http://www.notepad.wo.tc:2009/sky2">데모</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18604071&sid=61063d2f40dd2dca0199980c8373143f">다운로드</a> | <a href="http://www.notepad.wo.tc:2009/">제작자 웹사이트</a>]</p>
	<h3> 카멜레온 레이아웃 (스킨) <sup>by 된장맛껌</sup></h3>
	<p><img src="/opages/img/chameleon.gif" width="200" height="100" alt="카멜레온 레이아웃 스킨" class="thumb" />관리자 메뉴를 통해 손쉽게 스킨의 형태와 색상을 바꿀 수 있는 레이아웃 스킨입니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18603939">자세히</a> | <a href="http://shx.kr/">데모</a> | <a href="http://contest.xpressengine.com/?module=file&act=procFileDownload&file_srl=18603951&sid=95016e7a3a4a42182b9224cdd510cfe2">다운로드</a> | <a href="http://shx.kr/">제작자 웹사이트</a>]</p>
	<h3>웹미니 (웹사이트) <sup>by 빽짱구</sup></h3>
	<p><img src="/opages/img/webmini.gif" width="200" height="100" alt="웹미니쩜넷 웹 사이트" class="thumb" />XE 관련정보뿐 아니라 웹 제작자에게 필요한 다양한 정보를 알차게 제공하며, 자작 스킨을 멋지게 입힌 좋은 사이트입니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18590101">자세히</a> | <a href="http://www.webmini.net/">웹 사이트</a>]</p>
	<h3>참특수교육 (웹사이트) <sup>by 영구만세</sup></h3>
	<p><img src="/opages/img/truespedu.gif" width="200" height="100" alt="참특수교육 웹 사이트" class="thumb" />절제된 정보의 배치와 다양한 사용환경을 배려한 접근성, 주제와 대상의 이미지를 잘 반영한 심미성이 모두 뛰어납니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18589078">자세히</a> | <a href="http://www.truespedu.org/">웹 사이트</a>]</p>
	<h3>XE와 YahooMap OpenAPI를 이용한 여행기록 공유 사이트 (웹사이트) <sup>by 트루먼</sup></h3>
	<p><img src="/opages/img/triplog.gif" width="200" height="100" alt="TRIP Log 여행일기 웹 사이트" class="thumb" />여행정보 공유라는 목적에 충실한 콘텐트와 외부 API 활용이 돋보였고, LiveXE 모듈의 적절한 활용도 좋습니다. 축하합니다!		<br />
	[<a href="http://contest.xpressengine.com/18601005">자세히</a> | <a href="http://triplog.kr/">웹 사이트</a>]</p>
	<h2>Netizen 상 <em style="font-style:normal; color:#ccc">(Realforce103 한글판 키보드)</em></h2>
	<h3>WizardXE (프로그램) <sup>by zirho</sup></h3>
	<p><img src="/opages/img/wizardXe.png" width="200" height="77" alt="Wizard XE 모듈 프로그램" class="thumb" />추천을 72표 얻어 XE 상과 함께 프로그램 부문에서 Netizen 상을 받게 되었습니다. 다시 한 번 축하합니다! <br />
	[<a href="http://contest.xpressengine.com/18603910">자세히</a> | <a href="http://www.zirho.co.kr/?mid=intro&amp;iid=1">데모영상</a> | <a href="http://contest.xpressengine.com/?module=file&amp;act=procFileDownload&amp;file_srl=18603911&amp;sid=72b4ecb9a98b9b8c9ab339565630ba5d">다운로드</a> | <a href="http://www.zirho.co.kr/">제작자 웹사이트</a>]</p>
	<h3>‘조용한’ 게시판 2.0 (스킨) <sup>by June Oh</sup></h3>
	<p><img src="/opages/img/quietBoard.gif" width="200" height="85" alt="조용한 게시판 스킨" class="thumb" />추천을 40표 얻어 Create 상과  함께 프로그램 부문에서 Netizen 상을 받게 되었습니다. 다시  한 번 축하합니다! <br />
	[<a href="http://contest.xpressengine.com/18604002">자세히</a> | <a href="http://juneoh.net/quiet_board">데모</a> | <a href="http://contest.xpressengine.com/?module=file&amp;act=procFileDownload&amp;file_srl=18604004&amp;sid=b70f1e4ba1b7ac38d21fcbb14646334b">다운로드</a> | <a href="http://juneoh.net/">제작자 웹사이트</a>]</p>
	<h3>포털형 카라 팬페이지 카밀린넷 (웹사이트) <sup>by wooam</sup></h3>
	<p><img src="/opages/img/kamilin.gif" width="200" height="100" alt="카밀린넷 웹사이트" class="thumb" />추천을 40표 얻어  Community 상과 함께 프로그램 부문에서 Netizen 상을 받게 되었습니다. 다시 한 번 축하합니다! <br />
	[<a href="http://contest.xpressengine.com/18594213">자세히</a> | <a href="http://www.kamilin.net/">웹사이트</a>] </p>
	<h2>Pragmatic 상 <em style="font-style:normal; color:#ccc">(XE 티셔츠와 노트)</em></h2>
	<p><img src="/opages/img/xeNote.gif" width="100" height="100" alt="XE 티셔츠와 노트" class="thumb" />참가해주신 <a href="http://contest.xpressengine.com/?mid=contestVote">모든 분들</a>께 XE 티셔츠와 노트 세트를 보내 드립니다. </p>
	<p>&nbsp;</p>
	<p>좋은 작품이 많이 접수되어 심사가 쉽지 않았습니다. 더 많은 상품을  준비하지 못해, 근소한 차이로 입상을 못하신 분들께는 송구스러울 뿐입니다. 더 많은 분들께 더 많은 상품을 드리고 싶지만 그러지 못해 아쉽습니다. </p>
	<p>이번 기회를 통해 XE는 우리 모두가 함께 만들어가는 것임을 다시  한 번 확인할 수 있었습니다. 다시 한 번 공모전에 참가해주신 모든 분들께 진심으로 감사 드립니다.</p>
</div>
<!-- /#contest_honor --> 
