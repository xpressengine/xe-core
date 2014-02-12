<?php
    if(!defined("__ZBXE__")) exit();
?>
<script type="text/javascript">
function insertComplete(ret_obj) 
{
    var error = ret_obj['error'];
    var message = ret_obj['message'];

    alert(message);
    location.href = current_url;
}
function insertEmail(fo_obj) {
var oFilter = new XmlJsFilter(fo_obj, "emailstorage", "procInsert", insertComplete);
oFilter.addFieldItem("email_address",true,1,200,"","email");
oFilter.addResponseItem("error");
oFilter.addResponseItem("message");
return oFilter.proc("등록하시겠습니까?");
}
alertMsg["invalid_email"] = "%s의 형식이 잘못되었습니다. (예: xe@xpressengine.com)"; 
alertMsg["email_address"] = "이메일 주소"; 
alertMsg["isnull"] = "%s을 입력해주세요."; 

</script>
 

<!-- #contest_help -->
                <div id="contest_help">
                    <h1 class="h1"><img editor_component="image_link" src="layouts/xe_contest/img_content/h1NewsLetter.gif" alt="뉴스레터" width="209" height="51" /></h1>
                    <form action="./" method="post" class="email" onsubmit="return procFilter(this, insertEmail);">
                        <fieldset>
                            <legend class="invisibleElement">XE 공모전 뉴스레터 수신 신청</legend>
                            <label for="email">받아보실 이메일 주소</label><input name="email_address" id="email" class="inputText" type="text" /> <input name="" src="layouts/xe_contest/img_content/buttonNewsLetter.gif" alt="구독신청" type="image" />
                        </fieldset>
                    </form>
                    <p class="tx2" style="margin-bottom: 55px;">* 총 5회에 걸쳐 공모전 관련 소식을 전해드리며, 공모전 종료 후에는 더 이상 어떤 메일도 보내지 않습니다.</p>
                    <div id="newsLetter" class="newsLetter4">
                        <ul class="index">
                            <li class="tab1"><a href="#newsLetter1" onclick="document.getElementById('newsLetter').className='newsLetter1';return false;">1호</a></li>
                            <li class="tab2"><a href="#newsLetter2" onclick="document.getElementById('newsLetter').className='newsLetter2';return false;">2호</a></li>
                            <li class="tab3"><a href="#newsLetter3" onclick="document.getElementById('newsLetter').className='newsLetter3';return false;">3호</a></li>
                            <li class="tab4"><a href="#newsLetter4" onclick="document.getElementById('newsLetter').className='newsLetter4';return false;">4호</a></li>
                            <li class="tab5"><a href="#newsLetter5" onclick="document.getElementById('newsLetter').className='newsLetter5';return false;">5호</a></li>
                        </ul>
                        <div class="newsLetter" id="newsLetter1">
							<div style="width:545px; margin:0 auto; text-align:center; font-size:12px; font-family:Dotum, 돋움">
								<h1 style="margin:0"><img src="http://contest.xpressengine.com/opages/img/h1v1.gif" width="545" height="188" alt="XE 공모전 Newsletter Vol. 01 - 공모전 준비 이렇게 시작하세요."></h1>
								<div>
									<p style="text-align:left; line-height:1.6; color:#909090; margin:15px 0 30px 0; letter-spacing:-1px">XE 공모전은 개발(프로그램) 부문, 디자인/퍼블리싱(스킨) 부문, 그리고 웹사이트 부문으로 구성됩니다. <br>
										개발자나 디자이너 모두가 참여할 수 있지요. 여전히 뭘 어떻게 해야할지 모르시겠다고요? <br />
										그렇다면 계속 읽어보세요!</p>
									<div style="border:6px solid #f3f3f3; padding:30px 30px 30px 35px; text-align:left;">
										<h2 style="margin:0"><img src="http://contest.xpressengine.com/opages/img/h2Designer.gif" width="80" height="15" alt="디자이너세요?" style="margin:0 0 8px 0"></h2>
										<p style="margin:0 0 5px 0; color:#2e2e2e; line-height:1.5; letter-spacing:-1px;">블로그 스킨이나 카페 스킨, 방명록 스킨 등을 만들어보세요. 스킨 전체를 만들기가 어려우시면 <br>
											상단 내비게이션이나  좌측 메뉴만 만들어보세요. 방문자 카운터 같은 위젯용 스킨을 만들어보는<br> 
											건 어떨까요? XE 스킨 제작 가이드(또는 <a href="http://contest.xpressengine.com/?mid=contestTutorial">동영상</a>)을 참고하세요.</p>
										<p style="margin:0 0 15px 0; color:6f6f6f; font-size:11px; line-height:1.5; letter-spacing:-1px;">XE 에 맞는 HTML/CSS 구조를 몰라도 걱정 마세요. 퍼블리셔와 팀을 이루시거나 XE 스킨 제작 가이드를 참고하시면 되니까요!</p>
										<p style="margin:0 0 35px 0"><a href="http://xe.xpressengine.net/wiki/18250394" target="_blank"><img src="http://contest.xpressengine.com/opages/img/buttonSkinGuide.gif" width="162" height="24" alt="XE 스킨 제작 가이드" border="0"></a>&nbsp;&nbsp;<a href="http://contest.xpressengine.com/?mid=contestWanted" target="_blank"><img src="http://contest.xpressengine.com/opages/img/buttonFindMember.gif" width="162" height="24" alt="멤버 모집 게시판 가기" border="0"></a></p>
										<h2 style="margin:0"><img src="http://contest.xpressengine.com/opages/img/h2Developer.gif" width="69" height="15" alt="개발자세요?" style="margin:0 0 8px 0"></h2>
									   <p style="margin:0 0 5px 0; color:#2e2e2e; line-height:1.5; letter-spacing:-1px;">모듈이나 위젯, 아니면 애드온을 만드세요. 여러 다른 서비스와 연동되는 매시업 모듈을 <br>
											 만들어보세요. XE 프로그램 제작 가이드(또는 <a href="http://contest.xpressengine.com/?mid=contestTutorial">동영상</a>)를 참고하세요. PHP가 마음에 안 드세요? </p>
										<p style="margin:0 0 15px 0; color:6f6f6f; font-size:11px; line-height:1.5; letter-spacing:-1px;">XE와 연동되는 소프트웨어도 공모전 대상에 포함됩니다. Windows, Mac OS, Linux, iPhone, Android 등 선호하는 플랫폼 위에서 XE와 연계되어 동작하는 프로그램을 만들어보세요! C나 Java에서 AIR나 Silverlight까지, 어떤 언어를 쓰셔도 좋습니다. 멤버 모집 게시판에서 함께 할 디자이너를 찾아보세요!</p>
										<p style="margin:0 0 35px 0"><a href="http://xe.xpressengine.net/?mid=wiki&act=dispWikiTreeIndex" target="_blank"><img src="http://contest.xpressengine.com/opages/img/buttonProgramGuide.gif" width="162" height="24" alt="XE 프로그램 제작 가이드" border="0"></a>&nbsp;&nbsp;<a href="http://contest.xpressengine.com/?mid=contestWanted" target="_blank"><img src="http://contest.xpressengine.com/opages/img/buttonFindMember.gif" width="162" height="24" alt="멤버 모집 게시판 가기" border="0"></a></p>
										<h2 style="margin:0"><img src="http://contest.xpressengine.com/opages/img/h2Help.gif" width="121" height="15" alt="아직 잘 모르시겠다구요?" style="margin:0 0 8px 0"></h2>
										<p style="margin:0 0 5px 0; color:#2e2e2e; line-height:1.5; letter-spacing:-1px;">XE 공모전에서 무엇을 해야 할지 저희가 살짝 조언을 드려도 될까요?</p>
										<p style="margin:0 0 15px 0; color:6f6f6f; font-size:11px; line-height:1.5; letter-spacing:-1px;">다소 충격적인 결말이 나오더라도 놀라지는 마세요. ㅡㅡ;</p>
										<p style="margin:0"><a href="http://contest.xpressengine.com/opages/interactionGuide/index.html" target="_blank"><img src="http://contest.xpressengine.com/opages/img/buttonMyJob.gif" width="162" height="24" alt="내 할일 찾아보기" border="0"></a></p>
									</div>
									<p style="color:#989898; line-height:1.6; text-align:left">XE와 공모전에 관한 모든 궁금증은 질문 답변 게시판을 이용해주세요. <br>
										공모전 참가를 준비하고 계신 모든 분들께 행운을 빕니다!<br>
										<img src="http://contest.xpressengine.com/opages/img/tx091111.gif" width="205" height="13" alt="2009년 11월 11일, NHN 오픈UI기술팀 드림" style="margin:5px 0 0 0"></p>
									<p style="margin:22px 0 0 0; padding:0 0 23px 0; border-bottom:3px solid #000;"><a href="http://www.xpressengine.com/18429157" target="_blank"><img src="http://contest.xpressengine.com/opages/img/adEasyInstall.gif" width="545" height="82" alt="XE 1.3.0 쉬운설치 기능 추가! 부가기능이나 스킨, 이제 원-클릭으로 설치하세요." border="0"></a></p>
								</div>
							</div>
                        </div>
                        <div class="newsLetter" id="newsLetter2">
							<div style="width:545px; margin:0 auto; text-align:center; font-size:12px; font-family:Dotum, 돋움">
								<h1 style="margin:0"><img src="http://contest.xpressengine.com/opages/img/h1v2.gif" width="545" height="218" alt="XE 공모전 Newsletter"></h1>
								<div style="width:545px; margin:15px 0">
									<div style="border:6px solid #f3f3f3; padding:30px 30px 30px 35px; text-align:left; line-height:1.5">
										<p style="margin:1em 0">다들 아시겠지만, <a href="http://contest.xpressengine.com/18538562" style="font-weight:bold">XE 아이디어 모집 이벤트: Make a Wish! XE-Mas Event</a>가 진행 중입니다. 한국마이크로소프트에서 협찬한 Windows 7 Ultimate Edition과 무선 마우스를 타기 위해, 많은 분들이 기존 모듈의 개선이나 신규 모듈 아이디어를 내놓고 계십니다. 이벤트를 시작한지 일주일 만에 100개가 넘는 아이디어가<a href="http://contest.xpressengine.com/contestProposal"> 제작 제안 게시판</a>에 올라왔고, 이벤트 공지에도 많은 <a href="http://contest.xpressengine.com/18538562">댓글과 트랙백</a>이 달리고 있습니다. </p>
										<p style="margin:1em 0">이 수많은 아이디어들은 XE 공모전에 참여하시려는 분들 누구나 자유롭게 활용하실 수 있습니다. 아직 XE 공모전에 무얼 만들어 출품할지 결정하지 못하신 분들은, 지금 바로 <a href="http://contest.xpressengine.com/contestProposal" style="font-weight:bold">제작 제안 게시판</a>에 가보세요. XE 사용자들이 원하는 것이 무엇인지, 어떤 모듈을 만들면 인기가 좋을지 알려주는 소중한 자료들이 잔뜩 널려있으니까요! </p>
										<p style="margin:1em 0">XE 스킨 제작 가이드와 프로그램 제작 가이드 동영상은 다들 보셨나요? 글씨가 너무 작게 보여 조금 보다가 관두셨나요? 얼마 전에 다운로드용 고화질 동영상이 업데이트 되었습니다. <a href="http://contest.xpressengine.com/?mid=contestTutorial">동영상 강의 페이지</a>에서 “동영상 다운로드” 버튼을 누르거나, 아래의 링크를 클릭하면 바로 다운로드 받아 보실 수 있습니다. 공모전 출품작 제작에 큰 도움이 될 것입니다!</p>
										<ul>
											<li><a href="http://mov.xpressengine.com/xecamp/download/XE_modules.avi">"XE 프로그램 개발의 기본" 동영상 다운로드</a></li>
											<li><a href="http://mov.xpressengine.com/xecamp/download/XE_skin.avi">"XE 스킨 만드는 법" 동영상 다운로드</a></li>
										</ul>
										<p style="margin:1em 0">이제 얼마 후면 응모작 제출이 시작됩니다. 제작을 서두르세요! <br />
											준비하고 계신 모든 분들께 다시 한 번 행운을 빕니다! </p>
										<p style="margin:1em 0">2009년 12월 18일, NHN 오픈UI기술팀 드림</p>
									</div>
								</div>
								<p style="border-top:3px solid #000; margin:0; padding:15px 0; font-size:11px; color:#989898; line-height:1.6; text-align:left; letter-spacing:-1px; text-align:center">XE공모전 뉴스레터는 총 5회에 걸쳐, 직접 <a href="http://contest.xpressengine.com/?mid=contestLetter" target="_blank" style="font-weight:bold; color:#474747; text-decoration:none">뉴스레터 구독신청</a>을 하신 분들께만 보내드리고 있습니다.</p>
							</div>
                        </div>
                        <div class="newsLetter" id="newsLetter3">
        <div style="width:545px; margin:0 auto; ; font-family:Dotum, 돋움; font-size:12px; line-height:1.5">
	    <h1 style="margin:0"><img src="http://contest.xpressengine.com/opages/img/contest_header_03.jpg" width="545" height="390" alt="XE 공모전 Newsletter Vol. 01"></h1>

        <div style="margin: 15px 0pt; width: 545px;">
            <div style="border: 6px solid rgb(243, 243, 243); padding: 30px 30px 30px 35px; text-align: left; line-height: 1.5;">
        <p style="margin-bottom:30px;">XE 아이디어 모집 이벤트가 종료되었습니다. 수많은 XE 사용자 여러분들이 150개가 넘는 아이디어를 주셨습니다. 수상작은 다음과 같습니다.</p>
        <h3 style="font-size:14px;">Windows 7 Ultimate Edition 수상작 5개</h3>
        <ul>
            <li><a href="http://contest.xpressengine.com/18546612">토털 마이그레이션 툴</a> by <strong>tackeru</strong></li>
            <li><a href="http://contest.xpressengine.com/18538971">홈페이지 동맹기능</a> by <strong>misol</strong></li>
            <li><a href="http://contest.xpressengine.com/18540315">스토리지 분산 및 외부자원 활용</a> by <strong>VL-2536</strong></li>
            <li><a href="http://contest.xpressengine.com/18559851">게시판과 메일, 블로그 연동</a> by <strong>ssd14</strong></li>
            <li><a href="http://contest.xpressengine.com/18563855">동영상 삽입 기능</a> by <strong>sapphire0202</strong></li>
        </ul>
        <h3 style="font-size:14px;">Microsoft Wireless Mouse 3000 수상작 10개</h3>
        <ul>
            <li><a href="http://contest.xpressengine.com/18555173">시스템 메시지 개인화 기능</a> by <strong>누리안</strong></li>
            <li><a href="http://www.wingtech.co.kr/tt/2585">XE 마법사 기능</a> by <strong>zirho</strong></li>
            <li><a href="http://blog.naver.com/preah/120097409003">아이디어 31개</a> by <strong>preah</strong></li>
            <li><a href="http://contest.xpressengine.com/18563196">아이디어 33개</a> by <strong>老姜君(노강군)</strong></li>
            <li><a href="http://contest.xpressengine.com/18540402">아이디어 35개</a> by <strong>하모니</strong></li>
            <li><a href="http://love.4dreams.kr/326972">텍스타일 까보기</a> by <strong>키나</strong></li>
            <li><a href="http://contest.xpressengine.com/18538667">게시판 2차 분류 기능</a> by <strong>hktown</strong></li>
            <li><a href="http://contest.xpressengine.com/18555768">회비 관리 모듈</a> by <strong>Telomere</strong></li>
            <li><a href="http://contest.xpressengine.com/18544608">주소록 모듈</a> by <strong>kozi</strong></li>
            <li><a href="http://contest.xpressengine.com/18546385">갤러리, 음악플레이어, 다국어 지원</a> by <strong>Daniel</strong></li>
        </ul>
        <p style="margin-top:20px;">자세한 내용은 <a href="http://contest.xpressengine.com/18576522">XE 아이디어 모집 이벤트 수상작 발표 페이지</a>를 참고해주시기 바랍니다.</p> 
        <p>XE 공모전 <strong style="color:red; font-size:14px">응모작 제출 기간이 겨우 5일 앞으로</strong> 다가왔습니다.<br />
        접수 기간은 <strong>2010년 1월 4일 월요일 오후 3시부터 10일 일요일 자정까지</strong>입니다.<br />
        열심히 준비해오신 XE 모듈과 스킨이 널리 알려지고 푸짐한 상품도 받을 수 있도록<br />
        마지막까지 힘내주세요. 아직 늦지 않았습니다! 하지만 서두르세요!</p>
        <p>2009년 12월 30일, XE 개발팀 드림</p>
            </div>
        </div>
        
        <p style="border-top:3px solid #000; margin:0; padding:15px 0; font-size:11px; color:#989898; line-height:1.6; text-align:left; letter-spacing:-1px; text-align:center">XE공모전 뉴스레터는 총 5회에 걸쳐, 직접 <a href="http://contest.xpressengine.com/?mid=contestLetter" target="_blank" style="font-weight:bold; color:#474747; text-decoration:none">뉴스레터 구독신청</a>을 하신 분들께만 보내드리고 있습니다.</p>
