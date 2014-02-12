<?php
    if(!defined("__ZBXE__")) exit();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
  <style>
  body{font:12px/1.5 NanumGothic, 나눔고딕, "Malgun Gothic", "맑은 고딕", Dotum, 돋움;color:#565960;}
  a:hover, a:active, a:focus{text-decoration:underline;}
  a{text-decoration:none;color:#565960}
  em{font-weight:bold}
  .imagecaption{border-bottom:solid 1px}
  .__content{padding:0 10px}
  ul{list-style:none;margin:0;padding:0;}
  ul li{margin-left:25px;margin-top:3px;}
  .chrn li{margin-left:0px;padding-left:18px;background:url(./image/lineSub.gif) no-repeat 5px 4px}
  body{margin-top:0;padding-top:0;background:url(./image/contest_bg.png) repeat-x}
 ul.parn li span{font-weight:bold;font-size:14px;}
  .clickColor{color:#008CFF;text-decoration:underline;font-weight:bold}
  p{padding:0;margin:0}
  .__content p{font-weight:bold;}
  </style>
 
 <script type="text/javascript">
	function show(id){
		jQuery('.__content').hide();
		jQuery('#'+id).show();
	}
	jQuery(function($){
		$('ul.chrn li a').click(function() {
			$('ul.chrn li a').removeClass('clickColor');
			$(this).addClass('clickColor');
		});
	});
  	
 </script>
 	<div style="padding-left:30px;height:33px;padding-top:80px;font-size:17px;color:#ffffff;font-weight:bold"></div>

	<div style="float:left;height:100%;width:17%;padding-top:10px;">
		<ul class="parn">

			<li><span>메인페이지 보기</span>
				<ul class="chrn">
					<li class="chrn"> <a href="#" onclick="show('main_layout')" class="clickColor">메인 레이아웃 디자인</a></li>
				</ul>
			</li>
			<li><span>회원관련 페이지 보기</span>
				<ul class="chrn">
					<li> <a href="#" onclick="show('member')">회원가입 페이지 디자인</a></li>
					<li> <a href="#" onclick="show('login')">로그인 페이지 디자인</a></li>
					<li> <a href="#" onclick="show('info')">회원정보보기 페이지 디자인</a></li>
					<li> <a href="#" onclick="show('message')">쪽지보기 페이지 디자인</a></li>
					<li> <a href="#" onclick="show('password')">비밀번호 변경 페이지 디자인</a></li>
					<li> <a href="#" onclick="show('signout')">회원 탈퇴 페이지 디자인</a></li>
				</ul>
			</li>
			<li><span>게시판관련 페이지 보기</span>
				<ul class="chrn">
					<li><a href="#" onclick="show('list')">게시글 리스트 디자인</a></li>
					<li><a href="#" onclick="show('gallery')">게시글 리스트 갤러리 타입 디자인</a></li>
					<li><a href="#" onclick="show('view')">게시글 보기 디자인</a></li>
					<li><a href="#" onclick="show('write')">게시글 쓰기 디자인</a></li>
					<li><a href="#" onclick="show('comment')">대댓글 쓰기 디자인</a></li>
				</ul>
			</li>
			<li><span>기타</span>
				<ul class="chrn">
					<li><a href="#" onclick="show('error')">오류 메시지 화면 디자인</a></li>
				</ul>
			</li>
		</ul>
		<div style="border-top:solid 1px #565960;border-bottom:solid 1px #565960;margin-bottom:30px;margin-top:15px;">
		<p><span style="color:blue;font-weight:bold">파란색</span> 영역 :  디자인시 선택사항<br/> 
		<p><span style="color:red;font-weight:bold">빨간색</span> 영역 : 구분자<br/>
		<p><span style="color:black;font-weight:bold">검은색</span> 영역 : 디자인 제외 요소</p>
		</div>
	</div>
	<div style="float:right;width:80%;border-left:solid 1px #565960;margin:0 0 10px 0;padding:20px 0 0 10px;">
		<div class="__content" id="main_layout">
			<p>메인 페이지 디자인 주요 사항</p>
			<ol>
				<li> 화면 구성
					<ol>
						<li>반드시 컨텐츠 영역을 포함하되 컨텐츠 영역내용은 새롭게 디자인 하지 않아도 됩니다.</li>
						<li>컨텐츠 영역 표시 단수는 자유롭게 변형 가능합니다.</li>
					</ol>
				</li>
				<li> 메뉴영역 디자인
					<ol>
						<li>메뉴 영역의 위치 및 사이즈의 제한은 없습니다.</li>
						<li>1depth이상의 하위 메뉴를 표현할 수 있어야 합니다.</li>
					</ol>
				</li>
				<li> 컨텐츠 위젯 디자인
					<ol>
						<li>컨텐츠 영역을 새롭게 디자인하고자 하는 경우 "타이틀 영역"에 해당하는 <em>그림 01-03</em>부분을 디자인하시면 됩니다.</li>
					</ol>
				</li>
				<li> 로그인 위젯
					<ol>
						<li>로그인 위젯은 포함하지 않아도 됩니다.</li>
						<li>로그인 위젯을 포함 할 경우 반드시 로그인 실패 메시지 노출 영역 <em>그림 01-04</em> 도 디자인합니다.</li>
						<li>로그인 위젯을 포함 할 경우 반드시 로그인 이후의 화면 <em>그림 01-05</em> 도 디자인합니다.</li>
					</ol>
				</li>
				<li> 사이드 메뉴바
					<ol>
							<li>사이드 메뉴바는 포함하지 않아도 됩니다.</li>
						<li>사이드 메뉴바를 포함 할 경우 </li>
					</ol>
				</li>
			</ol>
			<img src="./image/01-01.png" />
			<img src="./image/01-02.png" />
			<div class="imagecaption">
				<p>그림 01-03</p>
				<img src="./image/01-03.png"/>
			</div>
			<div class="imagecaption">
				<p>그림 01-04</p>
				<img src="./image/01-04.png"/>
			</div>
			<div class="imagecaption">
				<p>그림 01-05</p>
				<img src="./image/01-05.png"/>
			</div>
		</div>
		<div class="__content" id="member">
			<p>회원 가입페이지 디자인 주요 사항</p>
			<ol>
				<li>회원가입 입력 폼구성은 예시안의 내용을 참고하여 디자인 합니다.</li>
				<li>회원가입 실패 메시지 노출 영역도 디자인 합니다.</li>
			</ol>
			<img src="./image/02-01.png" />
		</div>
		<div class="__content" id="login">
			<p>로그인 페이지 디자인 주요 사항</p>
			<ol>
				<li>로그인정보 입력 폼구성은 예시안을 참고하여 디자인 합니다.</li>
				<li>로그인 실패 메시지 노출 영역도 디자인 합니다.</li>
			</ol>
			<img src="./image/02-02.png" />
		</div>
		<div class="__content" id="info">
			<p>회원 정보보기 페이지 디자인 주요 사항</p>
			<ol>
				<li>회원정보 필드 구성은 예시안을 참고하여 디자인 합니다.</li>
				<li>회원관련 카테고리(상단의 탭)는 반드시 디자인하되 탭이 아닌 다른 형식이어도 됩니다.</li>
			</ol>
			<img src="./image/02-03.png" />
		</div>
		<div class="__content" id="message">
			<p>쪽지보기 페이지 디자인 주요 사항</p>
			<ol>
				<li>쪽지함 설정 변경 영역, 내용 확인 영역과, 리스트 영역을 구분하여 디자인 하되 필수로 포함합니다.</li>
				<li>회원관련 카테고리(상단의 탭)는 반드시 디자인하되 탭이 아닌 다른 형식이어도 됩니다.</li>
			</ol>
			<img src="./image/02-04.png" />
		</div>
		<div class="__content" id="password">
			<img src="./image/02-05.png" />
		</div>
		<div class="__content" id="signout">
			<img src="./image/02-06.png" />
		</div>
		<div class="__content" id="list">
			<img src="./image/03-01.png" />
		</div>
		<div class="__content" id="gallery">
			<img src="./image/03-05.png" />
		</div>
		<div class="__content" id="view">
			<p>게시글 보기 디자인 주요 사항</p>
			<ol>
				<li> 화면 구성
					<ol>
						<li>반드시 타이틀과 게시글 내용역역을 포함하되 게시글 영역내용은 새롭게 디자인 하지 않아도 됩니다.</li>
						<li>게시글 내용 필드 구성은 예시안을 참고하여 디자인 합니다.(게시글 등록일, 등록자, 조회수, 댓글 수 등등)</li>
					</ol>
				</li>
				<li> 엮인글, 댓글
					<ol>
						<li>엮인글 영역과 댓글 영역을 구분하되 반드시 디자인에 포함합니다.</li>
						<li>엮인글 표시 내용은 예시안을 참고하여 디자인 합니다.</li>
						<li>댓글과 대댓글을 구분하여 디자인하고, 사용자 프로필 이미지를 추가하여도 무관합니다.</li>
						<li>댓글 입력 에디터는 디자인하지 않고 예시안을 그대로 사용합니다.</li>
					</ol>
				</li>
				<li> 게시글 리스트 영역
					<ol>
						<li>해당영역은 생략 가능합니다.</li>
						<li>생략할 경우 게시글 리스트로 돌아갈 수 있는 "목록" or "돌아가기"버튼을 반드시 포함합니다.</li>
					</ol>
				</li>
			</ol>
			<img src="./image/03-02.png" />
		</div>
		<div class="__content" id="write">
			<img src="./image/03-03.png" />
		</div>
		<div class="__content" id="comment">
			<img src="./image/03-04.png" />
		</div>
		<div class="__content" id="error">
			<img src="./image/04-01.png" />
		</div>
	</div>

	<script type="text/javascript">
	jQuery(function($){
		$('.__content').hide();
		jQuery('#main_layout').show();
	});
	</script>