</div>
                        </div>
                        <div class="newsLetter" id="newsLetter4">
							<div style="width:545px; margin:0 auto; ; font-family:Dotum, 돋움; font-size:12px; line-height:1.5">
											<h1 style="margin:0;"><img src="http://contest.xpressengine.com/opages/img/h1v4.gif" alt="XE 공모전 Newsletter" /></h1>
											<div style="margin: 15px 0pt; width: 545px;">
												<div style="border: 6px solid rgb(243, 243, 243); padding: 30px 30px 30px 35px; text-align: left; line-height: 1.5;">
													<p>안녕하세요, XE 개발팀입니다. 새해  복 많이 받으세요.</p>
													<p>지난 월요일부터 XE 공모전 응모작 접수가 시작되었습니다.<br />
														제출 기한은 <strong style="text-decoration:underline">1월 10일 일요일 자정까지</strong>입니다. </p>
													<ul>
														<li>기존에 진행해오던 프로젝트도 공모전에 응모할 수 있습니다.</li>
														<li>한 사람이 여러 개의 응모작을 제출할 수 있습니다.</li>
														<li>XE 공식사이트 계정으로 로그인 또는 가입 후 접수할 수 있습니다. </li>
													</ul>
													<p>지금껏 열심히 만드신 프로그램과 스킨으로 <br />
														애플 맥북 에어의 주인이 되세요! </p>
													<p><a href="http://contest.xpressengine.com/?mid=contestRegistration" target="_blank"><img src="http://contest.xpressengine.com/opages/img/buttonRegistration.gif" alt="응모작 접수" style="border:0" /></a></p>
													<p>새해에는 더 멋진 XE가 되기 위해 노력하겠습니다. <br />
														감사합니다. </p>
													<p>XE 개발팀 드림</p>
												</div>
											</div>
											<p style="border-top:3px solid #000; margin:0; padding:15px 0; font-size:11px; color:#989898; line-height:1.6; text-align:left; letter-spacing:-1px; text-align:center">XE공모전 뉴스레터는 총 5회에 걸쳐, 직접 <a href="http://contest.xpressengine.com/?mid=contestLetter" target="_blank" style="font-weight:bold; color:#474747; text-decoration:none">뉴스레터 구독신청</a>을 하신 분들께만 보내드리고 있습니다.</p>
							</div>
                        </div>
                        <div class="newsLetter" id="newsLetter5">
                            XE 공모전 뉴스레터 5호를 준비중 입니다.
                        </div>

                    </div>

                </div>
                <!-- /#contest_help -->
